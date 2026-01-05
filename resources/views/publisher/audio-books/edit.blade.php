




@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg p-4 mb-5 bg-white rounded">
        <h2 class="card-title text-center mb-4 text-primary fw-bold">تعديل تفاصيل الكتاب الصوتي</h2>

        <form action="{{ route('publisher.audio-books.update', $audioBook->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label fw-semibold">العنوان</label>
                <input type="text" name="title" id="title" value="{{ old('title', $audioBook->title) }}" required
                       class="form-control @error('title') is-invalid @enderror">
                @error('title')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">الوصف</label>
                <textarea name="description" id="description" rows="4"
                          class="form-control @error('description') is-invalid @enderror">{{ old('description', $audioBook->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="duration" class="form-label fw-semibold">المدة (بالثواني)</label>
                <input type="number" name="duration" id="duration" value="{{ old('duration', $audioBook->duration) }}" required
                       class="form-control @error('duration') is-invalid @enderror">
                @error('duration')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="category" class="form-label fw-semibold">الفئة</label>
                <input type="text" name="category" id="category" value="{{ old('category', $audioBook->category) }}" required
                       class="form-control @error('category') is-invalid @enderror">
                @error('category')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="language" class="form-label fw-semibold">اللغة</label>
                <input type="text" name="language" id="language" value="{{ old('language', $audioBook->language) }}" required
                       class="form-control @error('language') is-invalid @enderror">
                @error('language')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="author" class="form-label fw-semibold">المؤلف</label>
                <input type="text" name="author" id="author" value="{{ old('author', $audioBook->author) }}"
                       class="form-control @error('author') is-invalid @enderror">
                @error('author')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="narrator" class="form-label fw-semibold">الراوي</label>
                <input type="text" name="narrator" id="narrator" value="{{ old('narrator', $audioBook->narrator) }}"
                       class="form-control @error('narrator') is-invalid @enderror">
                @error('narrator')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
<div class="mb-3">
                <label for="file" class="form-label fw-semibold">ملف الصوت <small class="text-muted">(حمّل ملف جديد للاستبدال)</small></label>
                <input type="file" name="file" id="file"
                       class="form-control @error('file') is-invalid @enderror">
                @error('file')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="cover_image" class="form-label fw-semibold">صورة الغلاف <small class="text-muted">(حمّل صورة جديدة للاستبدال)</small></label>
                <input type="file" name="cover_image" id="cover_image"
                       class="form-control @error('cover_image') is-invalid @enderror">
                @error('cover_image')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            @if($audioBook->cover_image)
                <div class="mb-3">
                    <label class="form-label fw-semibold">صورة الغلاف الحالية</label>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('storage/' . $audioBook->cover_image) }}" alt="صورة الغلاف الحالية"
                             class="img-thumbnail me-3" style="width: 128px; height: 128px; object-fit: cover;">
                        <small class="text-muted">حمّل صورة جديدة أعلاه لتغييرها.</small>
                    </div>
                </div>
            @endif

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg shadow-sm">تحديث الكتاب الصوتي</button>
            </div>
        </form>
    </div>
</div>
@endsection