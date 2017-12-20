<div class="answers">
    @foreach ($answers as $index => $answer)
    <div answer="{{ $answer->id }}" class="answer {{ $answer->correct ? 'correct' : '' }}"><b>{{ $index + 1 }})</b> {{ $answer->answer }}</div>
    @endforeach
</div>
