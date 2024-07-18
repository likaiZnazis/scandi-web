// App.js
import React, { Component } from 'react';

//
import Header from './Components/Header';
import DisplayProducts from './Components/DisplayProducts';


class App extends Component {
  constructor(props) {
    super(props);
    this.state = {
      selectedCategory: 'clothes',
    };
  }

  setSelectedCategory = (category) => {
    this.setState({ selectedCategory: category });
  };

  render() {
    return (
      <div id="root">
        <Header 
        onSelectCategory={this.setSelectedCategory} 
        activeCategory={this.state.selectedCategory}
        />
        <DisplayProducts categoryName={this.state.selectedCategory} />
      </div>
    );
  }
}

export default App;
