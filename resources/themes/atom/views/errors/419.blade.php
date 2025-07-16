@extends('layouts.error')
@section('title', __('Page Expired'))
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
    Oh bobba! Page expired.
@endsection
@section('content')
    <div class="text">Frank says your session has expired. Please refresh the page or return to the <a href="/">{{ setting('hotel_name') }} front page.</a></div>
@endsection