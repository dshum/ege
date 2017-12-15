@extends('moonlight::base')

@section('title', $element->{$currentItem->getMainProperty()})

@section('css')
<link media="all" type="text/css" rel="stylesheet" href="/packages/moonlight/touch/css/browse.css">
@endsection

@section('js')
<script src="/packages/moonlight/touch/js/element.js"></script>
<script>
var favoriteUrl = '{{ route('home.favorite') }}';
var favoritesUrl = '{{ route('home.favorites') }}';
var homeUrl = '{{ route('home') }}';
var searchUrl = '{{ route('search') }}';
var elementsUrl = '{{ route('elements.list') }}';
var countUrl = '{{ route('elements.count') }}';;
var copyUrl = '{{ route('elements.copy') }}';
var moveUrl = '{{ route('elements.move') }}';
var deleteUrl = '{{ route('elements.delete') }}';
var closeUrl = '{{ route('elements.close') }}';
var orderUrl = '{{ route('order') }}';
var title = '@yield('title')';
var opened = '{{ $open }}';
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
            <li><a href="{{ route('search') }}">Поиск</a></li>
            @if ($parent && $parent->getItem()->getNameId() != $currentItem->getNameId())
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
<div class="bottom-context-menu">
    <div class="button copy"><span class="halflings halflings-duplicate"></span><br>Копировать</div>
    <div class="button move"><span class="halflings halflings-arrow-right"></span><br>Переместить</div>
    <div class="button delete"><span class="halflings halflings-trash"></span><br>Удалить</div>
</div>
<div class="confirm favorite">
<div class="container">
    <div class="content">
        <p>Добавить на главную страницу?</p>
        <p>
            <input type="text" name="rubric" value="" placeholder="Рубрика">
            <span name="rubric_auto" class="autocomplete-container"></span>
        </p>
    </div>
    <div class="buttons">
        <input type="button" value="Добавить" class="btn ok">
        <input type="button" value="Отмена" class="btn cancel">
    </div>
</div>
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
    <div id="favorite-toggler" classId="{{ $element->getClassId() }}" {!! $favorite ? 'enabled="true" class="right options active"' : 'class="right options"' !!}><span class="glyphicons glyphicons-pushpin"></span></div>
    <h2>{{ $element->{$currentItem->getMainProperty()} }}</h2>
    <ul class="elements">
        <li>
            <div class="created">{{ $element->created_at->format('d.m.Y') }}<br><span class="time">{{ $element->created_at->format('H:i:s') }}</span></div>
            <div class="edit"><a href="{{ route('element.edit', $element->getClassId()) }}"><span class="halflings halflings-pencil"></span></a></div>
            @if ($element->getTouchListView())
            {!! $element->getTouchListView() !!}
            @else
            <div>{{ $currentItem->getTitle() }}</div>
            @endif
        </li>
    </ul>
    @if ($browsePluginView)
    <div class="plugin">
    {!! $browsePluginView !!}
    </div>
    @endif
    @if ($items)
    <ul class="items">
        @foreach ($items as $item)
            @if (isset($openedItem[$item->getNameId()]))
            <li item="{{ $item->getNameId() }}" classId="{{ $element->getClassId() }}" state="opened">
                <span class="a">{{ $item->getTitle() }}</span>
                <span class="total">{{ $openedItem[$item->getNameId()]['count'] }}</span><a class="addnew" href="{{ route('element.create', ['classId' => $element->getClassId(), 'item' => $item->getNameId()]) }}">+</a>
                <div item="{{ $item->getNameId() }}" class="list-container">{!! $openedItem[$item->getNameId()]['elements'] !!}</div>
            </li>
            @else
            <li item="{{ $item->getNameId() }}" classId="{{ $element->getClassId() }}" state="closed">
                <span class="a">{{ $item->getTitle() }}</span>
                <a class="addnew" href="{{ route('element.create', ['classId' => $element->getClassId(), 'item' => $item->getNameId()]) }}">+</a>
            </li>
            @endif
        @endforeach
    </ul>
    @else
    <div class="empty">Элементов не найдено.</div>
    @endif
</div>
@endsection