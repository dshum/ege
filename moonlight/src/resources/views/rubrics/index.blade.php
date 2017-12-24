<div class="row">
@foreach ($rubrics as $index => $rubric)
    <div class="elements">
        <div class="h2"><span>{{ $rubric->getTitle() }}</span></div>
        <ul>
            @foreach ($rubricElements[$rubric->getName()] as $element)
            <li><a href="{{ route('moonlight.browse.element', $element['classId']) }}">{{ $element['name'] }}</a></li>
            @endforeach
        </ul>
    </div>
@if ($index % 3 == 2)
</div>
<div class="row">
@endif
@endforeach
</div>