@extends('moonlight::layouts.base')

@section('nav')
<nav>
    <div class="logo"><a href="{{ route('moonlight.home') }}">Moonlight</a></div>
    <ul class="menu">
        <li><a href="home-browse.html">Избранное</a></li>
        <li><a href="browse.html">Страницы</a></li>
        <li><a href="search.html">Поиск</a></li>
        <li><a href="trash.html">Корзина</a></li>
        <li><a href="{{ route('moonlight.groups') }}">Пользователи</a></li>
    </ul>
    <div class="avatar"><a href="{{ route('moonlight.profile') }}"><img src="/packages/moonlight/img/avatar.jpg"></a></div>
</nav>
@endsection