@extends('layouts.error')
@section('title', __('Service Unavailable'))
@section('image_height', '88px')
@section('image_width', '118px')
@section('header_image_style')
    <style>
        .header::before {
            content: url(/assets/images/errors/frank_closed.png);
        }
    </style>
@endsection
@section('header_content')
    Oh bobba! Service unavailable.
@endsection
@section('content')
    <div class="text">Frank says the {{ setting('hotel_name') }} server is temporarily down for maintenance or overloaded. Please try again later or return to the <a href="/">{{ setting('hotel_name') }} front page.</a></div>
@endsection