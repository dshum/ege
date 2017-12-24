@extends('moonlight::layouts.browse')

@section('title', $element->$mainProperty)

@section('css')
<link media="all" type="text/css" rel="stylesheet" href="/packages/moonlight/css/browse.css">
@endsection

@section('js')
<script src="/packages/moonlight/js/browse.js"></script>
@endsection

@section('body')
<div class="main">
    <div class="container">
        <div class="path">
            <div class="part"><a href="{{ route('moonlight.browse.root') }}">Корень сайта</a></div>
            <div class="divider">/</div>
            @foreach ($parents as $parent)
            <div class="part"><a href="{{ route('moonlight.browse.element', $parent['classId']) }}">{{ $parent['name'] }}</a></div>
            <div class="divider">/</div>
            @endforeach
            <div class="part"><span>{{ $element->$mainProperty }}</span><a href="" class="edit"><i class="fa fa-pencil"></i></a></div>
        </div>
        @if ($creates)
        <div class="add-element">
            Добавить:
            @foreach ($creates as $index => $create)
            <a href="">{{ $create['name'] }}</a>{{ $index < sizeof($creates) - 1 ? ',' : '' }}
            @endforeach
        </div>
        @endif
        @foreach ($items as $item)
        <div classId="{{ \Moonlight\Main\Element::getClassId($element) }}" item="{{ $item['id'] }}"></div>
        @endforeach
        <div class="empty {{ sizeof($items) > 0 ? 'dnone' : '' }}">
            <div>Элементов не найдено.</div>
            <div><b>¯\_(ツ)_/¯</b></div>
        </div>
    </div>
</div>
@endsection

@section('sidebar')
<div class="sidebar">
    <div class="container">
        {!! $rubrics !!}
    </div>
</div>
@endsection