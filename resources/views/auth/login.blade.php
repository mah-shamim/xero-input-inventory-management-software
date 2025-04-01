@extends('layouts.guestApp')
@section('content')
    <div class="tw-container tw-mx-auto tw-w-2/3">
        @include('homepagePartials.navbar')
        <div class="tw-flex tw-items-center tw-justify-center">
            <form method="POST" class="tw-w-full tw-max-w-sm tw-bg-white tw-shadow-md tw-rounded-lg tw-px-8 tw-pt-6 tw-pb-8 tw-mb-4" action="{{ route('login') }}">
                @csrf
                <div class="tw-mb-4">
                    <label class="tw-block tw-text-gray-700 tw-text-sm tw-font-bold tw-mb-2" for="email">
                        Email
                    </label>
                    <input
                            dusk="email"
                            label="email"
                            name="email"
                            class="tw-shadow tw-appearance-none tw-border tw-rounded tw-w-full tw-py-2 tw-px-3 tw-text-gray-700 tw-leading-tight focus:tw-outline-none focus:tw-shadow-outline"
                            type="email"
                            value="{{ old('email') }}"
                    />
                    @error('email')
                    <p class="tw-text-red-500 tw-text-xs tw-italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="tw-mb-6">
                    <label class="tw-block tw-text-gray-700 tw-text-sm tw-font-bold tw-mb-2" for="password">
                        Password
                    </label>
                    <input
                            id="password"
                            dusk="password"
                            label="Password"
                            name="password"
                            class="tw-shadow tw-appearance-none tw-border tw-rounded tw-w-full tw-py-2 tw-px-3 tw-text-gray-700 tw-leading-tight focus:tw-outline-none focus:tw-shadow-outline"
                            type="password"
                    />
                    @error('password')
                    <p class="tw-text-red-500 tw-text-xs tw-italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="tw-flex tw-items-center tw-justify-between">
                    <button class="tw-bg-blue-500 hover:tw-bg-blue-700 tw-text-white tw-font-bold tw-py-2 tw-px-4 tw-rounded focus:tw-outline-none focus:tw-shadow-outline" type="submit" dusk="submit">
                        Login
                    </button>
                    <a href="/forgot-password">Forget Password?</a>
                </div>
            </form>
        </div>
    </div>

@endsection
@section('style-login')
    <style>

    </style>
@endsection
