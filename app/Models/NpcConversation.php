<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NpcConversation extends Model
{
    protected $table = 'npc_conversations';

    protected $guarded = ['id'];

    protected $casts = [
        'messages' => 'array',
    ];

    /**
     * Get the user this conversation belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the bot this conversation is with.
     */
    public function bot()
    {
        return $this->belongsTo(Bot::class, 'bot_id');
    }

    /**
     * Add a message to the conversation history.
     */
    public function addMessage(string $role, string $content): void
    {
        $messages = $this->messages ?? [];
        $messages[] = [
            'role' => $role,
            'content' => $content,
            'timestamp' => now()->toISOString(),
        ];

        // Keep only the last N messages
        $maxHistory = config('npc.defaults.max_history_messages', 20);
        if (count($messages) > $maxHistory) {
            $messages = array_slice($messages, -$maxHistory);
        }

        $this->update(['messages' => $messages]);
    }

    /**
     * Get messages formatted for OpenAI API.
     */
    public function getOpenAiMessages(): array
    {
        $messages = $this->messages ?? [];

        return array_map(function ($msg) {
            return [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ];
        }, $messages);
    }

    /**
     * Check if the conversation has timed out.
     */
    public function hasTimedOut(): bool
    {
        $timeout = config('npc.defaults.conversation_timeout', 300);

        return $this->updated_at->diffInSeconds(now()) > $timeout;
    }

    /**
     * Scope to find active conversation for a user and bot.
     */
    public function scopeActiveForUser($query, int $userId, int $botId)
    {
        $timeout = config('npc.defaults.conversation_timeout', 300);

        return $query->where('user_id', $userId)
            ->where('bot_id', $botId)
            ->where('updated_at', '>=', now()->subSeconds($timeout))
            ->latest('updated_at');
    }
}
