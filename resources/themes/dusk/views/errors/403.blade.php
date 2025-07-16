@extends('layouts.error')
@section('title', __('Forbidden'))
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
    Oh bobba! Access forbidden.
@endsection
@section('content')
    <div class="text">Frank says you don’t have permission to access this page. Please check your credentials or return to the <a href="/">{{ setting('hotel_name') }} front page.</a></div>
@endsection