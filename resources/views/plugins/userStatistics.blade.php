@if (sizeof($statistics))
<div class="statistics">
    <ul>
        @foreach ($statistics as $userTest)
        <li>
            @if ($userTest['complete'])
            <a href="{{ route('moonlight.browse.element', $userTest['classId']) }}"><b>{{ $userTest['name'] }}</b></a>,
            @else
            <a href="{{ route('moonlight.browse.element', $userTest['classId']) }}">{{ $userTest['name'] }}</a>,
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