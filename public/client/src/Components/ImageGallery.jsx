import React, { Component, createRef } from 'react';
import RightArrow from '../assets/images/RightArrow.svg';
import LeftArrow from '../assets/images/LeftArrow.svg';
import '../assets/css/mainImage.css';

class ImageGallery extends Component {
  constructor(props) {
    super(props);
    this.carouselRef = createRef();
  }

  scrollToImage = (index) => {
    const { current } = this.carouselRef;
    if (current) {
      const width = current.clientWidth;
      current.scrollTo({
        left: width * index,
        behavior: 'smooth',
      });
    }
  };

  handleNextImage = () => {
    const { selectedImageIndex, gallery, handleNextImage } = this.props;
    handleNextImage();
    this.scrollToImage((selectedImageIndex + 1) % gallery.length);
  };

  handlePreviousImage = () => {
    const { selectedImageIndex, gallery, handlePreviousImage } = this.props;
    handlePreviousImage();
    this.scrollToImage((selectedImageIndex - 1 + gallery.length) % gallery.length);
  };

  componentDidUpdate(prevProps) {
    if (prevProps.selectedImageIndex !== this.props.selectedImageIndex) {
      this.scrollToImage(this.props.selectedImageIndex);
    }
  }

  render() {
    const { gallery, selectedImageIndex, handleImageSelect } = this.props;

    return (
      <div className="image-gallery">
        <div className="main-image">
          <button className="carousel-button prev" onClick={this.handlePreviousImage}>
            <img src={LeftArrow} alt="Left arrow" />
          </button>
          <div className="carousel-container" ref={this.carouselRef}>
            {gallery.map((image, index) => (
              <div className="carousel-item" key={index}>
                <img className="display-image" src={image} alt={`Product ${index}`} />
              </div>
            ))}
          </div>
          <button className="carousel-button next" onClick={this.handleNextImage}>
            <img src={RightArrow} alt="Right arrow" />
          </button>
        </div>
        <div className="thumbnails">
          {gallery.map((image, index) => (
            <img
              key={index}
              src={image}
              alt={`Thumbnail ${index}`}
              className={selectedImageIndex === index ? 'selected' : ''}
              onClick={() => handleImageSelect(index)}
            />
          ))}
        </div>
      </div>
    );
  }
}

export default ImageGallery;
