
<label>{{ $title }}:</label>
<span name="{{ $name }}" container>
    @if ($value)
    <a href="{{ route('moonlight.element.edit', $value['classId']) }}">{{ $value['name'] }}</a>
    @else
    Не определено
    @endif
</span>
<span name="{{ $name }}" class="error"></span>
@if (! $readonly)
<div>
    <input type="hidden" name="{{ $name }}" value="{{ $value ? $value['id'] : null }}">
    <input type="text" class="one" item="{{ $relatedClass }}" property="{{ $name }}" name="{{ $name }}_autocomplete" value="" placeholder="ID или название">
    <span class="addition unset" property="{{ $name }}">Очистить</span>
</div>
@endif