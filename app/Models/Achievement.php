<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $table = 'achievements';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // Category labels for display
    public static array $categoryLabels = [
        'identity'     => 'Kimlik',
        'explore'      => 'Keşif',
        'music'        => 'Müzik',
        'social'       => 'Sosyal',
        'games'        => 'Oyunlar',
        'room_builder' => 'İnşaatçı',
        'pets'         => 'Evcil Hayvanlar',
        'tools'        => 'Araçlar',
        'events'       => 'Etkinlikler',
    ];

    // Category icon classes or emoji
    public static array $categoryIcons = [
        'identity'     => '🪪',
        'explore'      => '🧭',
        'music'        => '🎵',
        'social'       => '🤝',
        'games'        => '🎮',
        'room_builder' => '🏗️',
        'pets'         => '🐾',
        'tools'        => '🔧',
        'events'       => '🎉',
    ];

    public static function getLabelFor(string $category): string
    {
        return self::$categoryLabels[$category] ?? ucfirst($category);
    }

    public static function getIconFor(string $category): string
    {
        return self::$categoryIcons[$category] ?? '🏆';
    }
}
