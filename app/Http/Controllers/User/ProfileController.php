<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\UserProfileService;

class ProfileController extends Controller
{
    public function __construct(
        private readonly UserProfileService $profileService,
    ) {}

    public function __invoke(User $user)
    {
        return view('user.profile', $this->profileService->getProfileData($user));
    }
}
