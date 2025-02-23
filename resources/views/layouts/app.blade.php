<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Listrik Pasar Cipanas')</title>

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- ✅ Google Font (Roboto) -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>

    <!-- ✅ FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100 text-gray-900 font-roboto">

    <div class="container mx-auto mt-6">
        <!-- ✅ Notifikasi Alert -->
        @if(session('error'))
            <div class="alert alert-danger flex items-center p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i> 
                <span>{{ session('error') }}</span>
                <button type="button" class="ml-auto bg-transparent text-red-700 hover:text-red-900 focus:outline-none" data-bs-dismiss="alert">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <!-- ✅ Tempat Konten -->
        @yield('content')
    </div>

    <!-- ✅ Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
