<?php

namespace App\Http\Requests\Home;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

abstract class HomeRequest extends FormRequest
{
    protected function homeOwner(): ?User
    {
        $user = $this->route('user');

        if ($user instanceof User) {
            return $user;
        }

        if (! is_string($user)) {
            return null;
        }

        return User::where('username', $user)->first();
    }

    protected function isHomeOwner(): bool
    {
        $owner = $this->homeOwner();

        return $owner instanceof User && $this->user()?->is($owner);
    }

    protected function isHomeVisitor(): bool
    {
        $owner = $this->homeOwner();
        $visitor = $this->user();

        // Guests must never pass this guard on their own; a null user made
        // the previous nullsafe comparison truthy.
        return $owner instanceof User && $visitor instanceof User && ! $visitor->is($owner);
    }
}
