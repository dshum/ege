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
            <div class="part"><span>Поиск</span></div>
        </div>
        <div class="leaf">
            <input type="text" id="filter" placeholder="Название">
            <ul class="items">
                @foreach ($items as $item)
                <li><a href="{{ route('moonlight.search.item', $item->getNameId()) }}">{{ $item->getTitle() }}</a><br><small>{{ $item->getNameId() }}</small></li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection