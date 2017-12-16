@if ($isMainProperty)
<div class="label mainp"><i class="fa fa-flag"></i><span>ID или название</span></div>
<input type="hidden" name="{{ $name }}" value="{{ $value ? $value['id'] : null }}">
<div class="one container">
    <div><input type="text" class="one" item="{{ $relatedClass }}" property="{{ $name }}" name="{{ $name }}_autocomplete" value="{{ $value ? $value['name'] : null }}" placeholder="ID или название"></div>
    <div class="reset" property="{{ $name }}">&#215;</div>
</div>
@else
<div class="label textfield"><i class="fa fa-pencil"></i><span>{{ $title }}</span></div>
<div><input type="text" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $title }}"></div>
@endif