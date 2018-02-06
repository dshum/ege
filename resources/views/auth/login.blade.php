@extends('layout')

@section('title')
Авторизация
@stop

@section('content')
@isset ($error)
<div class="error">{{ $error }}</div>
@endisset
<form action="{{ route('login') }}" method="post">
    {{ csrf_field() }}
    <div class="row">
        <label>E-mail:</label><br>
        <input type="text" name="email" value="{{ $email }}">
    </div>
    <div class="row">
        <label>Пароль:</label><br>
        <input type="password" name="password" value="{{ $password }}"><br>
        <a href="{{ route('password.request') }}">Забыли пароль?</a>
    </div>
    <div class="row">
        <input type="checkbox" id="remember" name="remember" value="1"{{ $remember ? ' checked' : '' }}>
        <label for="remember">Запомнить меня</label>
    </div>
    <div class="row submit">
        <input type="submit" value="Войти" class="btn">
    </div>
</form>
@stop