@extends('moonlight::layouts.small')

@section('title', 'Moonlight')

@section('js')
<script>
  $(function() {
    $('[name="login"]').val('{{ $login or null }}');
  });
</script>
@endsection

@section('content')
<div class="login">
  <div class="container">
    <div class="path">
      Вход
    </div>
    <div class="block">
      @if (isset($message))
      <div class="error">{{ $message }}</div>
      @endif
      <form action="{{route('moonlight.login')}}" autocomplete="off" method="POST">
        {{ csrf_field() }}
        <div class="row"><input type="text" name="login" placeholder="Логин"></div>
        <div class="row"><input type="password" name="password" placeholder="Пароль"></div>
        <div class="row"><input type="submit" value="Войти" class="btn"></div>
      </form>
    </div>
    <div><a href="/restore">Забыли пароль?</a></div>
  </div>
</div>
@endsection