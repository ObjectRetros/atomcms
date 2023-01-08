<x-app-layout>
    @push('title', __('Shop'))

    <div class="col-span-12">
        <div class="w-full rounded bg-red-600 p-4 text-white text-sm text-center">
            {{ __('Here at :hotel we have some terms, that you must read before making a purchase. Once you make a purchases you automatically accept our given terms.', ['hotel' => setting('hotel_name')]) }}
            <a href="#" target="_blank" class="underline">{{ __('Shop terms') }}</a>
        </div>
    </div>

    <div class="col-span-12 md:col-span-9">
        <div class="grid grid-cols-3 gap-3 dark:text-gray-300">
            @foreach($packages as $package)
                <x-shop.packages :package="$package" :package-content="json_decode($package->data)" />
            @endforeach
        </div>
    </div>

    <div class="col-span-12 md:col-span-3 flex flex-col gap-y-3 dark:text-gray-100">
        <x-content.content-section icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __(':hotel Shop', ['hotel' => setting('hotel_name')]) }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Purchase :hotel items', ['hotel' => setting('hotel_name')]) }}
            </x-slot:under-title>

            <small>
                {{ __('Please read our shop terms before making a purchase here at :hotel. We consider all purchases a donation, and we will in no circumstances issue a refund.', ['hotel' => setting('hotel_name')]) }}
            </small>

            <div class="mt-4">
                Current balance:
                <strong> $<span id="balance">{{ auth()->user()->website_store_balance }}</span></strong>
            </div>

            <div class="flex flex-col my-4">
                <x-form.input name="fillupAmount" placeholder="Enter top up amount" :autofocus="true" />
            </div>

            @if (Auth::check())
                <div id="paypal-button-container"></div>
            @endif
        </x-content.content-section>

        @auth
            <x-content.content-section icon="hotel-icon">
                <x-slot:title>
                    {{ __('Redeem voucher') }}
                </x-slot:title>

                <x-slot:under-title>
                    {{ __('Redeem your voucher') }}
                </x-slot:under-title>

                <small>
                    {{ __('Enter a shop voucher to fill up your store balance', ['hotel' => setting('hotel_name')]) }}
                </small>

                <form action="{{ route('shop.redeem-voucher') }}" method="POST" class="flex flex-col gap-y-3">
                    @csrf

                   <div class="w-full flex flex-col mt-4">
                       <x-form.input name="code" placeholder="{{ __('Enter your shop voucher') }}" />
                   </div>

                    <x-form.secondary-button>
                        {{ __('Redeem') }}
                    </x-form.secondary-button>
                </form>
            </x-content.content-section>
        @endauth
    </div>

    <style>
        .bronze-vip {
            background: #c5630f url({{ sprintf('%s/VipParties2.gif', setting('badges_path')) }}) no-repeat center;
        }

        .silver-vip {
            background: #dddddd url({{ sprintf('%s/VipParties2_Top100.gif', setting('badges_path')) }}) no-repeat center;
        }

        .gold-vip {
            background: #E4A317FF url({{ sprintf('%s/VipParties2_Top10.gif', setting('badges_path')) }}) no-repeat center;
        }
    </style>

    @push('javascript')
        <script>
            window.addEventListener('load', () => {
                paypal.Buttons({
                    createOrder: function (data, actions) {
                        return fetch('{{ route("paypal.create") }}', {
                            method: 'post',
                            headers: {
                                'content-type': 'application/json'
                            },
                            body: JSON.stringify({
                                api_token: '{{ auth()->user()->paypal_api_token }}',
                                value: document.querySelector('#fillupAmount').value,
                            })
                        }).then(function (res) {
                            let json = res.json();

                            return json;
                        }).then(function (data) {
                            return data.id;
                        });
                    },

                    onApprove: function (data, actions) {
                        return fetch('{{ route("paypal.capture") }}', {
                            method: 'post',
                            headers: {
                                'content-type': 'application/json'
                            },
                            body: JSON.stringify({
                                orderID: data.orderID
                            })
                        }).then(function (res) {
                            return res.json();
                        }).then(function (details) {
                            paymentConfirmation(details);
                        });
                    }
                }).render('#paypal-button-container');
            });

            function paymentConfirmation(details) {
                Swal.fire(
                    'Thank you for your purchase!',
                    'Your balance has been topped up with $' + parseInt(details.purchase_units[0].payments.captures[0].amount.value),
                    'success'
                )

                const balance = parseInt(document.querySelector('#balance').innerHTML);
                if(balance === 0) {
                    document.querySelector('#balance').innerHTML = parseInt(details.purchase_units[0].payments.captures[0].amount.value)

                    return;
                }

                document.querySelector('#balance').innerHTML = parseInt(details.purchase_units[0].payments.captures[0].amount.value) + balance;
            }
        </script>
    @endpush
</x-app-layout>
