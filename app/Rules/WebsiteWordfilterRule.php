<?php

namespace App\Rules;

use App\Models\Miscellaneous\WebsiteWordfilter;
use Closure;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Str;

class WebsiteWordfilterRule implements InvokableRule
{
    public function __invoke(string $attribute, mixed $value, Closure $fail)
    {
        $words = WebsiteWordfilter::get()
            ->pluck('word')
            ->toArray();

        if (setting('website_wordfilter_enabled') === '1' && (in_array(strtolower($value), $words) || Str::contains(strtolower($value), $words))) {
            $fail(__('You entered something that is not allowed on :hotel', ['hotel' => setting('hotel_name')]));
        }
    }
}
