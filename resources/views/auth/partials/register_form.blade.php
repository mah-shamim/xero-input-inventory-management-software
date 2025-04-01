<form class="tw-w-full xi-tw-form" method="POST" action="{{ route('register') }}">
    @csrf
    <div class="tw-flex tw-flex-wrap">
        <div class="tw-w-full md:tw-w-1/3">
            <label for="title">Title</label>
        </div>
        <div class="tw-w-full md:tw-w-2/3">
            <select name="title" id="title"
                    dusk="title"
                    class="tw-block tw-w-full tw-border tw-bg-gray-100 @error('title') tw-border-red-500 @enderror">
                <option value="">Select Title</option>
                <option value="Mr" {{ old('title') == 'Mr' ? 'selected' : '' }}>Mr</option>
                <option value="Mrs" {{ old('title') == 'Mrs' ? 'selected' : '' }}>Mrs</option>
            </select>
            @error('title')
            <p class="tw-text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="tw-flex tw-flex-wrap">
        <div class="tw-w-full md:tw-w-1/3">
            <label for="name">Name</label>
        </div>
        <div class="tw-w-full md:tw-w-2/3">
            <input dusk="name" name="name" id="name" type="text" value="{{ old('name') }}"
                   class="tw-block tw-w-full tw-border tw-bg-gray-100 @error('name') tw-border-red-500 @enderror"/>
            @error('name')
            <p class="tw-text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="tw-flex tw-flex-wrap">
        <div class="tw-w-full md:tw-w-1/3">
            <label for="company">Company Name</label>
        </div>
        <div class="tw-w-full md:tw-w-2/3">
            <input dusk="company" name="company" id="company" type="text" value="{{ old('company') }}"
                   class="tw-block tw-w-full tw-border tw-bg-gray-100 @error('company') tw-border-red-500 @enderror"/>
            @error('company')
            <p class="tw-text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="tw-flex tw-flex-wrap">
        <div class="tw-w-full md:tw-w-1/3">
            <label for="email">Email</label>
        </div>
        <div class="tw-w-full md:tw-w-2/3">
            <input dusk="email" name="email" id="email" type="email" value="{{ old('email') }}"
                   class="tw-block tw-w-full tw-border tw-bg-gray-100 @error('email') tw-border-red-500 @enderror"/>
            @error('email')
            <p class="tw-text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="tw-flex tw-flex-wrap">
        <div class="tw-w-full md:tw-w-1/3">
            <label for="contact_phone">Phone</label>
        </div>
        <div class="tw-w-full md:tw-w-2/3">
            <input dusk="contact_phone" name="contact_phone" id="contact_phone" type="text" value="{{ old('contact_phone') }}"
                   class="tw-block tw-w-full tw-border tw-bg-gray-100 @error('contact_phone') tw-border-red-500 @enderror"/>
            @error('contact_phone')
            <p class="tw-text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="tw-flex tw-flex-wrap">
        <div class="tw-w-full md:tw-w-1/3">
            <label for="address1">Address</label>
        </div>
        <div class="tw-w-full md:tw-w-2/3">
            <input dusk="address1" name="address1" id="address1" type="text" value="{{ old('address1') }}"
                   class="tw-block tw-w-full tw-border tw-bg-gray-100 @error('address1') tw-border-red-500 @enderror"/>
            @error('address1')
            <p class="tw-text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="tw-flex tw-flex-wrap">
        <div class="tw-w-full md:tw-w-1/3">
            <label for="password">Password</label>
        </div>
        <div class="tw-w-full md:tw-w-2/3">
            <input dusk="password" type="password" name="password" id="password"
                   class="tw-block tw-w-full tw-border tw-bg-gray-100 @error('password') tw-border-red-500 @enderror"/>
            @error('password')
            <p class="tw-text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="tw-flex tw-flex-wrap">
        <div class="tw-w-full md:tw-w-1/3">
            <label for="password_confirmation">Password Confirmation</label>
        </div>
        <div class="tw-w-full md:tw-w-2/3">
            <input dusk="password_confirmation" type="password" name="password_confirmation" id="password_confirmation"
                   class="tw-block tw-w-full tw-border tw-bg-gray-100 @error('password_confirmation') tw-border-red-500 @enderror"/>
            @error('password_confirmation')
            <p class="tw-text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="tw-flex tw-flex-wrap">
        <div class="tw-w-full">
            <button dusk="submit" type="submit"
                    class="tw-bg-blue-500 hover:tw-bg-blue-700 tw-text-white tw-font-bold tw-py-2 tw-px-4 tw-rounded tw-mt-4">
                Submit
            </button>
        </div>
    </div>
</form>