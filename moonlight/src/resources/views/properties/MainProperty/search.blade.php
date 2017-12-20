<div class="label mainp"><i class="fa fa-flag"></i><span>ID или название</span></div>
<input type="hidden" name="{{ $name }}" value="{{ $id }}">
<div class="one container">
    <div><input type="text" class="one" item="{{ $relatedClass }}" property="{{ $name }}" name="{{ $name }}_autocomplete" value="{{ $text }}" placeholder="ID или название"></div>
    <div class="reset" property="{{ $name }}">&#215;</div>
</div>