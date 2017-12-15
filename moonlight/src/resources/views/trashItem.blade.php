@extends('moonlight::base')

@section('title', 'Корзина')

@section('css')
<link media="all" type="text/css" rel="stylesheet" href="/packages/moonlight/touch/css/browse.css">
@endsection

@section('js')
<script src="/packages/moonlight/touch/js/search.js"></script>
<script>
var homeUrl = '{{ route('home') }}';
var elementsUrl = '{{ route('search.list') }}';
var searchUrl = '{{ route('search') }}';
var deleteUrl = '{{ route('elements.delete.force') }}';
var restoreUrl = '{{ route('elements.restore') }}';
var autocompleteUrl = '{{ route('elements.autocomplete') }}';
var title = '@yield('title')';
var itemName = '{{ $currentItem->getNameId() }}';
</script>
@endsection

@section('body')
<nav>
    <left><span class="hamburger"><span class="glyphicons glyphicons-menu-hamburger"></span></span></left>
    <center><a href="{{ route('home') }}">@yield('title')</a></center>
    <right><a href="{{ route('search') }}"><span class="glyphicons glyphicons-search"></span></a></right>
</nav>
<div class="sidebar">
    <div class="sidebar-container">
        <ul class="menu">
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
<div class="bottom-context-menu">
    <div class="button restore"><span class="halflings halflings-arrow-left"></span><br>Восстановить</div>
    <div class="button delete"><span class="halflings halflings-ban-circle"></span><br>Удалить</div>
</div>
<div class="confirm restore">
    <div class="container">
        <div class="content">
            Восстановить элементы?
        </div>
        <div class="buttons">
            <input type="button" value="Выполнить" class="btn restore">
            <input type="button" value="Отмена" class="btn cancel">
        </div>
    </div>
</div>
<div class="confirm delete">
    <div class="container">
        <div class="content">
            Удалить окончательно?
        </div>
        <div class="buttons">
            <input type="button" value="Удалить" class="btn danger delete">
            <input type="button" value="Отмена" class="btn cancel">
        </div>
    </div>
</div>
<div class="main">
    <div class="path">
        <a href="{{ route('trash') }}">Корзина</a>
        <span class="halflings halflings-menu-right"></span>
    </div>
    <h2>{{ $currentItem->getTitle() }}</h2>
    <form>
        <input type="hidden" name="action" value="search">
        <input type="submit" class="phantom">
        <div class="browse-form">
            <div class="right"><span id="form-toggler" class="icon"><span class="glyphicons glyphicons-adjust-alt"></span></span></div>
            <input type="hidden" name="search_id" value="">
            <input type="text" name="search" placeholder="ID или {{ mb_strtolower($mainProperty->getTitle()) }}">
            <span class="submit-button"><span class="halflings halflings-menu-right"></span></span>
            <span name="search_auto" class="autocomplete-container"></span>
        </div>
        @if ($properties)
        <div id="form-container" class="dnone browse-form-params">
            <div class="row">
                @foreach ($properties as $property)
                    @if ($view = $property->getSearchView())
                    <div class="block">
                    {!! $view !!}
                    </div>
                    @endif
                @endforeach
            </div>
            <div class="row">
                <input type="submit" value="Найти" class="btn">
            </div>
        </div>
        @endif
    </form>
    @if ($elementsView)
    <div class="list-container">{!! $elementsView !!}</div>
    @endif
</div>
@endsection