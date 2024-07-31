import { ApolloClient, InMemoryCache } from '@apollo/client';

const client = new ApolloClient({
    uri: 'http://localhost:8000/category/{category_name}',
    cache: new InMemoryCache(),
  });

export default client;
