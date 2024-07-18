import React, { Component } from 'react';
import { Query } from '@apollo/client/react/components';
import { gql } from '@apollo/client';

//style
import '../assets/css/displayProducts.css';

const GET_PRODUCTS = gql`
  query GetProducts($categoryName: String!) {
    category(category_name: $categoryName) {
      category_id
      category_name
      products {
        product_id
      }
    }
  }
`;

class DisplayProducts extends Component {
  capitalizeFirstLetter = (string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }
  render() {

    const { categoryName } = this.props;
    const capitalizedCategoryName = this.capitalizeFirstLetter(categoryName);

    return (
      <Query query={GET_PRODUCTS} variables={{ categoryName }}>
        {({ loading, error, data }) => {
          if (loading) return <p>Loading...</p>;
          if (error) return <p>Error: {error.message}</p>;
          return (
            
            <div>
              <h1>{capitalizedCategoryName}</h1>
              <div className='gird-container'>
                {data.category.products.map((product) => (
                  <div className='grid-product' key={product.product_id}>{product.product_id}</div>
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
