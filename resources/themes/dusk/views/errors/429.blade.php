@extends('layouts.error')
@section('title', __('Too Many Requests'))
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
    Oh bobba! Too many requests.
@endsection
@section('content')
    <div class="text">Frank says you've made too many requests. Please wait a moment and try again, or return to the <a href="/">{{ setting('hotel_name') }} front page.</a></div>
@endsection