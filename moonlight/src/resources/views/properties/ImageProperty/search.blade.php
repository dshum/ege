<div class="label textfield" property="{{ $name }}"><span class="glyphicons glyphicons-pencil"></span><span>{{ $title }}</span></div>
<div{!! $value ? '' : ' class="dnone"' !!} container="property" property="{{ $name }}">
    <input type="text" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $title }}"{!! $value ? '' : ' disabled="disabled"' !!}>
</div>