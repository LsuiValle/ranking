@extends('layouts.app')

@section('content')
<div class="container">
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="game_name" class="form-label">Nombre de Invocador</label>
            <input type="text" class="form-control" id="game_name" name="game_name" required>
        </div>
        <div class="mb-3">
            <label for="tag_line" class="form-label">Tag</label>
            <input type="text" class="form-control" id="tag_line" name="tag_line" required>
        </div>
        <button type="submit" class="btn btn-primary">Ingresar</button>
    </form>
</div>
@endsection
