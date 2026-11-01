@extends('layouts.auth')

@section('title', 'Registrasi')

@section('content')

    <h1>Registrasi Pelapor</h1>
    <p>Buat akun untuk melaporkan indikasi kebakaran.</p>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('register.store') }}" method="POST">
        @csrf

        <div class="field">
            <label for="name">Nama</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                required
            >
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
            >
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
            >
        </div>

        <div class="field">
            <label for="password_confirmation">
                Konfirmasi Password
            </label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                required
            >
        </div>

        <button type="submit">Daftar</button>
    </form>

    <div class="link">
        Sudah punya akun?
        <a href="{{ route('login') }}">Login</a>
    </div>

@endsection
