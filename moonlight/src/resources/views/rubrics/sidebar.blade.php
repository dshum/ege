@foreach ($rubrics as $rubric)
<div class="elements" rubric="{{ $rubric->getName() }}" display="{{ isset($opens[$rubric->getName()]) ? 'show' : 'none' }}">
    <div class="h2"><span>{{ $rubric->getTitle() }}</span></div>
    @if (isset($rubricElements[$rubric->getName()]))
    <ul>
        @foreach ($rubricElements[$rubric->getName()] as $element)
        <li class="{{ $classId == $element['classId'] ? 'active' : '' }}"><a href="{{ route('moonlight.browse.element', $element['classId']) }}">{{ $element['name'] }}</a></li>
        @endforeach
    </ul>
    @endif
</div>
@endforeach