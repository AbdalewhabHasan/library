<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'لوحة تحكم الأدمن')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ... كل كود الـ CSS الخاص بك يبقى هنا ... */
    </style>
</head>
<body>
    <div class="sticky-navbar">
        {{-- ... كل كود شريط التنقل العلوي يبقى هنا ... --}}
    </div>

    <div class="container">
        @yield('content' )
    </div>
</body>
</html>
