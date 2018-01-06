@if ($itemPlace)
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
        <input type="hidden" name="{{ $name }}_copy" property="{{ $name }}" value="{{ $value ? $value['id'] : null }}">
        <input type="text" class="one" item="{{ $relatedClass }}" property="{{ $name }}" name="{{ $name }}_autocomplete" value="" placeholder="ID или название">
        <span class="addition unset" property="{{ $name }}">Очистить</span>
    </div>
    @endif
@elseif ($countPlaces)
    @if ($rootPlace)
    <p>
        <input type="radio" name="{{ $name }}_copy" property="{{ $name }}" id="{{ $name }}_copy_null" value="" {{ ! $value ? 'checked' : '' }}>
        <label for="{{ $name }}_copy_null">Корень сайта</label>
    </p>
    @endif
    @foreach ($elementPlaces as $element)
    <p>
        <input type="radio" name="{{ $name }}_copy" property="{{ $name }}" id="{{ $name }}_copy_{{ $element['id'] }}" value="{{ $element['id'] }}" {{ $value && $value['id'] == $element['id'] ? 'checked' : '' }}>
        <label for="{{ $name }}_copy_{{ $element['id'] }}">{{ $element['name'] }}</label>
    </p>
    @endforeach
@endif