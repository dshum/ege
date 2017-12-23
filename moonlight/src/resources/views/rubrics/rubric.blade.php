@if ($rubricElements)
<ul>
    @foreach ($rubricElements as $element)
    <li><a href="">{{ $element['name'] }}</a></li>
    @endforeach
</ul>
@endif