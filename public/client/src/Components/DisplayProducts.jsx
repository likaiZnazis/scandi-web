import React, { Component } from 'react';
import { gql } from '@apollo/client';
import { Query } from '@apollo/client/react/components';
import '../assets/css/displayProducts.css';
import cartIcon from '../assets/images/Product-cart-overlay.svg';

const GET_PRODUCTS = gql`
  query GetProducts($categoryName: String!) {
    category(category_name: $categoryName) {
      products {
        product_id
        name
        gallery
        in_stock
        description
        attributes {
          attribute_id
          id 
          items {
            item_id
            displayValue
            value
            id }
          name
          type } 
        prod_prices {
          amount
          currency {
            symbol
          }
        }
      }
    }
  }
`;

class DisplayProducts extends Component {
  capitalizeFirstLetter = (string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  //Need to return the first 
  selectFirstAttribute = (product) =>{
    const arrayAttributes = product.attributes.map((attribute) => [attribute.id, attribute.items[0].value]);
    return Object.fromEntries(arrayAttributes);
  }

  handleCartClick = (product) => {
    this.props.addToCart(product,this.selectFirstAttribute(product));
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

  render() {
    const { categoryName, onSelectProduct } = this.props;
    const capitalizedCategoryName = this.capitalizeFirstLetter(categoryName);

    return (
      <Query query={GET_PRODUCTS} variables={{ categoryName }}>
        {({ loading, error, data }) => {
          if (loading) return <p>Loading...</p>;
          if (error) return <p>Error: {error.message}</p>;

          return (
            <div>
              <h1 className='category-name'>{capitalizedCategoryName}</h1>
              <div className='grid-container'>
                {data.category.products.map((product) => (
                  <div
                    className={`grid-product ${!product.in_stock ? 'product-out-of-stock' : ''}`}
                    key={product.product_id}
                    data-testid={`product-${this.toKebabCase(product.name)}`}
                    onClick={() => onSelectProduct(product)}
                  >
                    {product.gallery.length > 0 && (
                      <img
                        className="product-image"
                        src={product.gallery[0]}
                        alt={`${product.name} image`}
                      />
                    )}
                    {!product.in_stock && (
                      <p className='out-of-stock'>OUT OF STOCK</p>
                    )}
                    <div className='product-details'>
                      <p className='product-name'>{product.name}</p>
                      {product.prod_prices.map((price, index) => (
                        <p className='product-price' key={index}>
                          {price.currency.symbol}{price.amount}
                        </p>
                      ))}
                    </div>
                    <img
                      className='cart-icon'
                      src={cartIcon}
                      alt='Add to cart'
                      onClick={(e) => {
                        e.stopPropagation(); // Prevent the click event from bubbling up to the parent div
                        this.handleCartClick(product);
                      }}
                    />
                  </div>
                ))}
              </div>
            </div>
          );
        }}
      </Query>
    );
  }
}

export default DisplayProducts;
