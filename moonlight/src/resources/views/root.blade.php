@extends('moonlight::layouts.browse')

@section('title', 'Корень сайта')

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
            <div class="part"><span>Корень сайта</span></div>
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
        <div classId="" item="{{ $item['id'] }}"></div>
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
        <h2>Ученики</h2>
        <ul class="elements">
            <li><a href="">denis-shumeev@yandex.ru</a></li>
            <li><a href="">vegorova@mail.ru</a></li>
        </ul>
        <h2>Тесты</h2>
        <ul class="elements">
            <li><a href="">Покори Воробьевы горы 2014-1</a></li>
            <li><a href="">Покори Воробьевы горы 2014-2</a></li>
            <li><a href="">Покори Воробьевы горы 2016</a></li>
        </ul>
        <h2>Прочее</h2>
        <ul class="elements">
            <li><a href="">Загрузка тестов</a></li>
        </ul>
        <div class="tree">
            <div>
                <div item>
                    <div class="item">Служебный раздел</div>
                    <div class="margin">
                        <div class="plus"><i class="fa fa-caret-right"></i></div>
                        <span><a href="browse.html">Ученики</a></span>
                    </div>
                    <div class="margin">
                        <div class="plus"><i class="fa fa-caret-right"></i></div>
                        <span><a href="browse.html">Предметы</a></span>
                        <div>
                            <div class="padding">
                                <div item>
                                    <div class="item">Предмет</div>
                                    <div class="margin">
                                        <div class="plus"><i class="fa fa-caret-down"></i></div>
                                        <span><a href="browse.html">ЕГЭ</a></span>
                                        <div>
                                            <div class="padding">
                                                <div item>
                                                    <div class="item">Тема</div>
                                                    <div class="margin">
                                                        <div class="plus"><i class="fa fa-caret-down"></i></div>
                                                        <span><a href="browse.html">Олимпиады</a></span>
                                                        <div>
                                                            <div class="padding">
                                                                <div item>
                                                                    <div class="item">Тест</div>
                                                                    <div class="margin">
                                                                    <div v-else class="empty"></div>
                                                                    <span><a href="browse.html" class="active">Олимпиада 2012 Константин Константинопольский</a></span>
                                                                    </div>
                                                                    <div class="margin">
                                                                    <div v-else class="empty"></div>
                                                                    <span><a href="browse.html">Покори Воробьевы горы 2014-1</a></span>
                                                                    </div>
                                                                    <div class="margin">
                                                                    <div v-else class="empty"></div>
                                                                    <span><a href="browse.html">Покори Воробьевы горы 2014-2</a></span>
                                                                    </div>
                                                                    <div class="margin">
                                                                    <div v-else class="empty"></div>
                                                                    <span><a href="browse.html">Покори Воробьевы горы 2015-1</a></span>
                                                                    </div>
                                                                    <div class="margin">
                                                                    <div v-else class="empty"></div>
                                                                    <span><a href="browse.html">Покори Воробьевы горы 2015-7</a></span>
                                                                    </div>
                                                                    <div class="margin">
                                                                    <div v-else class="empty"></div>
                                                                    <span><a href="browse.html">Покори Воробьевы горы 2016</a></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="margin">
                        <div class="plus"><i class="fa fa-caret-right"></i></div>
                        <span><a href="browse.html">Справочники</a></span>
                    </div>
                    <div class="margin">
                        <div v-else class="empty"></div>
                        <span><a href="browse.html">Загрузка тестов</a></span>
                        <div>
                            <div class="padding">
                            
                            </div>
                        </div>
                    </div>
                </div>
                <div item>
                    <div class="item">Настройки сайта</div>
                    <div class="margin">
                        <div class="empty"></div>
                        <span><a href="browse.html">Настройки сайта</a></span>
                        <div>
                            <div class="padding">
                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection