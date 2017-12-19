@if (sizeof($elements))
@if ($itemPluginView)
{!! $itemPluginView !!}
@endif
<div class="item active">
    <ul class="header">
        <li class="h2"><span>{{ $currentItem->getTitle() }}</span></li>
        <li class="total">
            <span class="order-toggler">Всего {{ $total }} {{ Moonlight\Utils\RussianTextUtils::selectCaseForNumber($total, ['элемент', 'элемента', 'элементов']) }}.</span>
            @if ($orders)
            Отсортировано по {!! $orders !!}.
            @endif
        </li>
    </ul>
    <div class="buttons">
        <div class="button save enabled"><i class="fa fa-floppy-o"></i>Сохранить</div>
        <div class="button copy enabled"><i class="fa fa-clone"></i>Копировать</div>
        <div class="button move enabled"><i class="fa fa-arrow-right"></i>Перенести</div>
        <div class="button delete enabled"><i class="fa fa-trash-o"></i>Удалить</div>
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
                <td class="browse"><a href="browse.html"><i class="fa fa-angle-right"></i></a></td>
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
</div>
@else
<div class="empty">Элементов не найдено.</div>
@endif