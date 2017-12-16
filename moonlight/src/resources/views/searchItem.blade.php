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
                        <div class="link active">
                            {!! $links[$property->getName()] !!}
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="search-form-params">
                    <div class="row">
                        @foreach ($properties as $property)
                        <div class="block">
                            <div class="close"><i class="fa fa-minus-square-o"></i></div>
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
        <div class="item active">
            <ul class="header">
                <li class="h2"><span>Служебный раздел</span></li>
                <li class="total">
                <span class="order-toggler">Всего 4 элемента</span>
                </li>
            </ul>
            <div class="buttons">
                <div class="button save enabled"><i class="fa fa-floppy-o"></i>Сохранить</div>
                <div class="button copy enabled"><i class="fa fa-clone"></i>Копировать</div>
                <div class="button move enabled"><i class="fa fa-arrow-right"></i>Перенести</div>
                <div class="button delete enabled"><i class="fa fa-trash-o"></i>Удалить</div>
            </div>
            <table class="elements">
                <thead>
                    <tr>
                        <th class="browse"><i class="fa fa-sort"></i></th>
                        <th><a href>Название</a></th>
                        <th class="date"><a href>Создано</a></th>
                        <th class="check"><div class="check"></div></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="browse"><a href="browse.html"><i class="fa fa-angle-right"></i></a></td>
                        <td class="name"><a href="edit.html"><i class="fa fa-pencil"></i><span>Ученики</span></a></td>
                        <td class="date">
                        <div class="date">11.07.2017</div>
                        <div class="time">16:08:43</div>
                        </td>
                        <td class="check"><div class="check"></div></td>
                    </tr>
                    <tr>
                        <td class="browse"><a href="browse.html"><i class="fa fa-angle-right"></i></a></td>
                        <td class="name"><a href="edit.html"><i class="fa fa-pencil"></i><span>Предметы</span></a></td>
                        <td class="date">
                        <div class="date">11.07.2017</div>
                        <div class="time">16:08:43</div>
                        </td>
                        <td class="check"><div class="check"></div></td>
                    </tr>
                    <tr>
                        <td class="browse"><a href="browse.html"><i class="fa fa-angle-right"></i></a></td>
                        <td class="name"><a href="edit.html"><i class="fa fa-pencil"></i><span>Справочники</span></a></td>
                        <td class="date">
                        <div class="date">11.07.2017</div>
                        <div class="time">16:08:43</div>
                        </td>
                        <td class="check"><div class="check"></div></td>
                    </tr>
                    <tr>
                        <td class="browse"><a href="browse.html"><i class="fa fa-angle-right"></i></a></td>
                        <td class="name"><a href="edit.html"><i class="fa fa-pencil"></i><span>Загрузка тестов</span></a></td>
                        <td class="date">
                        <div class="date">11.07.2017</div>
                        <div class="time">16:08:43</div>
                        </td>
                        <td class="check"><div class="check"></div></td>
                    </tr>
                </tbody>
            </table>
        </div>
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