@extends('moonlight::layouts.base')

@section('nav')
<nav>
    <div class="logo"><a href="{{ route('moonlight.home') }}">Moonlight</a></div>
    <ul class="menu">
        <li class="active"><a href="{{ route('moonlight.home') }}">Избранное</a></li>
        <li><a href="{{ route('moonlight.browse') }}">Страницы</a></li>
        <li><a href="{{ route('moonlight.search') }}">Поиск</a></li>
        <li><a href="{{ route('moonlight.trash') }}">Корзина</a></li>
        <li><a href="{{ route('moonlight.groups') }}">Пользователи</a></li>
    </ul>
    <div class="avatar"><a href="{{ route('moonlight.profile') }}"><img src="/packages/moonlight/img/avatar.jpg"></a></div>
</nav>
@endsection