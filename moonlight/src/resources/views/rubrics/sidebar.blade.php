@foreach ($favoriteRubrics as $favoriteRubric)
<div class="favorite elements" rubric="{{ $favoriteRubric->id }}" display="{{ isset($favorites[$favoriteRubric->id]) && sizeof($favorites[$favoriteRubric->id]) ? 'show' : 'none' }}">
    <div class="h2"><span>{{ $favoriteRubric->name }}</span></div>
    @if (isset($favorites[$favoriteRubric->id]) && sizeof($favorites[$favoriteRubric->id]))
    <ul>
        @foreach ($favorites[$favoriteRubric->id] as $favorite)
        <li class="{{ $classId == $favorite['classId'] ? 'active' : '' }}"><a href="{{ route('moonlight.browse.element', $favorite['classId']) }}">{{ $favorite['name'] }}</a></li>
        @endforeach
    </ul>
    @endif
</div>
@endforeach
@foreach ($rubrics as $rubric)
<div class="elements" rubric="{{ $rubric->getName() }}" display="{{ isset($rubricElements[$rubric->getName()]) ? 'show' : 'none' }}">
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