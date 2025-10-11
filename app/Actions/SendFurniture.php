<?php

namespace App\Actions;

use App\Models\User;
use App\Services\RconService;

class SendFurniture
{
    public function __construct(private RconService $rcon) {}

    public function execute(User $user, array $furniture): void
    {
        foreach ($furniture as $furni) {
            if ($this->rcon->isConnected) {
                for ($i = 0; $i < $furni['amount']; $i++) {
                    $this->rcon->sendGift($user, $furni['item_id'], 'Thank you for supporting ' . setting('hotel_name'));
                }
            } else {
                for ($i = 0; $i < $furni['amount']; $i++) {
                    $user->items()->create([
                        'item_id' => $furni['item_id'],
                    ]);
                }
            }
        }
    }
}
