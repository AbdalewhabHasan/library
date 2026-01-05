@extends('layouts.publisher')

@section('title', 'الإعدادات العامة')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">الإعدادات العامة</h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> الرجوع إلى لوحة التحكم
    </a>
</div>


    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">تعديل إعدادات الموقع</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- حقل اسم الموقع -->
                <div class="form-group mb-3">
                    <label for="site_name">اسم الموقع</label>
                    <input type="text" name="site_name" id="site_name" class="form-control" value="{{ $settings['site_name'] ?? '' }}">
                </div>

                <!-- حقل وصف الموقع -->
                <div class="form-group mb-3">
                    <label for="site_description">وصف الموقع (للسيو SEO)</label>
                    <textarea name="site_description" id="site_description" class="form-control" rows="3">{{ $settings['site_description'] ?? '' }}</textarea>
                </div>

                <!-- حقل لوجو الموقع -->
                <div class="form-group mb-4">
                    <label for="site_logo">لوجو الموقع</label>
                    <input type="file" name="site_logo" id="site_logo" class="form-control">

                    {{-- عرض اللوجو الحالي إذا كان موجوداً --}}
                    @if(isset($settings['site_logo']) && $settings['site_logo'])
                        <div class="mt-3">
                            <small>اللوجو الحالي:</small>

                            <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Site Logo" style="max-height: 80px; border-radius: 5px; background: #f8f9fa; padding: 5px;">
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ الإعدادات
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
