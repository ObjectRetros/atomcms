<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

    <title>{{ setting('hotel_name') }} - {{ __('Client') }}</title>

    <script src="{{ asset('assets/js/jquery-latest.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/jquery-ui.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/flashclient.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/swfobject.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('assets/css/clientnew.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/css/no-flash.css') }}" type="text/css">

    <script type="text/javascript">
        var flashvars = {
            "connection.info.host": "{{ config('habbo.flash.host') }}",
            "connection.info.port": "{{ config('habbo.flash.port') }}",
            "site.url": "",
            "url.prefix": "",
            "client.reload.url": "",
            "client.fatal.error.url": "",
            "client.connection.failed.url": "",
            "logout.url": "",
            "client.starting": "Please wait! Habbo is starting up.",
            "client.starting.revolving": "For science, you monster\/Loading funny message\u2026please wait.\/Would you like fries with that?\/Follow the yellow duck.\/Time is just an illusion.\/Are we there yet?!\/I like your t-shirt.\/Look left. Look right. Blink twice. Ta da!\/It\'s not you, it\'s me.\/Shhh! I\'m trying to think here.\/Loading pixel universe.",
            "client.notify.cross.domain": "1",
            "client.allow.cross.domain": "1",
            "flash.client.origin": "popup",
            "processlog.enabled": "0",
            "sso.ticket": "{{ $sso }}",
            "productdata.load.url": "{{ sprintf('%s/%s/%s', config('habbo.site.site_url'), config('habbo.flash.swf_base_path'), config('habbo.flash.external_productdata')) }}",
            "furnidata.load.url": "{{ sprintf('%s/%s/%s', config('habbo.site.site_url'), config('habbo.flash.swf_base_path'), config('habbo.flash.external_furnidata')) }}",
            "external.texts.txt": "{{ sprintf('%s/%s/%s', config('habbo.site.site_url'), config('habbo.flash.swf_base_path'), config('habbo.flash.external_texts')) }}",
            "external.variables.txt": "{{ sprintf('%s/%s/%s', config('habbo.site.site_url'), config('habbo.flash.swf_base_path'), config('habbo.flash.external_variables')) }}",
            "external.figurepartlist.txt": "{{ sprintf('%s/%s/%s', config('habbo.site.site_url'), config('habbo.flash.swf_base_path'), config('habbo.flash.external_figuredata')) }}",
            "flash.dynamic.avatar.download.configuration": "{{ sprintf('%s/%s/%s', config('habbo.site.site_url'), config('habbo.flash.swf_base_path'), config('habbo.flash.external_figuremap')) }}",
            "external.override.texts.txt": "{{ sprintf('%s/%s/%s', config('habbo.site.site_url'), config('habbo.flash.swf_base_path'), config('habbo.flash.external_override_texts')) }}",
            "external.override.variables.txt": "{{ sprintf('%s/%s/%s', config('habbo.site.site_url'), config('habbo.flash.swf_base_path'), config('habbo.flash.external_override_variables')) }}",
            "flash.client.url": "{{ sprintf('%s/%s/%s', config('habbo.site.site_url'), config('habbo.flash.swf_base_path'), config('habbo.flash.production_folder')) }}/",
        };

        window.FlashExternalInterface.disconnect = function() {
            window.location.href = "{{ route('me.show') }}";
        };

        var params = {
            "base": "{{ sprintf('%s/%s/%s', config('habbo.site.site_url'), config('habbo.flash.swf_base_path'), config('habbo.flash.production_folder')) }}/",
            "allowScriptAccess": "always",
            "menu": "false",
            "wmode": "opaque"
        };

        swfobject.embedSWF(
            '{{ sprintf('%s/%s/%s/%s', config('habbo.site.site_url'), config('habbo.flash.swf_base_path'), config('habbo.flash.production_folder'), config('habbo.flash.habbo_swf')) }}',
            'client', '100%', '100%', '11.1.0', '{{ asset('assets/js/expressInstall.swf') }}', flashvars, params, null,
            null);
    </script>
</head>

<body>
    <div id="client">
        <habbo-client-error>
            <div class="client-error__background-frank">
                <div class="client-error__text-contents">
                    <h1 class="client-error__title">{{ __('You are nearly in Habbo!') }}</h1>
                    <p>{{ __('Click the yellow Hotel button below, then click on run flash` when prompted to. See you in the Hotel!') }}
                    </p>
                </div>
                <div class="client-error__hotel-button-div">
                    <a href="https://www.adobe.com/go/getflashplayer" target="_blank" rel="noopener noreferrer"
                        class="hotel-button">
                        <span class="hotel-button__text">{{ __('Get flash') }}</span>
                    </a>
                </div>
            </div>
        </habbo-client-error>
    </div>
</body>

</html>
