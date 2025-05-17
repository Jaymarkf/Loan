
<body class="flex items-center justify-center min-h-screen bg-white">
    <div class="w-full max-w-sm p-8 border border-gray-200 rounded-xl shadow-md">
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center">
                <span class="text-gray-500 text-lg font-semibold">LOGO</span>
            </div>
        </div>
        @if (session('message'))
            <div class="mb-4 rounded-lg bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 px-4 py-3 shadow-md" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.366-.446.957-.446 1.323 0l6.518 7.945c.457.557.052 1.456-.661 1.456H3.923c-.713 0-1.118-.899-.661-1.456l6.518-7.945zM11 13a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('message') }}</span>
                </div>
            </div>
        @endif
        <h2 class="text-2xl font-semibold text-gray-800 text-center">Welcome Back</h2>
        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-600">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email" class="w-full p-3 rounded-lg border border-gray-300 focus:ring focus:ring-gray-400 outline-none">
                @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm text-gray-600">Password</label>
                <input type="password" name="password" required placeholder="Enter your password" class="w-full p-3 rounded-lg border border-gray-300 focus:ring focus:ring-gray-400 outline-none">
                @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full py-3 bg-gray-800 text-white font-semibold rounded-lg hover:bg-gray-700 transition">Login</button>
        </form>
        <p class="text-center text-gray-500 text-sm mt-4">Don't have an account? <a href="#" class="underline">Sign up</a></p>
    </div>
</body>