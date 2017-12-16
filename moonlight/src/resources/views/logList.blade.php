@if (sizeof($userActions))
    @if ($currentPage == 1)
    <div class="result">Всего {{ $total }} {{ Moonlight\Utils\RussianTextUtils::selectCaseForNumber($total, ['операция', 'операции', 'операций']) }}. Отсортировано по дате.</div>
    @else
    <div class="page">Страница {{ $currentPage }}</div>
    @endif
    <div class="leaf">
        <ul class="log">
        @foreach ($userActions as $userAction)
        <li>
            @if ($loggedUser->photoExists())
            <div class="avatar"><img src="{{ $userAction->user->getPhotoSrc() }}" /></div>
            @else
            <div class="avatar"><img src="/packages/moonlight/img/default-avatar.png" /></div>
            @endif
            <div class="date">{{ $userAction->created_at->format('d.m.Y') }}<br><span class="time">{{ $userAction->created_at->format('H:i:s') }}</span></div>
            <span class="user">{{ $userAction->user->login }}</span>
            <small>{{ $userAction->user->first_name }} {{ $userAction->user->last_name }}</small><br>
            <span class="title">{{ $userAction->getActionTypeName() }}</span>
            <span>{{ $userAction->comments }}</span>
        </li>
        @endforeach
        </ul>
    </div>
    @if ($hasMorePages)
    <div class="next" page="{{ $currentPage + 1 }}">
        Дальше<i class="fa fa-arrow-right"></i>
    </div>
    @endif
@else
    <div class="empty">Операций не найдено.</div>
@endif