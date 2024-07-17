import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'
import { ApolloClient, InMemoryCache, ApolloProvider, gql } from '@apollo/client';

const client = new ApolloClient({
  uri: 'http://localhost:8000/category/clothes',
  cache: new InMemoryCache(),
});

client
  .query({
    query: gql`
      {
        category(category_name: "clothes") {
          category_id
          category_name
          products {
            product_id
          }
        }
      }
    `
  })
  .then((result) => console.log(result))
  .catch((error) => console.error('Error fetching data:', error));

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>,
)
