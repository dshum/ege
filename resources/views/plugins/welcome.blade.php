<style>
.wrapper {
    width: 60rem;
    margin: 1rem 0 1rem 0;
    padding: 0;
}

.wrapper small {
    color: #357;
    font-size: 0.8rem;
}

.wrapper .h2 a {
    color: royalblue;
}

.wrapper .correct {
    font-weight: bold;
    color: green;
}

.wrapper .incorrect {
    font-weight: bold;
    color: red;
}
</style>
<div class="wrapper">
    <div class="row">
        @foreach ($users as $index => $user)
        <div class="elements">
            <div class="h2"><a href="{{ route('moonlight.browse.element', $user['classId']) }}"><span>{{ $user['name'] }}</span></a></div>
            <div><small>{{ $user['email'] }}</small></div>
            <ul>
                @if (! $user['tests'])
                <li>Тесты еще не выполнялись.</li>
                @else
                @foreach ($user['tests'] as $userTest)
                <li>
                    <div><a href="{{ route('moonlight.browse.element', $userTest['classId']) }}">{{ $userTest['name'] }}</a></div>
                    <div>
                        <small>{{ $userTest['created_at'] }}</small>,
                        <span class="correct">{{ $userTest['correct'] }}</span> /
                        <span class="incorrect">{{ $userTest['incorrect'] }}</span> /
                        {{ $userTest['total'] }},
                        {{ $userTest['percent'] }}%
                    </div>
                </li>
                @endforeach
                @endif
            </ul>
        </div>
        @endforeach
    </div>
</div>