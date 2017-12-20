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
        <div class="add-element">
            Добавить: <a href="">Раздел сайта</a>,<a href="">Служебный раздел</a>,<a href="">Настройки сайта</a>
        </div>
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
        <div class="item active">
            <ul class="header">
                <li class="h2"><span>Настройки сайта</span></li>
                <li class="total">
                    <span class="order-toggler">Всего 1 элемент</span>
                </li>
            </ul>
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