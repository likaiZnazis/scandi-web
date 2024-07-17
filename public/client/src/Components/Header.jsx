import React, {Component} from 'react';

//Assets
import Logo from '../assets/images/Logo.svg';
import Cart from '../assets/images/Cart.svg';

//Style
import '../assets/css/header.css'

class Header extends Component
{
  render(){
    return(
    <header>
        <nav>
            <ul>
                <li>Clothes</li>
                <li>Tech</li>
            </ul>
        </nav>

        <img src={Logo} alt="Logo" />

        <img src={Cart} alt="Cart" />

    </header>
    );
  }
}

export default Header;