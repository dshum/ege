@extends('moonlight::layouts.home')

@section('title', 'Moonlight')

@section('css')
@endsection

@section('js')
@endsection

@section('body')
<div class="wide">
    <div class="container">
        <div class="block-elements">
            <h2>Статистика</h2>
            <ul class="elements">
                <li><a href="home-browse.html">Выручка</a></li>
                <li><a href="home-browse.html">Выручка по способам оплаты</a></li>
                <li><a href="home-browse.html">Расходы</a></li>
                <li><a href="">Счета и акты</a></li>
                <li><a href="">Акты сверки</a></li>
                <li><a href="">Скорректированные акты</a></li>
                <li><a href="">Сводная таблица услуг, пополнений и актов</a></li>
                <li><a href="">Пользователи с актами за последний год</a></li>
                <li><a href="">Черная книга бухгалтера</a></li>
            </ul>
        </div>
        <div class="block-elements">
            <h2>Разделы сайта</h2>
            <ul class="elements">
                <li><a href="browse-section.html">Агентства недвижимости</a></li>
                <li><a href="">Застройщики</a></li>
            </ul>
        </div>
        <div class="block-elements">
            <h2>Прочее</h2>
            <ul class="elements">
                <li><a href="">Расходы</a></li>
                <li><a href="">Синхрофазотрон</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection