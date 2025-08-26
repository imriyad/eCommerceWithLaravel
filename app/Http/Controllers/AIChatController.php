<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->message;

        try {
            // Try a sequence of models that are more likely to work
            $models = [
                'microsoft/DialoGPT-large',    // Good for conversational AI
                'google/flan-t5-large',        // Good for instruction following
                'distilgpt2',                  // Small and reliable
                'gpt2'                         // Basic but usually available
            ];

            foreach ($models as $model) {
                $result = $this->tryModel($model, $message);
                if ($result['success']) {
                    return response()->json($result);
                }
                
                Log::info("Model {$model} failed, trying next...");
            }

            // If all models fail, return error
            return response()->json([
                'success' => false,
                'response' => 'All AI services are currently unavailable. Please try again later or use a different AI provider.'
            ], 503);

        } catch (\Exception $e) {
            Log::error('AI Chat Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'response' => 'Sorry, the AI service is temporarily unavailable. Please try again shortly.'
            ], 500);
        }
    }

    /**
     * Try a specific model
     */
    private function tryModel($model, $message)
    {
        try {
            $apiUrl = "https://api-inference.huggingface.co/models/{$model}";
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
                'Content-Type' => 'application/json',
            ])->timeout(30)
            ->post($apiUrl, [
                'inputs' => $this->formatPrompt($message),
                'parameters' [
                    'max_new_tokens' => 150,
                    'temperature' => 0.7,
                    'return_full_text' => false,
                ]
            ]);

            Log::info("Trying model: {$model}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data[0]['generated_text'])) {
                    return [
                        'success' => true,
                        'response' => trim($data[0]['generated_text'])
                    ];
                }
                elseif (isset($data['generated_text'])) {
                    return [
                        'success' => true,
                        'response' => trim($data['generated_text'])
                    ];
                }
            }

            return ['success' => false];

        } catch (\Exception $e) {
            Log::warning("Model {$model} failed: " . $e->getMessage());
            return ['success' => false];
        }
    }

    /**
     * Format prompt for shopping assistant
     */
    private function formatPrompt($message)
    {
        return "You are ShopAssist, a helpful shopping assistant. Answer the customer's question clearly and helpfully.\n\nCustomer: " . $message . "\n\nShopAssist:";
    }

    /**
     * Alternative: Use Ollama if installed locally (highly recommended)
     */
    public function chatWithOllama(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        try {
            // Use Ollama locally - much more reliable
            $response = Http::post('http://localhost:11434/api/generate', [
                'model' => 'gemma:7b-it', // or any model you have pulled
                'prompt' => "You are ShopAssist, a helpful shopping assistant. " . $request->message,
                'stream' => false,
                'options' => [
                    'temperature' => 0.7,
                    'num_predict' => 500,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'response' => trim($data['response'])
                ]);
            }

            return response()->json([
                'success' => false,
                'response' => 'Local AI service unavailable. Please install Ollama or try the cloud version.'
            ], 503);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'response' => 'Local AI service not running. Please install Ollama from https://ollama.ai'
            ], 503);
        }
    }

    /**
     * Health check for Hugging Face API
     */
    public function checkApiStatus()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
            ])->get('https://huggingface.co/api/whoami');

            return response()->json([
                'success' => true,
                'user' => $response->json(),
                'status' => 'API key is valid'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'API key may be invalid'
            ], 500);
        }
    }
}