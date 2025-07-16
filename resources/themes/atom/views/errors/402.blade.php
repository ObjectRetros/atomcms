@extends('layouts.error')
@section('title', __('Payment Required'))
@section('image_height', '89px')
@section('image_width', '64px')
@section('header_image_style')
    <style>
        .header::before {
            content: url(/assets/images/errors/frank_unsure.png);
        }
    </style>
@endsection
@section('header_content')
    Oh bobba! Payment required.
@endsection
@section('content')
    <div class="text">Frank says this feature requires payment. Please complete the payment or return to the <a href="/">{{ setting('hotel_name') }} front page.</a></div>
@endsection