<div class="leaf welcome">
    <div class="row">
        @foreach ($users as $index => $user)
        <div class="elements">
            <div class="h2"><a href="{{ route('moonlight.browse.element', $user['classId']) }}"><span>{{ $user['name'] }}</span></a></div>
            <div><small>{{ $user['email'] }}</small></div>
            @if (! $user['activated'])
            <div><small class="red">Не активирован</small></div>
            @endif
            <ul>
                @if (! $user['tests'])
                <li>Тесты еще не выполнялись.</li>
                @else
                @foreach ($user['tests'] as $userTest)
                <li>
                    <a href="{{ route('moonlight.browse.element', $userTest['classId']) }}">{{ $userTest['name'] }}</a>,
                    <small>{{ $userTest['created_at'] }}</small>,
                    <span class="correct">{{ $userTest['correct'] }}</span> /
                    <span class="incorrect">{{ $userTest['incorrect'] }}</span> /
                    {{ $userTest['total'] }},
                    {{ $userTest['percent'] }}%
                </li>
                @endforeach
                @endif
            </ul>
        </div>
        @if ($index % 2 == 1)
    </div>
    <div class="row">
        @endif
        @endforeach
    </div>
</div>