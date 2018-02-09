@extends('moonlight::layouts.home')

@section('title', 'Moonlight')

@section('css')
<link media="all" type="text/css" rel="stylesheet" href="/packages/moonlight/css/home.css">
@endsection

@section('js')
@endsection

@section('body')
<div class="main">
    <div class="container">
        @if ($homePluginView)
            {!! $homePluginView !!}
        @endif
        <div class="leaf">
            <div class="favorite-settings" title="Настройка"><a href=""><i class="fa fa-cog"></i></a></div>
            {!! $rubrics !!}
        </div>
    </div>
</div>
@endsection