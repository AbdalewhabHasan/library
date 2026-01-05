{{-- ====================================================================== --}}
{{-- ==   صفحة لوحة التحكم الرئيسية للناشر                                == --}}
{{-- ==   File: resources/views/publisher/dashboard.blade.php            == --}}
{{-- ====================================================================== --}}

{{-- 1. أخبر هذه الصفحة أنها ترث من ليآوت الناشر الموحد --}}
@extends('layouts.publisher')

{{-- 2. حدد عنوان الصفحة --}}
@section('title', 'لوحة تحكم الناشر')

{{-- 3. أضف الـ CSS الخاص بهذه الصفحة فقط --}}
@push('styles')
<style>
    /* هذا الـ CSS خاص فقط بصفحة لوحة التحكم */
    .dashboard-card { background: var(--card-bg); backdrop-filter: blur(20px); border: 1px solid var(--card-border); border-radius: 25px; box-shadow: var(--shadow-light); overflow: hidden; transition: all 0.4s ease; position: relative; }
    .card-header { background: var(--primary-gradient); color: white; font-size: 2rem; font-weight: 700; padding: 2rem; text-align: center; }
    .card-body { padding: 3rem; }
    .welcome-text { text-align: center; color: var(--text-muted); font-size: 1.3rem; margin-bottom: 2rem; font-weight: 400; }
    .stats-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; }
    .stat-card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 15px; padding: 1.5rem; text-align: center; transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-light); }
    .stat-number { font-size: 2.5rem; font-weight: 900; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .stat-label { color: var(--text-muted); font-weight: 600; margin-top: 0.5rem; }
    .charts-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; margin-bottom: 3rem; }
    .chart-container { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 15px; padding: 1.5rem; transition: all 0.3s ease; }
    .chart-container h3 { font-size: 1.2rem; font-weight: 700; color: var(--text-color); text-align: center; margin-bottom: 1.5rem; }
    .action-buttons { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem; }
    .action-btn { display: flex; align-items: center; justify-content: center; padding: 2rem; border: none; border-radius: 20px; font-weight: 700; font-size: 1.2rem; text-decoration: none; color: white; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: var(--shadow-heavy); }
    .btn-add { background: var(--success-gradient); } .btn-view { background: var(--info-gradient); } .btn-all { background: var(--warning-gradient); }
</style>
@endpush

{{-- 4. هذا هو المحتوى الخاص بلوحة التحكم فقط --}}
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="dashboard-card">
                <div class="card-header"><h1><i class="fas fa-tachometer-alt me-3"></i>لوحة تحكم الناشر</h1></div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success text-center">{{ session('success') }}</div>
                    @endif
                    <div class="welcome-text">مرحباً بك في لوحة تحكم الناشر. من هنا يمكنك إدارة كتبك الصوتية بسهولة وفعالية</div>

                    <div class="stats-container">
                        <div class="stat-card"><div class="stat-number">{{ $totalBooks ?? 0 }}</div><div class="stat-label">إجمالي الكتب</div></div>
                        <div class="stat-card"><div class="stat-number">{{ $approvedBooks ?? 0 }}</div><div class="stat-label">الكتب المقبولة</div></div>
                        <div class="stat-card"><div class="stat-number">{{ $pendingBooks ?? 0 }}</div><div class="stat-label">كتب قيد المراجعة</div></div>
                        <div class="stat-card"><div class="stat-number">{{ $totalSubscribers ?? 0 }}</div><div class="stat-label">إجمالي المشتركين</div></div>
                    </div>

                    <div class="charts-grid">
                        <div class="chart-container"><h3>حالة الكتب</h3><canvas id="statusChart"></canvas></div>
                        <div class="chart-container"><h3>نمو المشتركين (آخر 7 أيام)</h3><canvas id="growthChart"></canvas></div>
                    </div>
                  {{-- ▼▼▼ استبدل قسم الأزرار القديم بهذا القسم الجديد والصحيح ▼▼▼ --}}
<div class="action-buttons">
    {{-- هذا الزر يعمل بالفعل --}}
    <a href="{{ route('publisher.audio-books.create') }}" class="action-btn btn-add">
        <span>إضافة كتاب صوتي جديد</span><i class="fas fa-plus-circle"></i>
    </a>

    {{-- 1. زر عرض كتب الناشر الخاصة --}}
    <a href="{{ route('publisher.audio-books.index') }}" class="action-btn btn-view">
        <span>عرض كتبي الصوتية</span><i class="fas fa-headphones"></i>
    </a>

    {{-- 2. زر عرض كل الكتب في النظام (للمستخدمين) --}}
    <a href="{{ route('audio-books.all') }}" class="action-btn btn-all">
        <span>استعراض جميع الكتب</span><i class="fas fa-book-open"></i>
    </a>
  {{-- ▼▼▼ هذا هو الزر الجديد بتصميم متناسق ▼▼▼ --}}
<a href="{{ route('publisher.comments.manage') }}" class="btn btn-info" style="background: linear-gradient(135deg, #805ad5, #6b46c1); color: white;">
    <i class="fas fa-comments"></i> إدارة التعليقات
</a>


</div>
{{-- ▲▲▲ انتهى القسم الجديد ▲▲▲ --}}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- 5. أضف الـ JavaScript الخاص بالمخططات البيانية فقط --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function getChartColors() {
            const theme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            return {
                color: theme === 'dark' ? 'rgba(255, 255, 255, 0.8)' : '#1a202c',
                borderColor: theme === 'dark' ? 'rgba(255, 255, 255, 0.2)' : '#e2e8f0',
            };
        }

        function createCharts() {
            const colors = getChartColors();
            Chart.defaults.color = colors.color;
            Chart.defaults.borderColor = colors.borderColor;
            Chart.defaults.font.family = 'Cairo';
            Chart.defaults.font.weight = '600';

            if (window.statusChartInstance) window.statusChartInstance.destroy();
            if (window.growthChartInstance) window.growthChartInstance.destroy();

            const statusCtx = document.getElementById('statusChart').getContext('2d');
            window.statusChartInstance = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($statusChartLabels ?? []),
                    datasets: [{
                        data: @json($statusChartValues ?? []),
                        backgroundColor: ['rgba(75, 192, 192, 0.7)', 'rgba(255, 206, 86, 0.7)', 'rgba(255, 99, 132, 0.7)'],
                        borderColor: ['#4bc0c0', '#ffce56', '#ff6384'],
                        borderWidth: 2,
                        hoverOffset: 8
                    }]
                },
                options: { responsive: true, plugins: { legend: { position: 'top' } } }
            });

            const growthCtx = document.getElementById('growthChart').getContext('2d');
            window.growthChartInstance = new Chart(growthCtx, {
                type: 'line',
                data: {
                    labels: @json($growthChartLabels ?? []),
                    datasets: [{
                        label: 'مشتركون جدد',
                        data: @json($growthChartValues ?? []),
                        backgroundColor: 'rgba(102, 126, 234, 0.2)',
                        borderColor: 'rgba(102, 126, 234, 1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
            });
        }

        createCharts();
        document.addEventListener('themeChanged', createCharts);
    });
</script>
@endpush
