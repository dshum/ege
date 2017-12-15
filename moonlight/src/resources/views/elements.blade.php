@if (sizeof($elements))
    @if ($hasOrderProperty)
    <span item="{{ $currentItem->getNameId() }}" class="order-toggler"><span class="halflings halflings-sort"></span></span>
    @endif
    @if ($currentPage == 1)
    <div class="count">
        Всего {{ $total }} {{ Moonlight\Utils\RussianTextUtils::selectCaseForNumber($total, ['элемент', 'элемента', 'элементов']) }}.
        @if ($orders)
        Отсортировано по {!! $orders !!}.
        @endif
    </div>
    @else
    <div class="page">Страница {{ $currentPage }}</div>
    @endif
    <ul class="elements sortable" item="{{ $currentItem->getNameId() }}">
    @foreach ($elements as $element)
    <li id="element_{{ $element->getClassId() }}" classId="{{ $element->getClassId() }}">
        <div class="check" classId="{{ $element->getClassId() }}"></div>
        @if ($element->trashed())
        <div class="deleted">{{ $element->deleted_at->format('d.m.Y') }}<br><span class="time">{{ $element->deleted_at->format('H:i:s') }}</span></div>
        @endif
        <div class="created">{{ $element->created_at->format('d.m.Y') }}<br><span class="time">{{ $element->created_at->format('H:i:s') }}</span></div>
        @if ($element->trashed())
        <div>{{ $element->{$currentItem->getMainProperty()} }}</div>
        @else
        <div class="edit"><a href="{{ route('element.edit', $element->getClassId()) }}"><span class="halflings halflings-pencil"></span></a></div>
        <div main="true"><a href="{{ route('browse.element', $element->getClassId()) }}">{{ $element->{$currentItem->getMainProperty()} }}</a></div>
        @endif
        @if (isset($fields[$element->getClassId()]))
            @foreach ($fields[$element->getClassId()] as $property)
                @if ($view = $property->setElement($element)->getListView())
                {!! $view !!}
                @endif
            @endforeach
        @endif
    </li>
    @endforeach
    </ul>
    @if ($hasMorePages)
    <div><span class="next" page="{{ $currentPage + 1 }}" item="{{ $currentItem->getNameId() }}" classId="{{ isset($currentElement) && $currentElement ? $currentElement->getClassId() : null }}">Дальше <span class="halflings halflings-menu-right"></span></span></div>
    @endif
@else
    <div class="empty">Элементов не найдено.</div>
@endif