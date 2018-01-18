@extends('moonlight::layouts.base')

@section('nav')
<nav>
    <div class="logo"><a href="{{ route('moonlight.home') }}">Moonlight</a></div>
    <ul class="menu">
        <li><a href="{{ route('moonlight.browse') }}">Страницы</a></li>
        <li><a href="{{ route('moonlight.search') }}">Поиск</a></li>
        <li class="trash active"><a href="{{ route('moonlight.trash') }}">Корзина</a></li>
        <li><a href="{{ route('moonlight.users') }}">Пользователи</a></li>
    </ul>
    <div class="avatar">
        @if ($loggedUser->photoExists())
        <a href="{{ route('moonlight.profile') }}"><img src="{{ $loggedUser->getPhotoSrc() }}"></a>
        @else
        <a href="{{ route('moonlight.profile') }}"><img src="/packages/moonlight/img/avatar.png"></a>
        @endif
    </div>
</nav>
@endsection