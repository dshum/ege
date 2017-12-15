@extends('moonlight::base')

@section('title', $element->{$currentItem->getMainProperty()})

@section('css')
<link media="all" type="text/css" rel="stylesheet" href="/packages/moonlight/touch/css/edit.css">
@endsection

@section('js')
<script src="/packages/moonlight/touch/js/jquery.autocomplete.min.js"></script>
<script src="/packages/moonlight/touch/js/edit.js"></script>
<script>
var copyUrl = '{{ route('element.copy', $element->getClassId()) }}';
var moveUrl = '{{ route('element.move', $element->getClassId()) }}';
var deleteUrl = '{{ route('element.delete', $element->getClassId()) }}';
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
<div class="bottom-context-menu">
    <div class="button copy"><span class="halflings halflings-duplicate"></span><br>Копировать</div>
    <div class="button move"><span class="halflings halflings-arrow-right"></span><br>Переместить</div>
    <div class="button delete"><span class="halflings halflings-trash"></span><br>Удалить</div>
</div>
<div class="confirm copy">
    <div class="container">
        <div class="content">
            @if (sizeof($ones))
            @foreach ($ones as $one)
                @if ($view = $one->getCopyView())
                <div id="{{ $one->getName() }}_one_container" property="{{ $one->getName() }}" class="row">{!! $view !!}</div>
                @endif
            @endforeach
            @else
            Копировать элемент?
            @endif
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
            @if (sizeof($ones))
            @foreach ($ones as $one)
                @if ($view = $one->getMoveView())
                <div id="{{ $one->getName() }}_one_container" property="{{ $one->getName() }}" class="row">{!! $view !!}</div>
                @endif
            @endforeach
            @else
            Перенести элемент?
            @endif
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
    <div id="options-toggler" class="right options"><span class="glyphicons glyphicons-option-horizontal"></span></div>
    <h2><a href="{{ route('browse.element', $element->getClassId()) }}">{{ $element->{$currentItem->getMainProperty()} }}</a></h2>
    <div class="edit-form">
        <form action="{{ route('element.save', $element->getClassId()) }}" autocomplete="off" method="POST">
            @if ($element->getHref())
            <div class="row">
                <a href="{{ $element->getHref() }}" target="_blank">Смотреть на сайте</a>
            </div>
            @endif
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