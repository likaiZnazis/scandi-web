// ProductDetail.js
import React, { Component } from 'react';

class ProductDetail extends Component {
  render() {
    const { product } = this.props;

    return (
      <div>
        <h1>{product.name}</h1>
        <img src={product.gallery[0]} alt={`${product.name} image`} />
        <p>{product.in_stock ? 'In Stock' : 'Out of Stock'}</p>
        {product.prod_prices.map((price, index) => (
          <p key={index}>
            {price.currency.symbol}{price.amount}
          </p>
        ))}
      </div>
    );
  }
}

export default ProductDetail;
