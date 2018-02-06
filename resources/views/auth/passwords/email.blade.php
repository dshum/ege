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
@if (session('status'))
    <div class="ok">
        {{ session('status') }}
    </div>
@endif
<form action="{{ route('password.email') }}" method="post">
    {{ csrf_field() }}
    <div class="row">
        <label>E-mail:</label><br>
        <input type="text" name="email" value="{{ old('email') }}">
    </div>
    <div class="row submit">
        <input type="submit" value="Отправить" class="btn">
    </div>
</form>
@endsection
