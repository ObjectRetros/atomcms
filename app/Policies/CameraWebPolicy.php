<?php

namespace App\Policies;

class CameraWebPolicy extends HousekeepingPolicy
{
    protected function permission(): string
    {
        return 'manage_camera_web';
    }
}
