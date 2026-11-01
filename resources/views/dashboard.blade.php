@extends('layouts.auth')

@section('title', 'Dashboard')

@section('content')

    <h1>Dashboard Karhutala</h1>

    <p>
        Selamat datang, {{ auth()->user()->name }}.
    </p>

    <p>
        Role:
        <strong>{{ auth()->user()->role }}</strong>
    </p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf

        <button type="submit">Logout</button>
    </form>

@endsection
