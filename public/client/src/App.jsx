// App.js
import React, { Component } from 'react';
import Header from './Components/Header';
import DisplayProducts from './Components/DisplayProducts';
import ProductDetail from './Components/ProductDetail';

class App extends Component {
  constructor(props) {
    super(props);
    this.state = {
      selectedCategory: 'clothes',
      selectedProduct: null,
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

  render() {
    const { selectedCategory, selectedProduct } = this.state;

    return (
      <div id="root">
        <Header 
          onSelectCategory={this.setSelectedCategory} 
          activeCategory={selectedCategory}
        />
        {selectedProduct ? (
          <ProductDetail product={selectedProduct} />
        ) : (
          <DisplayProducts 
            categoryName={selectedCategory} 
            onSelectProduct={this.selectProduct} 
          />
        )}
      </div>
    );
  }
}

export default App;
