import React, { Component } from 'react';
import ActiveAttribute from './ActiveAttribute';

//Style
import '../assets/css/productDetail.css';



class ProductDetail extends Component {
  //Need to create a new component ProductAttributes.

  constructor(props) {
    super(props);
    this.state = {
      selectedAttributes: {},
    };
  }

  handleAttributeSelect = (attributeId, value) => {
    this.setState((prevState) => ({
      selectedAttributes: {
        ...prevState.selectedAttributes,
        [attributeId]: value,
      },
    }));
  };

  parseDescription = (description) => (description.replaceAll("<p>", "").replaceAll("</p>", ""))

  render() {
    const { product } = this.props;

    return (  
      <div className='grid-product-detail'>
        <div className=''>

        </div>
        <div className='grid-carusel'>
            {/* <img src={product.gallery[0]} alt={`${product.name} image`} /> */}
        </div>
        <div>
            <h1 className='product-detail-name'>{product.name}</h1>
          {product.attributes.map((attribute) => (
            <ActiveAttribute
              key={attribute.id}
              attribute={attribute}
              onAttributeSelect={(value) => this.handleAttributeSelect(attribute.id, value)}
            />
          ))}
            <p className='price-label'>PRICE:</p>
            {product.prod_prices.map((price, index) => (
            <p className='product-detail-price' key={index}>
                <br/>{price.currency.symbol}{price.amount}
            </p>
            ))}
            <button className='cart-button'>ADD TO CART</button>
            <p className='product-detail-description'>
                {this.parseDescription(product.description)}
            </p>
        </div>
      </div>
    );
  }
}

export default ProductDetail;
