<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\NpcConversation;
use App\Services\OpenAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NpcChatController extends Controller
{
    protected OpenAiService $openAiService;

    public function __construct(OpenAiService $openAiService)
    {
        $this->openAiService = $openAiService;
    }

    /**
     * Handle an incoming chat message from a player near the NPC.
     *
     * Expected JSON payload from the Arcturus emulator plugin:
     * {
     *     "user_id": 123,
     *     "username": "PlayerName",
     *     "bot_id": 1,
     *     "message": "Merhaba!",
     *     "player_x": 14,
     *     "player_y": 13
     * }
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'username' => 'required|string|max:50',
            'bot_id' => 'required|integer',
            'message' => 'required|string|max:500',
            'player_x' => 'required|integer',
            'player_y' => 'required|integer',
        ]);

        // Find the bot
        $bot = Bot::find($validated['bot_id']);

        if (!$bot) {
            return response()->json([
                'success' => false,
                'error' => 'Bot not found.',
            ], 404);
        }

        // Check proximity - only respond if player is within interaction distance
        $maxDistance = config('npc.defaults.interaction_distance', 1);

        if (!$bot->isWithinDistance($validated['player_x'], $validated['player_y'], $maxDistance)) {
            return response()->json([
                'success' => false,
                'error' => 'Player is too far from the NPC.',
                'ignored' => true,
            ], 200);
        }

        // Get or create conversation
        $conversation = NpcConversation::activeForUser($validated['user_id'], $bot->id)->first();

        if (!$conversation || $conversation->hasTimedOut()) {
            $conversation = NpcConversation::create([
                'user_id' => $validated['user_id'],
                'bot_id' => $bot->id,
                'messages' => [],
            ]);
        }

        // Add the player's message to conversation history
        $conversation->addMessage('user', $validated['message']);

        // Get the system prompt
        $systemPrompt = config('npc.defaults.system_prompt');

        // Add context about the player
        $contextualPrompt = $systemPrompt . "\n\nKonuştuğun oyuncunun adı: {$validated['username']}.";

        // Send to OpenAI
        $openAiMessages = $conversation->getOpenAiMessages();
        $response = $this->openAiService->chat($contextualPrompt, $openAiMessages);

        if ($response === null) {
            Log::warning('NPC Chat: OpenAI returned no response.', [
                'user_id' => $validated['user_id'],
                'bot_id' => $bot->id,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'NPC could not generate a response.',
            ], 500);
        }

        // Add the NPC's response to conversation history
        $conversation->addMessage('assistant', $response);

        return response()->json([
            'success' => true,
            'bot_id' => $bot->id,
            'bot_name' => $bot->name,
            'response' => $response,
        ]);
    }

    /**
     * Get info about the NPC bot (for the emulator plugin to discover the bot).
     */
    public function info(int $botId): JsonResponse
    {
        $bot = Bot::find($botId);

        if (!$bot) {
            return response()->json([
                'success' => false,
                'error' => 'Bot not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'bot' => [
                'id' => $bot->id,
                'name' => $bot->name,
                'room_id' => $bot->room_id,
                'x' => $bot->x,
                'y' => $bot->y,
                'interaction_distance' => config('npc.defaults.interaction_distance', 1),
            ],
        ]);
    }

    /**
     * Reset a player's conversation history with the NPC.
     */
    public function resetConversation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'bot_id' => 'required|integer',
        ]);

        NpcConversation::where('user_id', $validated['user_id'])
            ->where('bot_id', $validated['bot_id'])
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Conversation history cleared.',
        ]);
    }
}
