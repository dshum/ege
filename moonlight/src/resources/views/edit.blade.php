@extends('moonlight::layouts.browse')

@section('title', $element->$mainProperty)

@section('css')
<link media="all" type="text/css" rel="stylesheet" href="/packages/moonlight/css/edit.css">
@endsection

@section('js')
<script src="/packages/moonlight/js/tinymce/js/tinymce/tinymce.min.js"></script>
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
            @if ($itemPluginView)
                {!! $itemPluginView !!}
            @endif
            <ul class="header">
                <li class="h2"><span>Редактирование элемента типа &laquo;{{ $currentItem->getTitle() }}&raquo;</span></li>
            </ul>
            <div class="buttons">
                <div class="button save enabled"><i class="fa fa-floppy-o"></i>Сохранить</div>
                <div class="button copy enabled"><i class="fa fa-clone"></i>Копировать</div>
                @if ($movePropertyView)
                <div class="button move enabled"><i class="fa fa-arrow-right"></i>Перенести</div>
                @else
                <div class="button move"><i class="fa fa-arrow-right"></i>Перенести</div>
                @endif
                <div class="button delete enabled"><i class="fa fa-trash-o"></i>Удалить</div>
            </div>
            <form action="{{ route('moonlight.element.save', $classId) }}" method="POST">
                <div class="edit">
                    @foreach ($views as $name => $view)
                    <div class="row" name="{{ $name }}">
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
<div class="confirm" id="copy">
    <div class="container">
        <div class="content">
            <div>Куда копируем?</div>
            <div class="edit" radiogroup="copy">
                <div class="row">
                    @if ($copyPropertyView)
                    {!! $copyPropertyView !!}
                    @else
                    {{ $parentElement ? $parentElement['name'] : 'Корень сайта' }}
                    @endif
                </div>
            </div>
        </div>
        <div class="bottom">
            <input type="button" value="Скопировать" class="btn copy" url="{{ route('moonlight.element.copy', $classId) }}">
            <input type="button" value="Отмена" class="btn cancel">
        </div>
    </div>
</div>
@if ($movePropertyView)
<div class="confirm" id="move">
    <div class="container">
        <div class="content">
            <div>Куда переносим?</div>
            <div class="edit">
                <div class="row">
                    {!! $movePropertyView !!}
                </div>
            </div>
        </div>
        <div class="bottom">
            <input type="button" value="Перенести" class="btn move" url="{{ route('moonlight.element.move', $classId) }}">
            <input type="button" value="Отмена" class="btn cancel">
        </div>
    </div>
</div>
@endif
<div class="confirm" id="delete">
    <div class="container">
        <div class="content">
            Удалить элемент &laquo;{{ $element->$mainProperty }}&raquo;?
        </div>
        <div class="bottom">
            <input type="button" value="Удалить" class="btn danger remove" url="{{ route('moonlight.element.delete', $classId) }}">
            <input type="button" value="Отмена" class="btn cancel">
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