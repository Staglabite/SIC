<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN SICIP - POLRI</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="h-full flex items-center justify-center relative overflow-hidden">

    <!-- LOGIN CARD -->
    <div class="w-full max-w-md fade-in">
        <div class="bg-white rounded-2xl shadow-2xl p-10">
            <div class="text-center mb-8 fade-in" style="animation-delay:0.2s">
                <div class="flex justify-center mb-4">
                    <img src="/storage/LogoPolda.png" alt="Logo Polda" class="h-20">
                </div>
                <h1 class="text-5xl font-bold text-yellow-900 mb-2">SIC</h1>
                <p class="text-gray-600 text-lg">Sistem Izin & Cuti Polda Jawa Tengah</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6 fade-in" style="animation-delay:0.4s">
                @csrf
                <div>
                    <input 
                        type="text" 
                        name="identifier" 
                        value="{{ old('identifier') }}" 
                        required 
                        autofocus
                        placeholder="NRP atau Username"
                        class="w-full px-5 py-4 text-lg border-2 border-gray-300 rounded-xl focus:border-yellow-900 focus:outline-none transition-all"
                    >
                    @error('identifier')
                        <p class="text-red-600 text-sm mt-2 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <input 
                        type="password" 
                        name="password" 
                        required
                        placeholder="Password"
                        class="w-full px-5 py-4 text-lg border-2 border-gray-300 rounded-xl focus:border-yellow-900 focus:outline-none transition-all"
                    >
                </div>

                <button type="submit" class="w-full bg-yellow-900 hover:bg-yellow-950 text-white font-semibold text-l py-4 rounded-xl transform hover:scale-105 transition-all duration-200 shadow-lg">
                    Login
                </button>
            </form>

            <p class="text-center text-xs text-gray-500 mt-8 fade-in" style="animation-delay:0.8s">
                © 2025 POLRI - Sistem Informasi Cuti & Izin Personel
            </p>
        </div>
    </div>

</body>
</html>
