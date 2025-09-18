@extends('layouts.app')

@section('content')
    <div class="login-container">
        @if ($errors->any())
            <div class="error-messages">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <a href="{{ route('auth.login') }}" target="_blank" rel="noopener">Login</a>
        {{-- <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form> --}}
    </div>
@endsection
