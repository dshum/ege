@extends('moonlight::layouts.search')

@section('title', 'Поиск')

@section('css')
<link media="all" type="text/css" rel="stylesheet" href="/packages/moonlight/css/search.css">
@endsection

@section('js')
<script src="/packages/moonlight/js/search.js"></script>
@endsection

@section('body')
<div class="main">
    <div class="container">
        <div class="path">
            <div class="part"><a href="{{ route('moonlight.search') }}">Поиск</a></div>
            <div class="divider">/</div>
            <div class="part"><span>{{ $currentItem->getTitle() }}</span></div>
        </div>
        <form>
            <input type="hidden" name="action" value="search">
            <div class="search-form">
                <div class="search-form-links">
                    <div class="row">
                        @foreach ($properties as $property)
                        <div class="link {{ isset($actives[$property->getName()]) ? 'active' : '' }}" item="{{ $currentItem->getNameId() }}" name="{{ $property->getName() }}">
                            {!! $links[$property->getName()] !!}
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="search-form-params">
                    <div class="row">
                        @foreach ($properties as $property)
                        <div class="block {{ isset($actives[$property->getName()]) ? 'active' : '' }}" name="{{ $property->getName() }}">
                            <div class="close" item="{{ $currentItem->getNameId() }}" name="{{ $property->getName() }}"><i class="fa fa-minus-square-o"></i></div>
                            {!! $views[$property->getName()] !!}
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="row-submit">
                    <input type="submit" value="Найти" class="btn">
                </div>
            </div>
        </form>
        @if ($action == 'search')
        {!! $elements !!}
        @endif
    </div>
</div>
@endsection

@section('sidebar')
<div class="sidebar">
    <div class="container">
        <input type="text" id="filter" placeholder="Название">
        <ul class="items">
            @foreach ($items as $item)
            <li class="{{ $item->getNameId() == $currentItem->getNameId() ? 'active' : '' }}"><a href="{{ route('moonlight.search.item', $item->getNameId()) }}">{{ $item->getTitle() }}</a><br><small>{{ $item->getNameId() }}</small></li>
            @endforeach
        </ul>
    </div>
</div>
@endsection