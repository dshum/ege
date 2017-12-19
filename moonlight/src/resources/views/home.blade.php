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
            <h2>Ученики</h2>
            <ul class="elements">
                <li><a href="">denis-shumeev@yandex.ru</a></li>
                <li><a href="">vegorova@mail.ru</a></li>
            </ul>
        </div>
        <div class="block-elements">
            <h2>Тесты</h2>
            <ul class="elements">
                <li><a href="">Покори Воробьевы горы 2014-1</a></li>
                <li><a href="">Покори Воробьевы горы 2014-2</a></li>
                <li><a href="">Покори Воробьевы горы 2016</a></li>
            </ul>
        </div>
        <div class="block-elements">
            <h2>Прочее</h2>
            <ul class="elements">
                <li><a href="">Загрузка тестов</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection