<label>{{ $title }}:</label><br>
@if ($readonly)
<textarea name="{{ $name }}" placeholder="{{ $title }}" rows="7" readonly>{{ $value }}</textarea>
@else
<textarea name="{{ $name }}" placeholder="{{ $title }}" rows="7">{{ $value }}</textarea>
@endif