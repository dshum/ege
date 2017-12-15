@extends('moonlight::base')

@section('title', 'Поиск')

@section('css')
<link media="all" type="text/css" rel="stylesheet" href="/packages/moonlight/touch/css/browse.css">
@endsection

@section('js')
<script src="/packages/moonlight/touch/js/search.js"></script>
<script>
var homeUrl = '{{ route('home') }}';
var elementsUrl = '{{ route('search.list') }}';
var searchUrl = '{{ route('search') }}';
var copyUrl = '{{ route('elements.copy') }}';
var moveUrl = '{{ route('elements.move') }}';
var deleteUrl = '{{ route('elements.delete') }}';
var closeUrl = '{{ route('elements.close') }}';
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
    <div class="button copy"><span class="halflings halflings-duplicate"></span><br>Копировать</div>
    <div class="button move"><span class="halflings halflings-arrow-right"></span><br>Переместить</div>
    <div class="button delete"><span class="halflings halflings-trash"></span><br>Удалить</div>
</div>
<div class="confirm copy">
    <div class="container">
        <div class="content">
        {!! $onesCopy !!}
        </div>
        <div class="buttons">
            <input type="button" value="Копировать" class="btn copy">
            <input type="button" value="Отмена" class="btn cancel">
        </div>
    </div>
</div>
<div class="confirm move">
    <div class="container">
        <div class="content">
        {!! $onesMove !!}
        </div>
        <div class="buttons">
            <input type="button" value="Перенести" class="btn move">
            <input type="button" value="Отмена" class="btn cancel">
        </div>
    </div>
</div>
<div class="confirm delete">
    <div class="container">
        <div class="content">
            Удалить в корзину?
        </div>
        <div class="buttons">
            <input type="button" value="Удалить" class="btn danger delete">
            <input type="button" value="Отмена" class="btn cancel">
        </div>
    </div>
</div>
<div class="main">
    <div class="path">
        <a href="{{ route('search') }}">Поиск</a>
        <span class="halflings halflings-menu-right"></span>
    </div>
    <h2>{{ $currentItem->getTitle() }}<a class="addnew" href="{{ route('element.create', ['classId' => 'root', 'item' => $currentItem->getNameId()]) }}">+</a></h2>
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
                <div class="block">
                    <div class="label order" property="sort"><span class="halflings halflings-sort"></span><span>Сортировать по полю:</span></div>
                    <div container="property" property="sort" class="{{ $sort ? '' : 'dnone' }}">
                        <select name="sort"{{ $sort ? '' : ' disabled="disabled"' }}>
                            @foreach ($orderProperties as $property)
                            <option value="{{ $property->getName() }}"{{ $sort == $property->getName() ? ' selected' : '' }}>{{ $property->getTitle() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
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