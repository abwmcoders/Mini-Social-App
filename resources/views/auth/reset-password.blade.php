<x-layout>
    <h1 class="title">Reset your password</h1>
    <div class="mx-auto max-w-screen-sm card">
        <form action="{{ route('password.update')}}" method="POST">
            @csrf

            <input type="hidden" name="token" id="token" value="{{ $token }}">
            
            {{-- Email --}}
            <div class="mb-4">
                <label for="email">Email</label>
                <input type="text" name="email" value="{{ old('email') }}" class="input @error('email') ring-red-500 @enderror">
                @error('email') 
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            {{-- Password --}}
            <div class="mb-4">
                <label for="password">Password</label>
                <input type="password" name="password" class="input @error('password') ring-red-500 @enderror">
                @error('password') 
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            {{-- Confirm Password --}}
            <div class="mb-8">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="" class="input @error('password') ring-red-500 @enderror">
                @error('password') 
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            {{--Submit button--}}
            <button class="btn">Reseyt Password</button>
        </form>
    </div>
</x-layout>