<?php

namespace App\Http\Controllers;

use App\Ai\Agents\AiAssistant;
use Illuminate\Http\Request;

class AiAgentController extends Controller
{
    public function callAgent(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required'],
        ]);

        try {
            $response = AiAssistant::make(auth()->user())
                ->prompt($validated['message'], timeout: 120);

            return response()->json(['reply' => (string) $response]);
        } catch (\Exception $e) {
            return response()->json([
                'reply' => 'Sorry, something went wrong. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
