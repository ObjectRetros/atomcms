@php
    $hotelName = setting('hotel_name', config('app.name'));
    $pageTitle = trim($__env->yieldPushContent('title'));
    $metaDescription = trim($__env->yieldPushContent('meta_description'))
        ?: setting('seo_description')
        ?: __('Join :hotel today! Meet new friends, play games, design your own rooms and become part of our community.', ['hotel' => $hotelName]);
    $metaDescription = \Illuminate\Support\Str::limit(trim(strip_tags($metaDescription)), 160, '');
    $metaKeywords = setting('seo_keywords')
        ?: implode(', ', [$hotelName, 'habbo', 'retro hotel', 'virtual world', 'pixel art', 'community', 'free to play']);
    $metaImage = trim($__env->yieldPushContent('meta_image')) ?: setting('cms_logo');
    if ($metaImage && ! str_starts_with($metaImage, 'http')) {
        $metaImage = asset($metaImage);
    }
@endphp

<title>{{ $pageTitle ? sprintf('%s - %s', $pageTitle, $hotelName) : $hotelName }}</title>

<meta name="description" content="{{ $metaDescription }}">
<meta name="keywords" content="{{ $metaKeywords }}">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $hotelName }}">
<meta property="og:title" content="{{ $pageTitle ?: $hotelName }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">
@if ($metaImage)
    <meta property="og:image" content="{{ $metaImage }}">
@endif

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $pageTitle ?: $hotelName }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@if ($metaImage)
    <meta name="twitter:image" content="{{ $metaImage }}">
@endif
