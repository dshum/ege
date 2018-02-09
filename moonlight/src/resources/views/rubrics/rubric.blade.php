@if (sizeof($favorites))
<ul>
    @foreach ($favorites as $favorite)
    <li><a href="{{ route('moonlight.browse.element', $favorite['classId']) }}">{{ $favorite['name'] }}</a></li>
    @endforeach
</ul>
@elseif (sizeof($rubricElements))
<ul>
    @foreach ($rubricElements as $element)
    <li><a href="{{ route('moonlight.browse.element', $element['classId']) }}">{{ $element['name'] }}</a></li>
    @endforeach
</ul>
@else
<ul>
    <li>Элементов не найдено.</li>
</ul>
@endif