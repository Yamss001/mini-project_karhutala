@extends('layouts.auth')

@section('title', 'Login')

@section('content')

    <h1>Login Karhutala</h1>
    <p>Masuk untuk mengakses aplikasi.</p>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('login.store') }}" method="POST">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
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
            <label>
                <input
                    type="checkbox"
                    name="remember"
                    value="1"
                    style="width: auto;"
                >
                Ingat saya
            </label>
        </div>

        <button type="submit">Login</button>
    </form>

    <div class="link">
        Belum punya akun?
        <a href="{{ route('register') }}">Registrasi</a>
    </div>

@endsection
