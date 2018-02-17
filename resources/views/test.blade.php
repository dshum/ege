@extends('layout')

@section('title')
{{ $test->name }}
@stop

@section('content')
<h2>{{ $test->name }}</h2>

@if ($userTest && $userTest->complete)

@foreach ($questions as $k => $question)
    @if (isset($questionAnswered[$question->id]))
    <div class="question complete {{ $questionAnswered[$question->id]->correct ? 'correct' : 'incorrect' }}">
        <h3>Вопрос {{$k + 1}}</h3>
        {!! $question->question !!}
        @foreach ($answers[$question->id] as $answer)
            @if ($answer->correct && isset($answerChecked[$answer->id]))
                <div class="answer correct"><i class="fa fa-check"></i>{!! $answer->answer !!}</div>
            @elseif ($answer->correct)
                <div class="answer correct"><i class="empty"></i>{!! $answer->answer !!}</div>
            @elseif (isset($answerChecked[$answer->id]))
                <div class="answer incorrect"><span class="fa fa-check"></span>{!! $answer->answer !!}</div>
            @else
                <div class="answer"><span class="empty"></span>{!! $answer->answer !!}</div>
            @endif
        @endforeach
    </div>
    @endif
@endforeach

@else

<form method="post">
{{ csrf_field() }}
@foreach ($questions as $k => $question)
    <div class="question">
        <h3>Вопрос {{$k + 1}}</h3>
        @if ($question->isSingle())
            {!! $question->question !!}
            @foreach ($answers[$question->id] as $answer)
            <div class="answer">
                <input type="radio" name="answers[{{ $question->id }}]" id="answer_{{ $answer->id }}" value="{{ $answer->id }}"{{ isset($answerChecked[$answer->id]) ? ' checked' : '' }}>
                <label for="answer_{{ $answer->id }}">{!! $answer->answer !!}</label>
            </div>
            @endforeach
        @elseif ($question->isMultiple())
            {!! $question->question !!}
            @foreach ($answers[$question->id] as $answer)
            <div class="answer">
                <input type="checkbox" name="answers[{{ $question->id }}][]" id="answer_{{ $answer->id }}" value="{{ $answer->id }}"{{ isset($answerChecked[$answer->id]) ? ' checked' : '' }}>
                <label for="answer_{{ $answer->id }}">{!! $answer->answer !!}</label>
            </div>
            @endforeach
        @else
            {!! $question->question !!}
        @endif
    </div>
@endforeach
    <div>
        <input type="submit" value="Завершить" class="btn">
    </div>
</form>

@endif
@stop