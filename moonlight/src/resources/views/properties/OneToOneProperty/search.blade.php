<div class="label one"><i class="fa fa-tag"></i><span>{{ $title }}</span></div>
<input type="hidden" name="{{ $name }}" value="{{ $value ? $value['id'] : null }}">
<div class="one container">
    <div><input type="text" class="one" item="{{ $relatedClass }}" property="{{ $name }}" name="{{ $name }}_autocomplete" value="{{ $value ? $value['name'] : null }}" placeholder="ID или название"></div>
    <div class="reset" property="{{ $name }}">&#215;</div>
</div>