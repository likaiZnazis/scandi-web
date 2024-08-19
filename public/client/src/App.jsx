import React, { Component } from 'react';
import { gql } from '@apollo/client';
import client from './Client/ApolloClientSetup';
import Header from './Components/Header';
import DisplayProducts from './Components/DisplayProducts';
import ProductDetail from './Components/ProductDetail';
import Cart from './Components/Cart';

class App extends Component {
  constructor(props) {
    super(props);
    this.state = {
      selectedCategory: 'clothes',
      selectedProduct: null,
      cartItems: [],
      isCartVisible: false,
    };
  }

  componentDidMount() {
    window.addEventListener('popstate', this.handlePopState);
    this.loadCartItemsFromLocalStorage();
    this.updateCategoryFromUrl();
  }

  componentWillUnmount() {
    window.removeEventListener('popstate', this.handlePopState);
  }

  handlePopState = (event) => {
    this.updateCategoryFromUrl();
    if (event.state && event.state.selectedProduct) {
      this.setState({ selectedProduct: event.state.selectedProduct });
    } else {
      this.setState({ selectedProduct: null });
    }
  };

  updateCategoryFromUrl = () => {
    const path = window.location.pathname;
    const category = path === '/' ? 'clothes' : path.substring(1);
    this.setState({ selectedCategory: category });
  };

  setSelectedCategory = (category) => {
    this.setState({ selectedCategory: category, selectedProduct: null });
    window.history.pushState({}, '', `/${category}`);
  };

  selectProduct = (product) => {
    this.setState({ selectedProduct: product });
    window.history.pushState({ selectedProduct: product }, '', `?product=${product.product_id}`);
  };

  // In strict mode it adds the item twice. 1 -> 3
  addToCart = (product, selectedAttributes) => {
    this.setState((prevState) => {
      const existingProductIndex = prevState.cartItems.findIndex(
        (item) =>
          item.product_id === product.product_id &&
          JSON.stringify(item.selectedAttributes) === JSON.stringify(selectedAttributes)
      );

      let updatedCartItems;
      if (existingProductIndex !== -1) {
        updatedCartItems = [...prevState.cartItems];
        updatedCartItems[existingProductIndex].quantity += 1;
      } else {
        updatedCartItems = [
          ...prevState.cartItems,
          { ...product, selectedAttributes, quantity: 1 },
        ];
      }

      this.saveCartItemsToLocalStorage(updatedCartItems);
      this.state.isCartVisible = true;
      return { cartItems: updatedCartItems };
    });
  };

  updateQuantity = (productId, change) => {
    this.setState((prevState) => {
      const updatedCartItems = prevState.cartItems
        .map((item) => {
          if (item.product_id === productId) {
            const newQuantity = item.quantity + change;
            if (newQuantity > 0) {
              return { ...item, quantity: newQuantity };
            } else {
              return null;
            }
          }
          return item;
        })
        .filter((item) => item !== null);

      this.saveCartItemsToLocalStorage(updatedCartItems);
      return { cartItems: updatedCartItems };
    });
  };

  placeOrder = async (orderData) => {
    const PLACE_ORDER_MUTATION = gql`
      mutation CreateOrder($input: OrderInput!) {
        createOrder(input: $input) {
          order_id
          total_price
          items {
            order_item_id
            product_id
            quantity
            selectedAttributes
          }
        }
      }
    `;
    try {
      const { data } = await client.mutate({
        mutation: PLACE_ORDER_MUTATION,
        variables: { input: orderData },
      });

      console.log('Order placed:', data.createOrder);
      // reset
      this.setState({ cartItems: [] });
      this.saveCartItemsToLocalStorage([]);
    } catch (error) {
      console.error('Error placing order:', error);
    }
  };

  toggleCartVisibility = () => {
    this.setState((prevState) => ({
      isCartVisible: !prevState.isCartVisible,
    }));
  };

  saveCartItemsToLocalStorage = (cartItems) => {
    localStorage.setItem('cartItems', JSON.stringify(cartItems));
  };

  loadCartItemsFromLocalStorage = () => {
    const cartItems = localStorage.getItem('cartItems');
    if (cartItems) {
      this.setState({ cartItems: JSON.parse(cartItems) });
    }
  };

  render() {
    const { selectedCategory, selectedProduct, cartItems, isCartVisible } = this.state;

    return (
      <div id="root">
        <Header 
          onSelectCategory={this.setSelectedCategory} 
          activeCategory={selectedCategory}
          cartItems={cartItems}
          toggleCartVisibility={this.toggleCartVisibility}
        />
        {selectedProduct ? (
          <ProductDetail product={selectedProduct} addToCart={this.addToCart} />
        ) : (
          <DisplayProducts 
            categoryName={selectedCategory} 
            onSelectProduct={this.selectProduct}
            addToCart={this.addToCart}
          />
        )}
        <Cart 
          cartItems={cartItems}
          visibility={isCartVisible} 
          toggleCartVisibility={this.toggleCartVisibility}
          updateQuantity={this.updateQuantity}
          placeOrder={this.placeOrder}
        />
      </div>
    );
  }
}

export default App;
