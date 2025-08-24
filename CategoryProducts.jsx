import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { Link } from 'react-router-dom';

const CategoryProducts = ({ categoryId, categoryName }) => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (categoryId) {
      fetchProductsByCategory(categoryId);
    }
  }, [categoryId]);

  const fetchProductsByCategory = async (categoryId) => {
    setLoading(true);
    setError(null);
    try {
      const response = await axios.get(`http://localhost:8000/api/categories/${categoryId}/products`);
      setProducts(response.data);
    } catch (err) {
      setError('Failed to fetch products for this category');
      console.error('Error fetching products:', err);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center py-8">
        <p className="text-red-600 mb-4">{error}</p>
        <button 
          onClick={() => fetchProductsByCategory(categoryId)}
          className="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"
        >
          Try Again
        </button>
      </div>
    );
  }

  if (!categoryId) {
    return (
      <div className="text-center py-8">
        <p className="text-gray-500">Please select a category to view products</p>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto px-4 py-8">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900 mb-2">
          {categoryName || 'Category'} Products
        </h1>
        <p className="text-gray-600">
          {products.length} product{products.length !== 1 ? 's' : ''} found
        </p>
      </div>

      {products.length === 0 ? (
        <div className="text-center py-12">
          <div className="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center">
            <svg className="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
          </div>
          <h3 className="text-lg font-medium text-gray-900 mb-2">No products found</h3>
          <p className="text-gray-500 mb-6">This category doesn't have any products yet.</p>
          <Link 
            to="/"
            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
          >
            Browse All Products
          </Link>
        </div>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          {products.map((product) => (
            <div key={product.id} className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
              <div className="aspect-w-1 aspect-h-1 w-full">
                {product.image ? (
                  <img
                    src={`/storage/${product.image}`}
                    alt={product.name}
                    className="w-full h-48 object-cover"
                  />
                ) : (
                  <div className="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <svg className="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                )}
              </div>
              
              <div className="p-4">
                <h3 className="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                  {product.name}
                </h3>
                
                {product.description && (
                  <p className="text-gray-600 text-sm mb-3 line-clamp-2">
                    {product.description}
                  </p>
                )}
                
                <div className="flex items-center justify-between">
                  <span className="text-2xl font-bold text-indigo-600">
                    ${product.price}
                  </span>
                  
                  {product.stock > 0 ? (
                    <span className="text-sm text-green-600 font-medium">
                      In Stock ({product.stock})
                    </span>
                  ) : (
                    <span className="text-sm text-red-600 font-medium">
                      Out of Stock
                    </span>
                  )}
                </div>
                
                <div className="mt-4 flex space-x-2">
                  <Link
                    to={`/product/${product.id}`}
                    className="flex-1 bg-indigo-600 text-white text-center py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors duration-200"
                  >
                    View Details
                  </Link>
                  
                  {product.stock > 0 && (
                    <button
                      className="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors duration-200"
                      onClick={() => addToCart(product.id)}
                    >
                      Add to Cart
                    </button>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default CategoryProducts;
