@extends('moonlight::base')

@section('title', 'Новый элемент')

@section('css')
<link media="all" type="text/css" rel="stylesheet" href="/packages/moonlight/touch/css/edit.css">
@endsection

@section('js')
<script src="/packages/moonlight/touch/js/jquery.autocomplete.min.js"></script>
<script src="/packages/moonlight/touch/js/edit.js"></script>
<script>
var historyUrl = '{{ $history }}';
</script>
@endsection

@section('body')
<nav>
    <left><a href="{{ $history }}"><span class="halflings halflings-menu-left"></span></a></left>
    <center><a href="{{ route('home') }}">@yield('title')</a></center>
    <right><a href="{{ route('search') }}"><span class="glyphicons glyphicons-search"></span></a></right>
</nav>
<div class="sidebar">
    <div class="sidebar-container">
        <ul class="menu">
            <li><a href="{{ route('search') }}">Поиск</a></li>
            @if ($parent)
            <li><a href="{{ route('search.item', $parent->getItem()->getNameId()) }}">{{ $parent->getItem()->getTitle() }}</a></li>
            @endif
            <li><a href="{{ route('search.item', $currentItem->getNameId()) }}">{{ $currentItem->getTitle() }}</a></li>
            <li><hr></li>
            <li><a href="{{ route('browse') }}">Корень сайта</a></li>
            <li><a href="{{ route('trash') }}">Корзина</a></li>
            <li><hr></li>
            <li><a href="{{ route('users') }}">Пользователи</a></li>
            <li><a href="{{ route('log') }}">Журнал</a></li>
            <li><hr></li>
            <li><a href="{{ route('profile') }}">{{ $loggedUser->first_name }} {{ $loggedUser->last_name }}</a></li>
            <li><a href="{{ route('logout') }}">Выход</a></li>
        </ul>
    </div>
</div>
<div class="main">
    @if ($parent)
    <div class="path">
        <a href="{{ route('browse') }}">Корень сайта</a>
        <span class="halflings halflings-menu-right"></span>
        <a href="{{ route('browse.element', $parent->getClassId()) }}">{{ $parent->name }}</a>
        <span class="halflings halflings-menu-right"></span></div>
    @else
    <div class="path">
        <a href="{{ route('browse') }}">Корень сайта</a>
        <span class="halflings halflings-menu-right"></span>
    </div>
    @endif
    <h2>{{ $currentItem->getTitle() }}</h2>
    <div class="edit-form">
        <form action="{{ route('element.add', $currentItem->getNameId()) }}" autocomplete="off" method="POST">
            @foreach ($properties as $property)
                @if ($view = $property->getEditView())
                <div id="{{ $property->getName() }}_container" property="{{ $property->getName() }}" class="row">{!! $view !!}</div>
                @endif
            @endforeach
            <div class="row submit">
                <input type="submit" value="Сохранить" class="btn">
            </div>
        </form>
      </div>
</div>
@endsection