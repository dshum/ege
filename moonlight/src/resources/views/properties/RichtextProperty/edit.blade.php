<label>{{ $title }}:</label><br>
@if ($readonly)
<div>{!! $value !!}</div>
@else
<textarea name="{{ $name }}" tinymce="true">{!! $value !!}</textarea>
@endif