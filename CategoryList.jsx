import React, { useState, useEffect } from 'react';
import axios from 'axios';

const CategoryList = ({ onCategorySelect, selectedCategoryId }) => {
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchCategories();
  }, []);

  const fetchCategories = async () => {
    try {
      const response = await axios.get('http://localhost:8000/api/categories');
      setCategories(response.data);
    } catch (err) {
      setError('Failed to fetch categories');
      console.error('Error fetching categories:', err);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center h-32">
        <div className="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-indigo-500"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center py-4">
        <p className="text-red-600 mb-2">{error}</p>
        <button 
          onClick={fetchCategories}
          className="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700"
        >
          Try Again
        </button>
      </div>
    );
  }

  return (
    <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
      <h3 className="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
      
      <div className="space-y-2">
        {categories.map((category) => (
          <button
            key={category.id}
            onClick={() => onCategorySelect(category.id, category.name)}
            className={`w-full text-left px-3 py-2 rounded-md transition-colors duration-200 ${
              selectedCategoryId === category.id
                ? 'bg-indigo-100 text-indigo-700 border border-indigo-200'
                : 'hover:bg-gray-50 text-gray-700'
            }`}
          >
            <span className="font-medium">{category.name}</span>
          </button>
        ))}
      </div>
      
      {categories.length === 0 && (
        <div className="text-center py-4">
          <p className="text-gray-500 text-sm">No categories available</p>
        </div>
      )}
    </div>
  );
};

export default CategoryList;
