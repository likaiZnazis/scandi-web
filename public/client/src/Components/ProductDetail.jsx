import React, { Component } from 'react';
import ActiveAttribute from './ActiveAttribute';
import ImageGallery from './ImageGallery';
import '../assets/css/productDetail.css';

class ProductDetail extends Component {
  constructor(props) {
    super(props);
    this.state = {
      selectedAttributes: {},
      selectedImageIndex: 0,
      isDescriptionExpanded: false,
      isDescriptionLong: false,
    };
  }

  componentDidMount() {
    this.checkDescriptionLength();
  }

  handleAttributeSelect = (attributeId, value) => {
    this.setState((prevState) => ({
      selectedAttributes: {
        ...prevState.selectedAttributes,
        [attributeId]: value,
      },
    }));
  };

  handleNextImage = () => {
    this.setState((prevState) => ({
      selectedImageIndex: (prevState.selectedImageIndex + 1) % this.props.product.gallery.length,
    }));
  };

  handlePreviousImage = () => {
    this.setState((prevState) => ({
      selectedImageIndex: (prevState.selectedImageIndex - 1 + this.props.product.gallery.length) % this.props.product.gallery.length,
    }));
  };

  handleImageSelect = (index) => {
    this.setState({ selectedImageIndex: index });
  };

  parseDescription = (description) => description.replace(/<[^>]*>?/gm, '');

  allAttributesSelected = () => {
    const { selectedAttributes } = this.state;
    const { product } = this.props;
    return product.attributes.every(attribute => selectedAttributes[attribute.id]);
  };

  handleAddToCart = () => {
    const { product, addToCart } = this.props;
    const { selectedAttributes } = this.state;
    if (this.allAttributesSelected() && product.in_stock) {
      addToCart(product, selectedAttributes);
    }
  };

  checkDescriptionLength = () => {
    const productDescription = this.parseDescription(this.props.product.description);
    if (productDescription.length > 250) {
      this.setState({ isDescriptionLong: true });
    }
  };

  toggleDescription = () => {
    this.setState((prevState) => ({
      isDescriptionExpanded: !prevState.isDescriptionExpanded,
    }));
  };

  render() {
    const { product } = this.props;
    const { selectedImageIndex, isDescriptionExpanded, isDescriptionLong } = this.state;

    return (
      <div className="grid-product-detail">
        <div className="grid-carousel">
          <ImageGallery
            gallery={product.gallery}
            selectedImageIndex={selectedImageIndex}
            handlePreviousImage={this.handlePreviousImage}
            handleNextImage={this.handleNextImage}
            handleImageSelect={this.handleImageSelect}
          />
        </div>
        <div className="product-detail-info">
          <h1 className="product-detail-name">{product.name}</h1>
          {product.attributes.map((attribute) => (
            <ActiveAttribute
              key={attribute.id}
              attribute={attribute}
              onAttributeSelect={(value) => this.handleAttributeSelect(attribute.id, value)}
            />
          ))}
          <p className="price-label">PRICE:</p>
          {product.prod_prices.map((price, index) => (
            <p className="product-detail-price" key={index}>
              <br />
              {price.currency.symbol}
              {price.amount}
            </p>
          ))}
          <button
            data-testid='add-to-cart'
            className={`cart-button ${this.allAttributesSelected() && product.in_stock ? '' : 'disabled'}`}
            onClick={this.handleAddToCart}
            disabled = {this.allAttributesSelected() === false || product.in_stock === false}
          >
            ADD TO CART
          </button>
          <p
            className={`product-detail-description ${isDescriptionExpanded ? 'expanded' : ''}`}
            data-testid='product-description'
          >
            {this.parseDescription(product.description)}
          </p>
          {isDescriptionLong && (
            <button
              className="toggle-description-button"
              onClick={this.toggleDescription}
            >
              {isDescriptionExpanded ? 'Read less' : 'Read more'}
            </button>
          )}
        </div>
      </div>
    );
  }
}

export default ProductDetail;
