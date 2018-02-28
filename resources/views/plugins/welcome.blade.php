@if (sizeof($recent))
<div class="leaf welcome">
    <h2>Последние завершенные тесты</h2>
    <ul>
        @foreach ($recent as $userTest)
        <li>
            <a href="{{ route('moonlight.browse.element', $userTest['user']['classId']) }}"><span>{{ $userTest['user']['name'] }}</span></a>,
            <a href="{{ route('moonlight.browse.element', $userTest['classId']) }}">{{ $userTest['name'] }}</a>,
            @if ($userTest['complete_at'])
            {{ $userTest['complete_at'] }},
            @endif
            {{ $userTest['created_at'] }},
            <span class="correct">{{ $userTest['correct'] }}</span> /
            <span class="incorrect">{{ $userTest['incorrect'] }}</span> /
            {{ $userTest['total'] }},
            {{ $userTest['percent'] }}%
        </li>
        @endforeach
    </ul>
</div>
@endif