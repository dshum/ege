@if (sizeof($statistics))
<div class="statistics">
    <ul>
        @foreach ($statistics as $userTest)
        <li>
            @if ($userTest['complete'])
            <a href="{{ route('moonlight.browse.element', $userTest['classId']) }}"><b>{{ $userTest['name'] }}</b></a>,
            {{ $userTest['complete_at'] }},
            @else
            <a href="{{ route('moonlight.browse.element', $userTest['classId']) }}">{{ $userTest['name'] }}</a>,
            {{ $userTest['created_at'] }},
            @endif
            <span class="correct">{{ $userTest['correct'] }}</span> /
            <span class="incorrect">{{ $userTest['incorrect'] }}</span> /
            {{ $userTest['total'] }},
            {{ $userTest['percent'] }}%
        </li>
        @endforeach
    </ul>
</div>
@endif