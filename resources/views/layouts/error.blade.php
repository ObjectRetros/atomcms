<!DOCTYPE html>
<html lang="en">
<head>
    <style>
		body {
            margin: 0;
            height: 100vh;
            background-color: #e5e7eb;
			background: url(/assets/images/background-light.jpg) no-repeat center center;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            font-family: Arial, sans-serif;
            color: #000000;
            font-size: 24px;
            padding-bottom: 56px;
			position: relative;
            overflow: hidden;
        }
        .header {
            position: absolute;
            top: 15%;
            text-align: center;
            z-index: 2;
        }
        .header::before {
            display: block;
            height: @yield('image_height', '115px');
            margin: 0 auto 12px;
            width: @yield('image_width', '132px');
        }
        .text {
            position: absolute;
            top: calc(15% + @yield('image_height', '115px') + 12px + 30px);
            text-align: center;
            font-size: 14px;
            max-width: 1080px;
            color: #14328a;
            z-index: 2;
        }
        .text a {
            color: #5f5f5f;
            text-decoration: underline;
        }
        .text a:hover {
            color: #d6deff;
        }
        .debug {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 5px;
            text-align: left;
            font-family: monospace;
            font-size: 12px;
            color: #000000;
            max-height: 300px;
            overflow-y: auto;
            margin: 10px auto;
        }
        .debug pre {
            margin: 0;
            white-space: pre-wrap;
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #3b3b3b;
            color: #8b8b8b;
            font-weight: 700;
            font-size: 14px;
            transition: color 0.2s ease-in-out;
            z-index: 2;
        }
    </style>
    @yield('header_image_style')
</head>
<body>
    <div class="header">@yield('header_content')</div>
    @yield('content')
    <footer>{{ __(':hotel is a not for profit educational project', ['hotel' => setting('hotel_name')]) }} ©{{ date('Y') }}</footer>
</body>
</html>