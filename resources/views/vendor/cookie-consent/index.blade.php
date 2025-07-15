@if($cookieConsentConfig['enabled'] && ! $alreadyConsentedWithCookies)
    <div class="cookie-consent-overlay">
        <div class="cookie-consent-modal js-cookie-consent">
            <div class="cookie-consent-content">
                <h2 class="cookie-consent-title">{{ __(':hotel', ['hotel' => setting('hotel_name')]) }} {{ trans('cookie-consent::texts.intro') }}</h2>
                <p class="cookie-consent-message">
                    {{ trans('cookie-consent::texts.message') }}
                </p>
                <div class="cookie-consent-actions">
                    <button type="button" class="cookie-consent-button js-cookie-consent-agree">
                        {{ trans('cookie-consent::texts.agree') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .cookie-consent-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cookie-consent-modal {
            background: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
            padding: 20px;
            color: #000000;
        }

        .cookie-consent-content {
            text-align: center;
        }

        .cookie-consent-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .cookie-consent-message {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .cookie-consent-actions {
            display: flex;
            justify-content: center;
        }

        .cookie-consent-button {
            background: #007aff;
            color: #1e3a8a;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
			color: #FFFFFF;
        }

        .cookie-consent-button:hover {
            background: #eab308;
        }

        body.cookie-consent-active {
            overflow: hidden;
        }

        body.cookie-consent-active > *:not(.cookie-consent-overlay) {
            filter: blur(5px);
        }

        /* Fade-in animation for page load */
        body {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Fade-out animation for page reload */
        body.fade-out {
            animation: fadeOut 0.5s ease-out forwards;
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Cookie consent modal initialized');
            document.body.classList.add('cookie-consent-active');
        });

        window.laravelCookieConsent = (function () {
            const COOKIE_VALUE = 1;
            const COOKIE_DOMAIN = '{{ config('session.domain') ?? request()->getHost() }}';

            function consentWithCookies() {
                try {
                    console.log('Consent button clicked');
                    setCookie('{{ $cookieConsentConfig['cookie_name'] }}', COOKIE_VALUE, {{ $cookieConsentConfig['cookie_lifetime'] }});
                    hideCookieDialog();
                    document.body.classList.remove('cookie-consent-active');
                    console.log('Consent processed, initiating fade-out and reload');

                    document.body.classList.add('fade-out');
                    setTimeout(() => {
                        console.log('Fade-out complete, refreshing page');
                        window.location.reload();
                    }, 500);
                } catch (error) {
                    console.error('Error in consentWithCookies:', error);
                }
            }

            function cookieExists(name) {
                return (document.cookie.split('; ').indexOf(name + '=' + COOKIE_VALUE) !== -1);
            }

            function hideCookieDialog() {
                const dialogs = document.getElementsByClassName('js-cookie-consent');
                const overlay = document.querySelector('.cookie-consent-overlay');
                for (let i = 0; i < dialogs.length; ++i) {
                    dialogs[i].style.display = 'none';
                }
                if (overlay) {
                    overlay.style.display = 'none';
                }
            }

            function setCookie(name, value, expirationInDays) {
                const date = new Date();
                date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000));
                document.cookie = name + '=' + value
                    + ';expires=' + date.toUTCString()
                    + ';domain=' + COOKIE_DOMAIN
                    + ';path=/{{ config('session.secure') ? ';secure' : null }}'
                    + '{{ config('session.same_site') ? ';samesite='.config('session.same_site') : null }}';
            }

            if (cookieExists('{{ $cookieConsentConfig['cookie_name'] }}')) {
                hideCookieDialog();
                document.body.classList.remove('cookie-consent-active');
            }

            const buttons = document.getElementsByClassName('js-cookie-consent-agree');
            for (let i = 0; i < buttons.length; ++i) {
                buttons[i].addEventListener('click', consentWithCookies);
            }

            return {
                consentWithCookies: consentWithCookies,
                hideCookieDialog: hideCookieDialog
            };
        })();
    </script>
@endif