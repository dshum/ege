<div class="label number" property="{{ $name }}"><span class="glyphicons glyphicons-calculator"></span><span>{{ $title }}</span></div>
<div{!! $from || $to ? '' : ' class="dnone"' !!} container="property" property="{{ $name }}">
    <input type="text" name="{{ $name }}-from" value="{{ $from ? $from->format('Y-m-d') : '' }}" class="number" placeholder="от" {!! $from ? '' : ' disabled="disabled"' !!}> &#151;
    <input type="text" name="{{ $name }}-to" value="{{ $to ? $to->format('Y-m-d') : '' }}" class="number" placeholder="до" {!! $to ? '' : ' disabled="disabled"' !!}>
</div>