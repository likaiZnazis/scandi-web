// App.js
import React, { Component } from 'react';
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
  }

  componentWillUnmount() {
    window.removeEventListener('popstate', this.handlePopState);
  }

  handlePopState = (event) => {
    if (event.state && event.state.selectedProduct) {
      this.setState({ selectedProduct: event.state.selectedProduct });
    } else {
      this.setState({ selectedProduct: null });
    }
  };

  setSelectedCategory = (category) => {
    this.setState({ selectedCategory: category, selectedProduct: null });
    window.history.pushState({}, '', window.location.pathname);
  };

  selectProduct = (product) => {
    this.setState({ selectedProduct: product });
    window.history.pushState({ selectedProduct: product }, '', `?product=${product.product_id}`);
  };

  //In strict mode it adds the item twice. 1 -> 3
  addToCart = (product, selectedAttributes) => {
    this.setState((prevState) => {
      //Check if item with the same attrbiutes is present in the cart
      const existingProductIndex = prevState.cartItems.findIndex(
        (item) =>
          item.product_id === product.product_id &&
          JSON.stringify(item.selectedAttributes) === JSON.stringify(selectedAttributes)
      );

      //If product exists with those attr add it to quantity
      //else add new product
      console.log(existingProductIndex);
      if (existingProductIndex !== -1) {
        const updatedCartItems = [...prevState.cartItems];
        updatedCartItems[existingProductIndex].quantity += 1;
        return { cartItems: updatedCartItems };
      } else {
        return {
          cartItems: [
            ...prevState.cartItems,
            { ...product, selectedAttributes, quantity: 1 },
          ],
        };
      }
    });
  };

  updateQuantity = (productId, change) => {
    this.setState((prevState) => {
      const updatedCartItems = prevState.cartItems
        .map((item) => {
          if (
            item.product_id === productId
          ) {
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

      return { cartItems: updatedCartItems };
    });
  };


  toggleCartVisibility = () => {
    this.setState((prevState) => ({
      isCartVisible: !prevState.isCartVisible,
    }));
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
          <ProductDetail product={selectedProduct}  addToCart = {this.addToCart}/>
        ) : (
          <DisplayProducts 
            categoryName={selectedCategory} 
            onSelectProduct={this.selectProduct} 
          />
        )}
        <Cart 
          cartItems={cartItems}
          visibility={isCartVisible} 
          toggleCartVisibility={this.toggleCartVisibility}
          updateQuantity={this.updateQuantity}
        />
      </div>
    );
  }
}

export default App;
