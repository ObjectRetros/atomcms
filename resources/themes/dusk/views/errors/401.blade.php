@extends('layouts.error')
@section('title', __('Unauthorized'))
@section('image_height', '86px')
@section('image_width', '56px')
@section('header_image_style')
    <style>
        .header::before {
            content: url(/assets/images/errors/frank_access.png);
        }
    </style>
@endsection
@section('header_content')
    Oh bobba! Unauthorized access.
@endsection
@section('content')
    <div class="text">Frank says you need to log in to access this page. Please log in or return to the <a href="/">{{ setting('hotel_name') }} front page.</a></div>
@endsection