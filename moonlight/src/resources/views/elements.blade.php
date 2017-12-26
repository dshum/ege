@if ($itemPluginView)
    {!! $itemPluginView !!}
@endif
@if ($total || isset($hasBrowseFilter))
<div class="item active">
    <ul class="header">
        <li class="h2" display="show"><span>{{ $currentItem->getTitle() }}</span></li>
        <li class="total">
            <span class="order-toggler">Всего {{ $total }} {{ Moonlight\Utils\RussianTextUtils::selectCaseForNumber($total, ['элемент', 'элемента', 'элементов']) }}.</span>
            @if ($orders && $total)
            Отсортировано по {!! $orders !!}.
            @endif
        </li>
    </ul>
    <div list>
        @if (isset($browseFilterView) && $browseFilterView)
        <div class="plugin">
            {!! $browseFilterView !!}
        </div>
        @endif
        @if ($total)
        <div class="buttons">
            @if ($mode == 'trash')
            <div class="button restore enabled"><i class="fa fa-arrow-left"></i>Восстановить</div>
            <div class="button delete enabled"><i class="fa fa-trash-o"></i>Удалить</div>
            @else
            <div class="button save enabled"><i class="fa fa-floppy-o"></i>Сохранить</div>
            <div class="button copy enabled"><i class="fa fa-clone"></i>Копировать</div>
            <div class="button move enabled"><i class="fa fa-arrow-right"></i>Перенести</div>
            <div class="button delete enabled"><i class="fa fa-trash-o"></i>Удалить</div>
            @endif
        </div>
        <table class="elements">
            <thead>
                <tr>
                    <th class="browse"><i class="fa fa-sort"></i></th>
                    @foreach ($properties as $property)
                    <th><a href>{{ $property->getTitle() }}</a></th>
                    @endforeach
                    <th class="check"><div class="check"></div></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($elements as $element)
                <tr>
                    @if ($mode == 'browse')
                    <td class="browse"><a href="{{ route('moonlight.browse.element', \Moonlight\Main\Element::getClassId($element)) }}"><i class="fa fa-angle-right"></i></a></td>
                    @elseif ($mode == 'search')
                    <td class="browse"><a href="{{ route('moonlight.browse.element', \Moonlight\Main\Element::getClassId($element)) }}"><i class="fa fa-angle-right"></i></a></td>
                    @else
                    <td class="browse"><i class="fa fa-angle-right"></i></td>
                    @endif
                    @if (isset($views[Moonlight\Main\Element::getClassId($element)]))
                        @foreach ($views[Moonlight\Main\Element::getClassId($element)] as $view)
                            {!! $view !!}
                        @endforeach
                    @endif
                    <td class="check"><div class="check"></div></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if ($lastPage > 1)
        <ul class="pager" classId="{{ isset($classId) ? $classId : ''}}" item="{{ $currentItem->getNameId() }}" page="{{ $currentPage }}" last="{{ $lastPage }}">
            <li prev class="arrow {{ $currentPage > 1 ? 'active' : '' }}"><i class="fa fa-arrow-left"></i></li>
            <li first class="arrow {{ $currentPage > 1 ? 'active' : '' }}">1</li>
            <li class="page"><input type="text" value="{{ $currentPage }}"></li>
            <li last class="arrow {{ $currentPage < $lastPage ? 'active' : '' }}">{{ $lastPage }}</li>
            <li next class="arrow {{ $currentPage < $lastPage ? 'active' : '' }}"><i class="fa fa-arrow-right"></i></li>
        </ul>
        @endif
        @else
        <div class="empty">Элементов не найдено.</div>
        @endif
    </div>
</div>
@elseif ($mode != 'browse')
<div class="empty">Элементов не найдено.</div>
@endif