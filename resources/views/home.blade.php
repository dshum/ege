@extends('layout')

@section('title')
Тесты ЕГЭ по биологии
@endsection

@section('content')
@isset ($register)
<div class="ok">Вы успешно зарегистрировались!</div>
@endisset

<h1>Результаты</h1>

@if (sizeof($userTests))
    @foreach ($userTests as $userTest)
    <p>
    <a href="{{ route('test', $userTest->test_id) }}">{{ $userTest->name }}</a><br>
    {{ $userTest->created_at->format('d.m.Y H:i:s') }}<br>
    @if ($userTest->complete)Завершен@elseВ процессе@endif
    </p>
    @endforeach
@else
    <p>Тесты еще не выполнялись.</p>
@endif
@endsection