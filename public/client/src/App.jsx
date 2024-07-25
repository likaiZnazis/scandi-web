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

  addToCart = (product, selectedAttributes) => {
    this.setState((prevState) => ({
      cartItems: [...prevState.cartItems, { ...product, selectedAttributes, quantity: 1 }],
    }));
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
        />
      </div>
    );
  }
}

export default App;
