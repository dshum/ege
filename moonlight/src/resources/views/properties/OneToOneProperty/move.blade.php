<label>{{ $title }}:</label>
<span name="{{ $name }}" container>Не изменять</span>
<span name="{{ $name }}" class="error"></span>
@if (! $readonly)
<br>
<input type="hidden" name="{{ $name }}" value="-1">
<input type="text" class="one" item="{{ $relatedClass }}" property="{{ $name }}" name="{{ $name }}_autocomplete" value="" placeholder="ID или название"><br>
<span class="addition unset" property="{{ $name }}">Очистить</span>
<span class="addition reset" property="{{ $name }}">Вернуть</span>
@endif