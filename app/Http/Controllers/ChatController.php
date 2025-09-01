<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'session_id' => 'nullable|string'
        ]);

        $userMessage = $request->input('message');
        $sessionId = $request->input('session_id') ?? Str::random(20);

        // Save user message
        try {
            Chat::create([
                'message' => $userMessage,
                'sender' => 'user',
                'session_id' => $sessionId
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving user message: ' . $e->getMessage());
        }

        // Generate bot response
        $botResponse = $this->generateResponse($userMessage);

        // Save bot response
        try {
            Chat::create([
                'message' => $botResponse,
                'sender' => 'bot',
                'session_id' => $sessionId
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving bot response: ' . $e->getMessage());
        }

        return response()->json([
            'response' => $botResponse,
            'session_id' => $sessionId
        ]);
    }

    private function generateResponse($message)
    {
        $message = strtolower(trim($message));
        
        Log::info('Received message: ' . $message);

        // Check for specific intents in order of priority
        if ($this->isGreeting($message)) {
            return $this->handleGreeting($message);
        }
        
        if ($this->isProductInquiry($message)) {
            return $this->handleProductInquiry($message);
        }
        
        if ($this->isSpecificProductQuestion($message)) {
            return $this->handleSpecificProductQuestion($message);
        }
        
        if ($this->isOrderRelated($message)) {
            return $this->handleOrderRelated($message);
        }
        
        if ($this->isRecommendationRequest($message)) {
            return $this->handleRecommendationRequest($message);
        }
        
        if ($this->isPricingQuestion($message)) {
            return $this->handlePricingQuestion($message);
        }
        
        if ($this->isStockQuestion($message)) {
            return $this->handleStockQuestion($message);
        }
        
        if ($this->isThankYou($message)) {
            return $this->handleThankYou();
        }
        
        if ($this->isHelpRequest($message)) {
            return $this->handleHelpRequest();
        }

        // Default response for unrecognized messages
        return $this->handleDefaultResponse($message);
    }

    // Intent detection methods
    private function isGreeting($message)
    {
        return preg_match('/\b(hello|hi|hey|greetings|good morning|good afternoon|good evening)\b/', $message);
    }

    private function isProductInquiry($message)
    {
        return preg_match('/\b(product|item|thing|merchandise|goods|inventory|stock|category|categories|what do you sell|what\'s available|show me products|list products)\b/', $message);
    }

    private function isSpecificProductQuestion($message)
    {
        // Look for product names or specific attributes
        $products = Product::where('is_active', 1)->get();
        
        foreach ($products as $product) {
            $productName = strtolower($product->name);
            if (str_contains($message, $productName) || 
                preg_match('/\b' . preg_quote($productName, '/') . '\b/', $message)) {
                return true;
            }
        }
        
        // Check for brand mentions
        $brands = Product::where('is_active', 1)->distinct()->pluck('brand');
        foreach ($brands as $brand) {
            if ($brand && str_contains($message, strtolower($brand))) {
                return true;
            }
        }
        
        return false;
    }

    private function isOrderRelated($message)
    {
        return preg_match('/\b(order|track|status|delivery|shipment|package|parcel|where is my|when will|tracking number)\b/', $message);
    }

    private function isRecommendationRequest($message)
    {
        return preg_match('/\b(recommend|suggest|advise|what\'s good|what\'s popular|best seller|top item|what should i buy|idea|featured|trending)\b/', $message);
    }

    private function isPricingQuestion($message)
    {
        return preg_match('/\b(price|cost|how much|expensive|cheap|affordable|discount|sale|offer|promotion|deal)\b/', $message);
    }

    private function isStockQuestion($message)
    {
        return preg_match('/\b(stock|available|in stock|out of stock|quantity|how many left|when restock|backorder)\b/', $message);
    }

    private function isThankYou($message)
    {
        return preg_match('/\b(thanks|thank you|appreciate|grateful|cheers|nice one)\b/', $message);
    }

    private function isHelpRequest($message)
    {
        return preg_match('/\b(help|support|assist|what can you do|how does this work|guide)\b/', $message);
    }

    // Response handling methods
    private function handleGreeting($message)
    {
        $totalProducts = Product::where('is_active', 1)->count();
        $greetings = [
            "Hello! We have $totalProducts amazing products available. How can I assist you with your shopping today?",
            "Hi there! I'm here to help you explore our $totalProducts products. What can I do for you?",
            "Hey! Welcome to our store with $totalProducts quality items. How can I help you today?",
            "Greetings! I'm your shopping assistant for our $totalProducts products. What would you like to know?"
        ];
        
        return $greetings[array_rand($greetings)];


        
    }

    private function handleProductInquiry($message)
    {
        // Get all active categories with product counts
        $categories = Category::withCount(['products' => function($query) {
            $query->where('is_active', 1);
        }])->get();
        
        $categoryInfo = [];
        foreach ($categories as $category) {
            if ($category->products_count > 0) {
                $categoryInfo[] = "{$category->name} ({$category->products_count} products)";
            }
        }
        
        $categoryList = implode(', ', $categoryInfo);
        
        // Check if specific category is mentioned
        foreach ($categories as $category) {
            if (str_contains($message, strtolower($category->name))) {
                $products = Product::where('category_id', $category->id)
                    ->where('is_active', 1)
                    ->take(5)
                    ->get();
                
                if ($products->count() > 0) {
                    $response = "In {$category->name}, we have: ";
                    $productNames = [];
                    foreach ($products as $product) {
                        $price = $product->discount_price ?? $product->price;
                        $productNames[] = "{$product->name} (\${$price})";
                    }
                    $response .= implode(', ', $productNames) . ". Would you like details about any of these?";
                    return $response;
                }
            }
        }
        
        return "We have products in these categories: $categoryList. Which category interests you? I can show you specific products!";
    }

    private function handleSpecificProductQuestion($message)
    {
        // Search for products by name
        $products = Product::where('is_active', 1)->get();
        $matchedProducts = [];
        
        foreach ($products as $product) {
            $productName = strtolower($product->name);
            if (str_contains($message, $productName) || 
                preg_match('/\b' . preg_quote($productName, '/') . '\b/', $message)) {
                $matchedProducts[] = $product;
            }
        }
        
        // Search by brand
        if (empty($matchedProducts)) {
            $brands = Product::where('is_active', 1)->distinct()->pluck('brand');
            foreach ($brands as $brand) {
                if ($brand && str_contains($message, strtolower($brand))) {
                    $matchedProducts = Product::where('brand', $brand)
                        ->where('is_active', 1)
                        ->get()
                        ->toArray();
                    break;
                }
            }
        }
        
        if (!empty($matchedProducts)) {
            $response = "";
            foreach ($matchedProducts as $product) {
                $price = $product['discount_price'] ?? $product['price'];
                $stock = $product['stock'] > 0 ? "In stock ({$product['stock']} available)" : "Out of stock";
                
                $response .= "{$product['name']}: \${$price} - {$stock}. ";
                $response .= "Description: " . substr($product['description'], 0, 100) . "... ";
            }
            
            if (count($matchedProducts) === 1) {
                $response .= "Would you like to know more about this product?";
            } else {
                $response .= "Which product would you like more details about?";
            }
            
            return $response;
        }
        
        return "I couldn't find that specific product. Would you like to browse our categories instead?";
    }

    private function handleOrderRelated($message)
    {
        if (preg_match('/\b(ORD|ord|order|#)?(\d{3,})\b/', $message, $matches)) {
            $orderNumber = $matches[2] ?? $matches[0];
            return "I can help you track order #$orderNumber. Please provide your order number or check the 'My Orders' section in your account for detailed status.";
        }
        
        return "To check your order status, I'll need your order number. You can also visit the 'My Orders' section in your account for detailed information.";
    }

    private function handleRecommendationRequest($message)
    {
        // Get best-selling products (assuming higher stock movement indicates popularity)
        $popularProducts = Product::where('is_active', 1)
            ->where('stock', '>', 0)
            ->orderBy('stock', 'asc') // Lower stock might indicate popularity
            ->take(5)
            ->get();
        
        if ($popularProducts->count() > 0) {
            $response = "Our most popular items right now: ";
            $productList = [];
            
            foreach ($popularProducts as $product) {
                $price = $product->discount_price ?? $product->price;
                $productList[] = "{$product->name} (\${$price})";
            }
            
            $response .= implode(', ', $productList) . ". ";
            $response .= "Would you like details about any of these best-sellers?";
            
            return $response;
        }
        
        return "I'd recommend checking out our electronics collection or summer clothing line. Both have great products at competitive prices!";
    }

    private function handlePricingQuestion($message)
    {
        // Try to match specific products
        $products = Product::where('is_active', 1)->get();
        
        foreach ($products as $product) {
            $productName = strtolower($product->name);
            if (str_contains($message, $productName)) {
                $price = $product->discount_price ?? $product->price;
                $originalPrice = $product->discount_price ? " (originally \${$product->price})" : "";
                return "The {$product->name} is currently \${$price}{$originalPrice}. " . 
                       ($product->stock > 0 ? "It's in stock!" : "Currently out of stock.");
            }
        }
        
        // Category-based pricing
        $categories = Category::all();
        foreach ($categories as $category) {
            if (str_contains($message, strtolower($category->name))) {
                $categoryProducts = Product::where('category_id', $category->id)
                    ->where('is_active', 1)
                    ->get();
                
                if ($categoryProducts->count() > 0) {
                    $minPrice = $categoryProducts->min('price');
                    $maxPrice = $categoryProducts->max('price');
                    return "In {$category->name}, prices range from \${$minPrice} to \${$maxPrice}. Any specific product you're interested in?";
                }
            }
        }
        
        return "Our prices vary by product. We have items from \$8 to \$200. Could you specify which product or category you're interested in?";
    }

    private function handleStockQuestion($message)
    {
        $products = Product::where('is_active', 1)->get();
        
        foreach ($products as $product) {
            $productName = strtolower($product->name);
            if (str_contains($message, $productName)) {
                if ($product->stock > 0) {
                    return "Yes, {$product->name} is in stock! We have {$product->stock} available.";
                } else {
                    return "Sorry, {$product->name} is currently out of stock. We expect more soon!";
                }
            }
        }
        
        return "I can check stock availability for you. Please specify which product you're interested in.";
    }

    private function handleThankYou()
    {
        $responses = [
            "You're welcome! Is there anything else I can help you find in our store?",
            "Happy to help! Let me know if you need information about any other products.",
            "My pleasure! Feel free to ask if you have more questions about our products.",
            "Glad I could assist! What else would you like to know about our offerings?"
        ];
        
        return $responses[array_rand($responses)];
    }

    private function handleHelpRequest()
    {
        $totalProducts = Product::where('is_active', 1)->count();
        
        return "I can help you with our {$totalProducts} products! You can ask me about:
- Specific product information and pricing
- Product availability and stock levels
- Order status and tracking
- Product recommendations
- Category browsing
- And much more!

What would you like to know about our products?";
    }

    private function handleDefaultResponse($message)
    {
        Log::info('Unrecognized message pattern: ' . $message);
        
        $totalProducts = Product::where('is_active', 1)->count();
        $defaultResponses = [
            "I'm here to help you explore our {$totalProducts} products. You can ask me about specific items, categories, or get recommendations!",
            "I'm not sure I understand. Could you try asking about our products, their prices, or availability?",
            "I specialize in helping with product information. Feel free to ask me about our {$totalProducts} items!",
            "I'd love to help you find the perfect product! You can ask me about specific items, brands, or categories."
        ];
        
        return $defaultResponses[array_rand($defaultResponses)];
    }

    public function getChatHistory($sessionId)
{
    $chats = Chat::where('session_id', $sessionId)
                ->orderBy('created_at', 'desc')
                ->take(10) // Limit to last 50 messages
                ->get()
                ->reverse(); // Reverse to maintain chronological order

    return response()->json($chats);
}
}