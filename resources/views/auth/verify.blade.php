@extends('layouts.guestApp')

@section('content')
    <div class="tw-container tw-mx-auto tw-py-8">
        <div class="tw-flex tw-justify-center">
            <div class="tw-w-full md:tw-w-2/3 lg:tw-w-1/2">
                <div class="tw-bg-white tw-rounded-lg tw-shadow-md">
                    <div class="tw-px-6 tw-py-4 tw-border-b tw-border-gray-200 tw-flex tw-justify-between tw-items-center">
                        <h2 class="tw-font-semibold tw-text-lg">{{ __('Verify Your Email Address') }}</h2>
                        <a class="tw-text-red-500 tw-underline" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="tw-hidden">
                        @csrf
                    </form>

                    <div class="tw-px-6 tw-py-4">
                        @if (session('resent'))
                            <div class="tw-bg-green-100 tw-border tw-border-green-400 tw-text-green-700 tw-px-4 tw-py-3 tw-rounded tw-mb-4" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif

                        <p class="tw-text-gray-700">
                            {{ __('Before proceeding, please check your email for a verification link.') }}
                        </p>
                        <p class="tw-text-gray-700">
                            {{ __('If you did not receive the email') }},
                        </p>
                        <form class="tw-inline" method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="tw-text-blue-500 tw-underline tw-hover:text-blue-700">
                                {{ __('click here to request another') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
