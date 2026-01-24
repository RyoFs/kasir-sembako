<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Kasir</title>

    {{-- Bootstrap Offline --}}
    <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    {{-- Font Awesome Offline --}}
    <link href="{{ asset('assets/fontawesome/css/all.min.css') }}" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #007bff 0%, #00bcd4 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            animation: fadeInBody 0.6s ease-in-out;
        }

        @keyframes fadeInBody {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInCard 0.8s ease-in-out forwards;
        }

        @keyframes fadeInCard {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    @yield('content')

    {{-- SweetAlert tetap offline karena sudah diinstall melalui Composer --}}
    @include('sweetalert::alert')

    {{-- Bootstrap JS Offline (opsional jika perlu modal/alert JS) --}}
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
