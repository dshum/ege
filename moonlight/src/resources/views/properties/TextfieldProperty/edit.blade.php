<label>{{ $title }}:</label><br>
@if ($readonly)
<input type="text" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $title }}" readonly>
@else
<input type="text" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $title }}">
@endif