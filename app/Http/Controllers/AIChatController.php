<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // For API call

class AIChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->message;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4', 
                'messages' => [
                    ['role' => 'user', 'content' => $message]
                ],
                'temperature' => 0.7,
            ]);


            // Log the raw response from OpenAI
            // \Log::info('OpenAI Response:', $response->json());

            $data = $response->json();
            $reply = $data['choices'][0]['text'] ?? "Sorry, I couldn't respond.";

            return response()->json([
                'success' => true,
                'response' => $reply
            ]);
        } catch (\Exception $e) {
            // Log the error
            // \Log::error('OpenAI Error:', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'response' => 'Something went wrong while contacting AI.'
            ], 500);
        }
    }
}
