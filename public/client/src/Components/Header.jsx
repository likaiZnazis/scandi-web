import React, { Component } from 'react';

//Images
import Logo from '../assets/images/Logo.svg';
import Cart from '../assets/images/Cart.svg';

//Style
import '../assets/css/header.css';

class Header extends Component {
    render() {
      const { onSelectCategory, activeCategory, toggleCartVisibility, cartItems } = this.props;
  
      return (
        <header>
          <nav>
            <ul>
              <li
                onClick={() => onSelectCategory('clothes')}
              >
                <a
                className={activeCategory === 'clothes' ? 'active' : ''}
                data-testid={activeCategory === 'clothes' ? 'active-category-link' : 'category-link'}
                href="/clothes">Clothes</a>
              </li>
              <li
                onClick={() => onSelectCategory('tech')}
              >
                <a
                className={activeCategory === 'tech' ? 'active' : ''} 
                data-testid={activeCategory === 'tech' ? 'active-category-link' : 'category-link'}
                href="/tech">Tech</a>
              </li>
              <li
                onClick={() => onSelectCategory('all')}
              >
                <a data-testid={activeCategory === 'all' ? 'active-category-link' : 'category-link'}
                href="/all"
                className={activeCategory === 'all' ? 'active' : ''}>All</a>
              </li>
            </ul>
          </nav>
          <img src={Logo} alt="Logo" />
          <div className="cart-button-wrapper">
            <img data-testid='cart-btn' onClick={toggleCartVisibility} src={Cart} alt="Cart" className='cart'/>
            {cartItems.length > 0 && (
              <div className="item-count-bubble"
              onClick={toggleCartVisibility}>{cartItems.length}</div>
            )}
          </div>
        </header>
      );
    }
  }
  
  export default Header;
