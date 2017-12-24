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
        <div class="plugin">
            {!! $homePluginView !!}
        </div>
        @endif
        <div class="leaf">
            {!! $rubrics !!}
        </div>
    </div>
</div>
@endsection