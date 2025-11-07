<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN SICIP - POLRI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Fade In Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 1s ease-out forwards;
        }

        /* Bubble Background */
        .bubbles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .bubble {
            position: absolute;
            bottom: -100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: rise 10s infinite ease-in;
        }

        @keyframes rise {
            0% {
                transform: translateY(0) scale(1);
                opacity: 0;
            }
            10% { opacity: 1; }
            100% {
                transform: translateY(-110vh) scale(1.5);
                opacity: 0;
            }
        }

        /* Variasi ukuran & delay animasi */
        .bubble:nth-child(1) { width: 40px; height: 40px; left: 20%; animation-delay: 0s; }
        .bubble:nth-child(2) { width: 60px; height: 60px; left: 40%; animation-delay: 2s; animation-duration: 12s; }
        .bubble:nth-child(3) { width: 20px; height: 20px; left: 60%; animation-delay: 4s; animation-duration: 8s; }
        .bubble:nth-child(4) { width: 80px; height: 80px; left: 80%; animation-delay: 1s; animation-duration: 14s; }
        .bubble:nth-child(5) { width: 25px; height: 25px; left: 30%; animation-delay: 3s; animation-duration: 9s; }
        .bubble:nth-child(6) { width: 50px; height: 50px; left: 70%; animation-delay: 5s; animation-duration: 11s; }
        .bubble:nth-child(7) { width: 15px; height: 15px; left: 10%; animation-delay: 6s; animation-duration: 7s; }
        .bubble:nth-child(8) { width: 70px; height: 70px; left: 50%; animation-delay: 7s; animation-duration: 13s; }
        .bubble:nth-child(9) { width: 30px; height: 30px; left: 90%; animation-delay: 2s; animation-duration: 10s; }
        .bubble:nth-child(10){ width: 40px; height: 40px; left: 15%; animation-delay: 4s; animation-duration: 12s; }
    </style>
</head>
<body class="h-full flex items-center justify-center relative overflow-hidden">

    <!-- Bubble Background -->
    <div class="bubbles">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <!-- LOGIN CARD -->
    <div class="w-full max-w-md fade-in">
        <div class="bg-white rounded-2xl shadow-2xl p-10">
            <div class="text-center mb-8 fade-in" style="animation-delay:0.2s">
                <h1 class="text-5xl font-bold text-blue-900 mb-2">SICIP</h1>
                <p class="text-gray-600 text-lg">Sistem Informasi Cuti & Izin Personel</p>
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
                        class="w-full px-5 py-4 text-lg border-2 border-gray-300 rounded-xl focus:border-blue-600 focus:outline-none transition-all"
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
                        class="w-full px-5 py-4 text-lg border-2 border-gray-300 rounded-xl focus:border-blue-600 focus:outline-none transition-all"
                    >
                </div>

                <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-bold text-xl py-4 rounded-xl transform hover:scale-105 transition-all duration-200 shadow-lg">
                    MASUK
                </button>
            </form>

            <div class="mt-8 bg-gray-100 p-6 rounded-xl text-sm fade-in" style="animation-delay:0.6s">
                <p class="font-bold text-blue-900 mb-3 text-center">CONTOH LOGIN:</p>
                <div class="space-y-2 text-gray-700">
                    <p><span class="font-mono bg-white px-3 py-1 rounded">1234567890123456</span> → Personel (pass: personel123)</p>
                    <p><span class="font-mono bg-white px-3 py-1 rounded">renmin01</span> → Renmin (pass: renmin123)</p>
                    <p><span class="font-mono bg-white px-3 py-1 rounded">pimpinan01</span> → Pimpinan (pass: pimpinan123)</p>
                </div>
            </div>

            <p class="text-center text-xs text-gray-500 mt-8 fade-in" style="animation-delay:0.8s">
                © 2025 POLRI - Sistem Informasi Cuti & Izin Personel
            </p>
        </div>
    </div>

</body>
</html>
