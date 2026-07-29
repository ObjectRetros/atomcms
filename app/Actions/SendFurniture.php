<?php

namespace App\Actions;

use App\Contracts\Rcon;
use App\Emulator\Contracts\FurnitureRepository;
use App\Models\User;

final readonly class SendFurniture
{
    public function __construct(
        private readonly Rcon $rcon,
        private readonly FurnitureRepository $furniture,
    ) {}

    /**
     * @param  array<int, array{item_id: int, amount: int}>  $furniture
     */
    public function execute(User $user, array $furniture): void
    {
        foreach ($furniture as $furni) {
            if ($this->rcon->isConnected()) {
                for ($i = 0; $i < $furni['amount']; $i++) {
                    $this->rcon->sendGift($user, $furni['item_id'], 'Thank you for supporting ' . setting('hotel_name'));
                }

                continue;
            }

            $this->furniture->grant($user, (int) $furni['item_id'], (int) $furni['amount']);
        }
    }
}
