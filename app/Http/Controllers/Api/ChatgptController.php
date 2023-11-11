<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatgptController extends Controller
{
    /**
     * Prompt to ChatGPT for get information.
     *
     * @return \Illuminate\Http\Response
     */
    function sendQuery(Request $request)
    {
        $prompt = $request->prompt;
        $data = Http::withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . env("OPEN_API_KEY")
        ])->post(
            "https://api.openai.com/v1/chat/completions",
            [
                "model" => "gpt-3.5-turbo",
                "messages" => [
                    [
                        "role" => "user",
                        "content" => $prompt
                    ]
                ],
                "temperature" => 0.5,
                "max_tokens"=> 200,
                "top_p" => 1.0,
                "frequency_penalty" => 0.5,
                "stop" => "11."
            ]
        )->json();

        $chat = Chat::create([
            'user_id' => $request->user()->id,
            'prompt' => $request->prompt,
            'content' => $data['choices'][0]['message']['content']
        ]);
        return response()->json($chat);
    }
}
