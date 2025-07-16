@extends('layouts.error')
@section('title', __('Internal Server Error'))
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
    Oh bobba! Server error.
@endsection
@section('content')
    @if (config('app.debug'))
    <div class="text">
        <div class="debug">
            <pre>Error: {{ $exception->getMessage() }}</pre>
        </div>
    </div>
    @else
    <div class="text">We're experiencing some technical difficulties. Please try again later.</div>
    @endif
@endsection