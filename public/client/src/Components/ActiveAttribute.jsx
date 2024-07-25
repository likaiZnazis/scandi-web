import React, { Component } from 'react';

//style
import '../assets/css/activeAttribute.css';

class ActiveAttribute extends Component {
  constructor(props) {
    super(props);
    this.state = {
      selectedAttribute: null,
    };
  }

  handleAttributeSelect = (attribute) => {
    this.setState({ selectedAttribute: attribute });
    this.props.onAttributeSelect(attribute);
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

  render() {
    const { attribute } = this.props;
    const { selectedAttribute } = this.state;

    return (
      <div className= 'attribute'>
        <p className="attribute-id">{`${attribute.id.toUpperCase()}:`}</p>
        <div className={`${attribute.type === 'swatch' 
      ? 'swatch-attribute' : 'text-attribute'}`}
      data-testid={`product-attribute-${this.toKebabCase(attribute.id)}`}>
          {attribute.items.map((item) => {
            const isActive = selectedAttribute === item.value;
            return (
              <div
                key={item.id}
                className={`attribute-item 
                ${isActive ? 'active' : ''} 
                ${attribute.type === 'swatch' ? "swatch-item" : "text-item"}`}
                style={attribute.type === 'swatch' ? { backgroundColor: this.setHexColor(item.value) } : {}}
                title={item.displayValue}
                onClick={() => this.handleAttributeSelect(item.value)}
              >
                {attribute.type === 'text' && item.displayValue}
              </div>
            );
          })}
        </div>
      </div>
    );
  }
}

export default ActiveAttribute;
