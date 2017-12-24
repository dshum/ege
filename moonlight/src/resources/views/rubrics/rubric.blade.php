@if ($rubricElements)
<ul>
    @foreach ($rubricElements as $element)
    <li><a href="{{ route('moonlight.browse.element', $element['classId']) }}">{{ $element['name'] }}</a></li>
    @endforeach
</ul>
@endif