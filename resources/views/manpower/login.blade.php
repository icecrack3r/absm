<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Manpower</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-center mb-6">Login Absensi</h2>
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('manpower.login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        NIP / Email
                    </label>
                    <input type="text" name="login" value="{{ old('login') }}" 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Password
                    </label>
                    <input type="password" name="password" 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                </div>
                
                <button type="submit" 
                        class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 focus:outline-none">
                    Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>