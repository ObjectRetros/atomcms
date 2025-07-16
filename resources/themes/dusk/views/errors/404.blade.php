@extends('layouts.error')
@section('title', __('Not Found'))
@section('image_height', '115px')
@section('image_width', '132px')
@section('header_image_style')
    <style>
        .header::before {
            content: url(/assets/images/errors/frank_inspect.png);
        }
    </style>
@endsection
@section('header_content')
    Oh bobba! Page not found.
@endsection
@section('content')
    <div class="text">Frank can't find the page you're looking for. Please check the URL or try starting over from the <a href="/">{{ setting('hotel_name') }} front page.</a></div>
@endsection