<div class="label textarea" property="{{ $name }}"><span class="glyphicons glyphicons-comments"></span><span>{{ $title }}</span></div>
<div{!! $value ? '' : ' class="dnone"' !!} container="property" property="{{ $name }}">
    <input type="text" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $title }}"{!! $value ? '' : ' disabled="disabled"' !!}>
</div>