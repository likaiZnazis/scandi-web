import React, { Component } from "react";
import '../assets/css/cart.css';

class Cart extends Component {

  handleQuantityChange = (productId, change) => {
    this.props.updateQuantity(productId, change);
  };

  calculateTotalPrice = () => {
    return this.props.cartItems.reduce((total, item) => {
      const itemPrice = item.prod_prices.find(price => price.currency.symbol === this.getCurrencySymbol());
      if (itemPrice) {
        return total + itemPrice.amount * item.quantity;
      }
      return total;
    }, 0).toFixed(2);
  };

  getCurrencySymbol = () => {
    const { cartItems } = this.props;
    if (cartItems.length > 0) {
      const itemPrice = cartItems[0].prod_prices.find(price => price.currency.symbol);
      if (itemPrice) {
        return itemPrice.currency.symbol;
      }
    }
    return "$";
  };

  setHexColor = (color) => color.includes("#") ? color : `#${color}`;

  renderAttributes = (attributes, selectedAttributes) => {
    return attributes.map((attribute, index) => (
      <div key={index} className='attribute'>
        <p className="attribute-id">{`${attribute.id.toUpperCase()}:`}</p>
        <div className={`cart-${attribute.type === 'swatch' ? 'swatch-attribute' : 'text-attribute'}`}>
          {attribute.items.map(item => {
            const isActive = selectedAttributes[attribute.id] === item.value;
            return (
              <div
                key={item.id}
                className={`cart-attribute-item 
                ${isActive ? 'active' : ''} 
                cart-${attribute.type === 'swatch' ? "swatch-item" : "text-item"}`}
                style={attribute.type === 'swatch' ? { backgroundColor: this.setHexColor(item.value) } : {}}
                title={item.displayValue}
              >
                {attribute.type === 'text' && item.displayValue}
              </div>
            );
          })}
        </div>
      </div>
    ));
  };

  render() {
    const { cartItems, visibility, toggleCartVisibility } = this.props;

    return (
      <>
        {visibility && <div className="overlay" onClick={toggleCartVisibility}></div>}
        <div className="cart-modal" style={{ display: visibility ? "block" : "none" }}>
          <div className="cart">
            <div className="cart-header">
              <span className="cart-title">My Bag</span>
              <span className="cart-comma">, </span>
              <span className="cart-item-count">
                {cartItems.length === 0
                  ? ''
                  : `${cartItems.length} ${cartItems.length > 1 ? 'items' : 'item'}`}
              </span>
            </div>
            <div className="cart-product">
              {cartItems.map((product, index) => (
                <div key={index} className="cart-product-row">
                  <div className="cart-product-detail">
                    <h4 className="cart-product-title">{product.name}</h4>
                    <div className="cart-product-price">
                      {product.prod_prices.map((price, idx) => (
                        <p className="cart-product-detail-price" key={idx}>
                          {price.currency.symbol}{price.amount}
                        </p>
                      ))}
                    </div>
                    {this.renderAttributes(product.attributes, product.selectedAttributes)}
                  </div>
                  <div className="cart-product-quantity">
                    <button
                      className="cart-product-button increase"
                      onClick={() => this.handleQuantityChange(product.product_id, 1)}
                    >
                      +
                    </button>
                    <span>{product.quantity}</span>
                    <button
                      className="cart-product-button decrease"
                      onClick={() => this.handleQuantityChange(product.product_id, -1)}
                    >
                      -
                    </button>
                  </div>
                  <div className="cart-product-image">
                    <img src={product.gallery[0]} alt={product.name} />
                  </div>
                </div>
              ))}
            </div>
            <div className="cart-price">
              <p className="cart-total">Total</p>
              <p className="cart-total-price">{this.getCurrencySymbol()}{this.calculateTotalPrice()}</p>
            </div>
            <button className="cart-place-order">PLACE ORDER</button>
          </div>
        </div>
      </>
    );
  }
}

export default Cart;
