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
        @if (\Moonlight\Main\Element::getClassId($element) == 'App.Topic.1')
        <div class="h2"><span>Объявления</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Жилые комплексы</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Коттеджные поселки</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Бизнес-центры</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Агентства недвижимости</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Застройщики</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Спрос</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Пользователи</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Справочники</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Баннеры</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Сообщения и т. д.</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Поиск повторных объявлений</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Рассылка</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Полезные утилиты</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Статистика</span></div>
        <ul class="elements">
            <li><a href="">Выручка</a></li>
            <li><a href="">Выручка по способам оплаты</a></li>
            <li><a href="">Выручка по клиентам</a></li>
            <li><a href="">Услуги</a></li>
            <li><a href="">Услуги по месяцам</a></li>
            <li><a href="">Тарифы</a></li>
            <li><a href="">Тарифы по месяцам</a></li>
            <li><a href="">Расходы</a></li>
            <li><a href="">Пользователи с достатком</a></li>
            <li><a href="">Счета и акты</a></li>
            <li><a href="">Почтовые адреса</a></li>
        </ul>
        <div class="h2"><span>WIKI: База знаний</span></div>
        <ul class="elements"></ul>
        <div class="h2"><span>Прочее</span></div>
        <ul class="elements">
            <li><a href="">Расходы</a></li>
            <li><a href="">Синхрофазотрон</a></li>
        </ul>
        @else
        <div class="elements">
            <div class="h2"><span>Ученики</span></div>
            <ul>
                <li><a href="">denis-shumeev@yandex.ru</a></li>
                <li><a href="">vegorova@mail.ru</a></li>
            </ul>
        </div>
        <div class="elements">
            <div class="h2"><span>Служебные разделы</span></div>
            <ul class="elements">
                <li><a href="">Ученики</a></li>
                <li><a href="">Предметы</a></li>
                <li><a href="">Справочники</a></li>
                <li><a href="">Загрузка тестов</a></li>
            </ul>
        </div>
        <div class="elements">
            <div class="h2"><span>Справочники</span></div>
        </div>
        <div class="elements">
            <div class="h2"><span>Предметы</span></div>
            <div class="tree">
                <div>
                    <div item>
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
        <div class="elements">
            <div class="h2"><span>Настройки сайта</span></div>
        </div>
        @endif
    </div>
</div>
@endsection