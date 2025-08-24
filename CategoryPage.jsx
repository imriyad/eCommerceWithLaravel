import React, { useState } from 'react';
import CategoryList from './CategoryList';
import CategoryProducts from './CategoryProducts';

const CategoryPage = () => {
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [selectedCategoryName, setSelectedCategoryName] = useState('');

  const handleCategorySelect = (categoryId, categoryName) => {
    setSelectedCategory(categoryId);
    setSelectedCategoryName(categoryName);
  };

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="max-w-7xl mx-auto px-4">
        <div className="mb-8">
          <h1 className="text-4xl font-bold text-gray-900 mb-2">Browse by Category</h1>
          <p className="text-gray-600">
            Select a category to view related products
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
          {/* Left Sidebar - Categories */}
          <div className="lg:col-span-1">
            <CategoryList 
              onCategorySelect={handleCategorySelect}
              selectedCategoryId={selectedCategory}
            />
          </div>

          {/* Right Side - Products */}
          <div className="lg:col-span-3">
            <CategoryProducts 
              categoryId={selectedCategory}
              categoryName={selectedCategoryName}
            />
          </div>
        </div>
      </div>
    </div>
  );
};

export default CategoryPage;
