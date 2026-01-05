<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنجازاتي</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #2d3748, #1a202c );
            color: #f7fafc;
            margin: 0;
            padding: 2rem;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #9f7aea;
        }
        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        .achievement-card {
            background: #4a5568;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            opacity: 0.5;
        }
        .achievement-card.unlocked {
            opacity: 1;
            background: #2d3748;
            border-color: #9f7aea;
            box-shadow: 0 0 20px rgba(159, 122, 234, 0.4);
        }
        .achievement-card .icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            color: #cbd5e0;
            transition: all 0.3s ease;
        }
        .achievement-card.unlocked .icon {
            color: #f6ad55;
            transform: scale(1.1);
        }
        .achievement-card h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .achievement-card p {
            font-size: 0.9rem;
            color: #a0aec0;
            margin-bottom: 1rem;
        }
        .achievement-card.unlocked p {
            color: #e2e8f0;
        }
        .unlocked-date {
            font-size: 0.8rem;
            font-weight: 500;
            color: #9f7aea;
        }
        .back-link {
            display: inline-block;
            margin-top: 3rem;
            text-align: center;
            width: 100%;
            color: #cbd5e0;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            color: #9f7aea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-trophy"></i> إنجازاتك</h1>
        </div>

        <div class="achievements-grid">
            @foreach ($allAchievements as $achievement)
                @php
                    $isUnlocked = $unlockedAchievements->has($achievement->id);
                @endphp
                <div class="achievement-card {{ $isUnlocked ? 'unlocked' : '' }}">
                    <div class="icon">
                        <i class="{{ $achievement->icon }}"></i>
                    </div>
                    <h3>{{ $achievement->name }}</h3>
                    <p>{{ $achievement->description }}</p>
                 {{-- ▼▼▼ هذا هو الكود الصحيح والنهائي ▼▼▼ --}}
@if ($isUnlocked && $unlockedAchievements[$achievement->id]->pivot)
    <div class="unlocked-date">
        تم الحصول عليه في: {{ \Carbon\Carbon::parse($unlockedAchievements[$achievement->id]->pivot->unlocked_at)->format('Y-m-d') }}
    </div>
@endif

                </div>
            @endforeach
        </div>
        <a href="{{ route('listener.dashboard') }}" class="back-link">العودة إلى لوحة التحكم</a>
    </div>
</body>
</html>
