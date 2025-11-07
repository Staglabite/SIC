<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Personel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 min-h-screen">
    <div class="container mx-auto p-10">
        <div class="bg-white rounded-3xl shadow-2xl p-12 text-center">
            <h1 class="text-6xl font-bold text-green-800 mb-6">PERSONEL</h1>
            <div class="bg-green-100 p-8 rounded-2xl inline-block">
                <p class="text-3xl">Selamat Datang,</p>
                <p class="text-4xl font-bold text-green-900">{{ $user->name }}</p>
                <p class="text-2xl mt-4">NRP: <span class="font-mono bg-white px-4 py-2 rounded">{{ $user->nrp }}</span></p>
                <p class="text-xl">Pangkat: {{ $user->pangkat }} | {{ $user->jabatan }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="mt-10">
                @csrf
                <button class="bg-red-600 hover:bg-red-700 text-white text-xl font-bold py-4 px-12 rounded-xl">
                    LOGOUT
                </button>
            </form>
        </div>
    </div>
</body>
</html>