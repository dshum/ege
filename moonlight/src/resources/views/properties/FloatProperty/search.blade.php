<div class="label number" property="{{ $name }}"><span class="glyphicons glyphicons-calculator"></span><span>{{ $title }}</span></div>
<div{!! $from || $to ? '' : ' class="dnone"' !!} container="property" property="{{ $name }}">
    <input type="text" name="{{ $name }}-from" value="{{ $from }}" class="number" placeholder="от" {!! $from ? '' : ' disabled="disabled"' !!}> &#151;
    <input type="text" name="{{ $name }}-to" value="{{ $to }}" class="number" placeholder="до" {!! $to ? '' : ' disabled="disabled"' !!}>
</div>