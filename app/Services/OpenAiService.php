<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiService
{
    protected string $apiKey;
    protected string $model;
    protected int $maxTokens;
    protected float $temperature;

    public function __construct()
    {
        $this->apiKey = config('npc.openai.api_key');
        $this->model = config('npc.openai.model', 'gpt-4o-mini');
        $this->maxTokens = config('npc.openai.max_tokens', 150);
        $this->temperature = config('npc.openai.temperature', 0.8);
    }

    /**
     * Send a chat completion request to OpenAI.
     *
     * @param string $systemPrompt The system prompt defining the NPC's personality.
     * @param array $messages Conversation history in OpenAI format.
     * @return string|null The assistant's response, or null on failure.
     */
    public function chat(string $systemPrompt, array $messages): ?string
    {
        if (empty($this->apiKey)) {
            Log::error('NPC Chat: OpenAI API key is not configured.');
            return null;
        }

        $payload = [
            'model' => $this->model,
            'messages' => array_merge(
                [['role' => 'system', 'content' => $systemPrompt]],
                $messages
            ),
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])
                ->timeout(15)
                ->post('https://api.openai.com/v1/chat/completions', $payload);

            if ($response->failed()) {
                Log::error('NPC Chat: OpenAI API request failed.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();

            return $data['choices'][0]['message']['content'] ?? null;
        } catch (\Exception $e) {
            Log::error('NPC Chat: Exception during OpenAI API call.', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
