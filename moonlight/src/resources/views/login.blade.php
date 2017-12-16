@extends('moonlight::layouts.small')

@section('title', 'Moonlight')

@section('content')
<div class="login">
    <div class="path">
        Вход
    </div>
    <div class="block">
        @if (isset($message))
        <div class="error">{{ $message }}</div>
        @endif
        <form action="{{route('moonlight.login')}}" autocomplete="off" method="POST">
            {{ csrf_field() }}
            <div class="row">
                <label>Логин</label><br>
                <input type="text" name="login" value="{{ $login or null }}" placeholder="Логин">
            </div>
            <div class="row">
                <label>Пароль</label><br>
                <input type="password" name="password" placeholder="Пароль"><br>
                <a href="{{ route('moonlight.restore') }}">Забыли пароль?</a>
            </div>
            <div class="row">
                <input type="submit" value="Войти" class="btn">
            </div>
        </form>
    </div>
</div>
@endsection