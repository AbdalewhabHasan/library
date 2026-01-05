@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center mb-4" style="font-size: 1.8rem;">Audio Books by {{ $publisher->name }}</h2>

    <!-- Subscribe/Unsubscribe Button -->
    <div class="text-center mb-4">
        @if(Auth::user()->subscriptions()->where('publisher_id', $publisher->id)->exists())
            <!-- Unsubscribe Button if already subscribed -->
            <form action="{{ route('listener.unsubscribe', $publisher->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm" style="font-size: 1rem;">
                    Unsubscribe from {{ $publisher->name }}
                </button>
            </form>
        @else
            <!-- Subscribe Button if not subscribed -->
            <form action="{{ route('listener.subscribe', $publisher->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm" style="font-size: 1rem;">
                    Subscribe to {{ $publisher->name }}
                </button>
            </form>
        @endif
    </div>

    <div class="row">
        @foreach($audioBooks as $audioBook)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <img src="{{ $audioBook->cover_image ? asset('storage/' . $audioBook->cover_image) : asset('images/audio-placeholder.png') }}"
                     class="card-img-top" alt="Audio Book Cover"
                     style="height: 200px; object-fit: cover;">

                <div class="card-body">
                    <h5 class="card-title text-truncate" style="font-size: 1.2rem;">{{ $audioBook->title }}</h5>
                    <p class="card-text text-muted" style="font-size: 1rem;">{{ $audioBook->description }}</p>
                    <div class="d-flex justify-content-between">
                        <span class="badge bg-info" style="font-size: 0.9rem;">{{ $audioBook->category }}</span>
                        <span class="badge bg-secondary" style="font-size: 0.9rem;">{{ $audioBook->language }}</span>
                    </div>
                    <small class="text-muted d-block mt-2" style="font-size: 0.9rem;">Author: {{ $audioBook->author }}</small>
                    <small class="text-muted" style="font-size: 0.9rem;">Narrator: {{ $audioBook->narrator }}</small>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-sm btn-primary play-button"
                            data-file="{{ Storage::url($audioBook->file_path) }}" style="font-size: 0.9rem;">
                        Play
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Audio Player -->
    <div id="audioPlayerContainer" class="mt-4" style="display: none;">
        <h5 style="font-size: 1.4rem;">Now Playing:</h5>
      <audio id="audio-player" controls style="width: 100%;">

            <source src="" type="audio/mpeg">
            Your browser does not support audio playback.
        </audio>
    </div>
</div>

<!-- JavaScript for Handling Play Button -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const playButtons = document.querySelectorAll('.play-button');
        const audioPlayerContainer = document.getElementById('audioPlayerContainer');
       const audioPlayer = document.getElementById('audio-player');


        playButtons.forEach(button => {
            button.addEventListener('click', () => {
                const fileUrl = button.getAttribute('data-file');
                if (fileUrl) {
                    audioPlayer.src = fileUrl;
                    audioPlayerContainer.style.display = 'block';
                    audioPlayer.play();
                }
            });
        });
    });
</script>
@endsection
