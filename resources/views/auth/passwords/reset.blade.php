@extends('layouts.guestApp')

@section('content')
    <div class="tw-container tw-mx-auto tw-py-8">
        <div class="tw-flex tw-justify-center">
            <div class="tw-w-full md:tw-w-2/3 lg:tw-w-1/2">
                <div class="tw-bg-white tw-rounded-lg tw-shadow-md">
                    <div class="tw-px-6 tw-py-4 tw-border-b tw-border-gray-200">
                        <h2 class="tw-font-semibold tw-text-lg">{{ __('Reset Password') }}</h2>
                    </div>
                    <div class="tw-px-6 tw-py-4">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="tw-mb-4">
                                <label for="email" class="tw-block tw-text-gray-700 tw-text-sm tw-font-bold tw-mb-2">{{ __('E-Mail Address') }}</label>
                                <input id="email" type="email" class="tw-shadow tw-appearance-none tw-border @error('email') tw-border-red-500 @enderror tw-rounded tw-w-full tw-py-2 tw-px-3 tw-text-gray-700 tw-leading-tight focus:tw-outline-none focus:tw-shadow-outline" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                <p class="tw-text-red-500 tw-text-xs tw-italic tw-mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="tw-mb-4">
                                <label for="password" class="tw-block tw-text-gray-700 tw-text-sm tw-font-bold tw-mb-2">{{ __('Password') }}</label>
                                <input id="password" type="password" class="tw-shadow tw-appearance-none tw-border @error('password') tw-border-red-500 @enderror tw-rounded tw-w-full tw-py-2 tw-px-3 tw-text-gray-700 tw-leading-tight focus:tw-outline-none focus:tw-shadow-outline" name="password" required autocomplete="new-password">
                                @error('password')
                                <p class="tw-text-red-500 tw-text-xs tw-italic tw-mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="tw-mb-4">
                                <label for="password-confirm" class="tw-block tw-text-gray-700 tw-text-sm tw-font-bold tw-mb-2">{{ __('Confirm Password') }}</label>
                                <input id="password-confirm" type="password" class="tw-shadow tw-appearance-none tw-border tw-rounded tw-w-full tw-py-2 tw-px-3 tw-text-gray-700 tw-leading-tight focus:tw-outline-none focus:tw-shadow-outline" name="password_confirmation" required autocomplete="new-password">
                            </div>

                            <div class="tw-mb-0">
                                <div class="tw-flex tw-justify-end">
                                    <button type="submit" class="tw-bg-blue-500 tw-hover:bg-blue-700 tw-text-white tw-font-bold tw-py-2 tw-px-4 tw-rounded focus:tw-outline-none focus:tw-shadow-outline">
                                        {{ __('Reset Password') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
