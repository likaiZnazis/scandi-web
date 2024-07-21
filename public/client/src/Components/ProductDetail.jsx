import React, { Component } from 'react';

//Style
import '../assets/css/productDetail.css';



class ProductDetail extends Component {
    parseDescription = (description) => (description.replace("<p>", "").replace("</p>", ""))
    renderAttribute = (attribute) => {
        const setHexColor = (color) => color.includes("#") ?
         color : `#${color}`; 
        switch (attribute.type) {
          case 'swatch':
            return (
              <div className='swatch-attribute' key={attribute.id}>
                {attribute.items.map((item) => (
                    
                  <div 
                    key={item.id} 
                    className='swatch-item' 
                    style={{ backgroundColor: setHexColor(item.value) }}
                    title={item.displayValue}
                  />
                ))}
              </div>
            );
          case 'text':
            return (
              <div className='text-attribute' key={attribute.id}>
                {attribute.items.map((item) => (
                  <div key={item.id} className='text-item'>
                    {item.displayValue}
                  </div>
                ))}
              </div>
            );
          default:
            return null;
        }
      };
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
                <div className='attributes' key={attribute.id}>
                    <p className='attribute-id' >
                        {`${attribute.id.toUpperCase()}:`}
                    </p>
                    {this.renderAttribute(attribute)}
                </div>
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
