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

  //rewrite
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

  toKebabCase = (str) => {
    return str
      .toLowerCase()
      .replace(/\s+/g, '-')
      .replace(/[^\w\-]+/g, '')
      .replace(/\-\-+/g, '-')
      .replace(/^-+/, '')
      .replace(/-+$/, '');
  }

  setHexColor = (color) => color.includes("#") ? color : `#${color}`;

  renderAttributes = (attributes, selectedAttributes) => {
    return attributes.map((attribute, index) => (
      <div key={index} className='attribute' >
        <p className="attribute-id">{`${attribute.id.toUpperCase()}:`}</p>
        <div className={`cart-${attribute.type === 'swatch' ? 'swatch-attribute' : 'text-attribute'}`}
        data-testid={`cart-item-attribute-${this.toKebabCase(attribute.id)}`}>
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
                data-testid={`cart-item-attribute-${this.toKebabCase(attribute.id)}-${item.id}-${isActive ? 'selected' : ''}`}
              >
                {attribute.type === 'text' && item.displayValue}
              </div>
            );
          })}
        </div>
      </div>
    ));
  };

  getTotalQuantity = () => {
    return this.props.cartItems.reduce((acc, prod) => acc + prod.quantity , 0);
  }

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
              <span className="cart-item-count" data-testid='cart-total'>
                {`${this.getTotalQuantity() }`}
              </span>
              <span className="cart-item-count-sufix">
              {`${cartItems.length > 1 ? 'items' : 'item'}`}
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
                      data-testid='cart-item-amount-increase'
                    >
                      +
                    </button>
                    <span data-testid='cart-item-amount'>
                      {product.quantity}
                    </span>
                    <button
                      className="cart-product-button decrease"
                      onClick={() => this.handleQuantityChange(product.product_id, -1)}
                      data-testid='cart-item-amount-decrease'
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
            <button className= {`cart-place-order ${this.getTotalQuantity() < 1 ? "disabled" : "enabled"}`}
            // onClick={this.handlePlaceOrder}
            >
              PLACE ORDER
            </button>
          </div>
        </div>
      </>
    );
  }
}

export default Cart;
