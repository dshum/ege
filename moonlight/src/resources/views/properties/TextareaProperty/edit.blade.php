<label>{{ $title }}:</label><br>
@if ($readonly)
<textarea name="{{ $name }}" placeholder="{{ $title }}" rows="10" readonly>{!! $value !!}</textarea>
@else
<textarea name="{{ $name }}" placeholder="{{ $title }}" rows="10">{!! $value !!}</textarea>
@endif