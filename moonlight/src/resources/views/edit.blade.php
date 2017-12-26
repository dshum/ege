@extends('moonlight::layouts.browse')

@section('title', $element->$mainProperty)

@section('css')
<link media="all" type="text/css" rel="stylesheet" href="/packages/moonlight/css/edit.css">
@endsection

@section('js')
<script src="/packages/moonlight/js/edit.js"></script>
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
            <div class="part"><a href="{{ route('moonlight.browse.element', $classId) }}">{{ $element->$mainProperty }}</a></div>
        </div>
        <div class="item active">
            <ul class="header">
                <li class="h2"><span>Редактирование элемента типа &laquo;{{ $currentItem->getTitle() }}&raquo;</span></li>
            </ul>
            <div class="buttons">
                <div class="button save enabled"><i class="fa fa-floppy-o"></i>Сохранить</div>
                <div class="button copy enabled"><i class="fa fa-clone"></i>Копировать</div>
                <div class="button move enabled"><i class="fa fa-arrow-right"></i>Перенести</div>
                <div class="button delete enabled"><i class="fa fa-trash-o"></i>Удалить</div>
            </div>
            <form action="{{ route('moonlight.element.save', $classId) }}" method="POST">
                <div class="edit">
                    @foreach ($views as $view)
                    <div class="row">
                        {!! $view !!}
                    </div>
                    @endforeach
                    <div class="row submit">
                        <input type="submit" value="Сохранить" class="btn">
                    </div>
                </div>
            </form>
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