@extends('layout')

@section('title')
{{ $test->name }}
@stop

@section('content')
<h2>{{ $test->name }}</h2>

@if ($userTest && $userTest->complete)

@foreach ($questions as $k => $question)
    @if (isset($questionAnswered[$question->id]))
    <div class="row complete {{ $questionAnswered[$question->id]->correct ? 'correct' : 'incorrect' }}">
        <h3>Вопрос {{$k + 1}}</h3>
        {!! $question->question !!}
        @foreach ($answers[$question->id] as $answer)
            @if ($answer->correct)
                <div class="correct-answer"><i class="fa fa-check"></i>{!! $answer->answer !!}</div>
            @elseif (isset($answerChecked[$answer->id]))
                <div class="incorrect-answer"><span class="empty"></span>{!! $answer->answer !!}</div>
            @else
                <div><span class="empty"></span>{!! $answer->answer !!}</div>
            @endif
        @endforeach
        <br>
    </div>
    @endif
@endforeach

@else

<form method="post">
{{ csrf_field() }}
@foreach ($questions as $k => $question)
    <div class="row">
        <h3>Вопрос {{$k + 1}}</h3>
        {!! $question->question !!}
        @foreach ($answers[$question->id] as $answer)
            <input type="radio" name="answers[{{ $question->id }}]" id="answer_{{ $answer->id }}" value="{{ $answer->id }}"{{ isset($answerChecked[$answer->id]) ? ' checked' : '' }}>
            <label for="answer_{{ $answer->id }}">{!! $answer->answer !!}</label><br>
        @endforeach
        <br>
    </div>
@endforeach
    <div class="row submit">
        <input type="submit" value="Завершить" class="btn">
    </div>
</form>

@endif
@stop