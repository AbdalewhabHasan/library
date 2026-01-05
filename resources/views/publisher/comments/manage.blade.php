<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة التعليقات</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6a11cb;
            --secondary-color: #2575fc;
            --dark-bg: #1a202c;
            --card-bg: #2d3748;
            --text-primary: #f7fafc;
            --text-secondary: #a0aec0;
            --danger-color: #e53e3e;
            --shadow-light: rgba(255, 255, 255, 0.05 );
        }
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-primary);
            margin: 0;
            padding: 2rem;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 1rem;
        }
        .page-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--card-bg);
            color: var(--text-primary);
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background-color: var(--primary-color);
            transform: translateY(-2px);
        }
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-weight: 500;
            border-left: 4px solid;
        }
        .alert-success {
            background-color: rgba(45, 206, 137, 0.1);
            color: #2dce89;
            border-color: #2dce89;
        }
        .comment-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            overflow: hidden;
            border: 1px solid var(--shadow-light);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .comment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        .card-body {
            padding: 1.5rem;
        }
        .card-title {
            font-weight: 700;
            font-size: 1.2rem;
        }
        .card-subtitle {
            font-size: 0.9rem;
        }
        .card-subtitle a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        .card-text {
            margin-top: 1rem;
            font-size: 1rem;
            color: var(--text-secondary);
            line-height: 1.8;
        }
        .btn-danger {
            background-color: var(--danger-color);
            border: none;
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }
        .card-footer {
            background-color: rgba(0,0,0,0.2);
            padding: 0.75rem 1.5rem;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }
        .pagination .page-link {
            background-color: var(--card-bg);
            border-color: var(--shadow-light);
            color: var(--text-primary);
        }
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="page-header">
            <h1><i class="fas fa-comments"></i> إدارة التعليقات</h1>
            <a href="{{ route('publisher.dashboard') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> العودة للوحة التحكم
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @forelse ($comments as $comment)
            <div class="card comment-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">{{ $comment->user->name }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">
                                على كتاب: <a href="#">{{ $comment->audioBook->title }}</a>
                            </h6>
                            <p class="card-text">"{{ $comment->comment }}"</p>
                        </div>
                        <div class="align-self-start">
                            <form action="{{ route('publisher.comments.delete', $comment->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا التعليق؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="حذف التعليق">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <i class="fas fa-clock"></i> {{ $comment->created_at->diffForHumans() }}
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">
                لا توجد أي تعليقات على كتبك حتى الآن.
            </div>
        @endforelse

        {{-- لعرض أزرار التنقل بين الصفحات بتنسيق Bootstrap --}}
        @if ($comments->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $comments->links() }}
            </div>
        @endif
    </div>
</body>
</html>
