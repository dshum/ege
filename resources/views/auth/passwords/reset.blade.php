@extends('layout')

@section('title')
Сброс пароля
@endsection

@section('content')
@if ($errors->any())
    <div class="error">
    @foreach ($errors->all() as $message)
    <div>{{ $message }}</div>
    @endforeach
    </div>
@endif
<form action="{{ route('password.request') }}" method="post">
    {{ csrf_field() }}
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="row">
        <label>E-mail:</label><br>
        <input type="text" name="email" value="{{ $email or old('email') }}">
    </div>
    <div class="row">
        <label>Новый пароль:</label><br>
        <input type="password" name="password">
    </div>
    <div class="row">
        <label>Подтверждение:</label><br>
        <input type="password" name="password_confirmation">
    </div>
    <div class="row submit">
        <input type="submit" value="Сохранить" class="btn">
    </div>
</form>
@endsection
