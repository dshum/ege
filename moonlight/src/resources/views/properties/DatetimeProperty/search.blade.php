<script>
$(function() {
    $('[name="{{ $name }}-from"]').calendar({
        dateFormat: '%Y-%m-%d',
        selectHandler: function() {
            $('[name="{{ $name }}-from"]').val(this.date.print(this.dateFormat));
        }
    });

    $('[name="{{ $name }}-to"]').calendar({
        dateFormat: '%Y-%m-%d',
        selectHandler: function() {
            $('[name="{{ $name }}-to"]').val(this.date.print(this.dateFormat));
        }
    });
});
</script>
<div class="label date" property="{{ $name }}"><span class="glyphicons glyphicons-calendar"></span><span>{{ $title }}</span></div>
<div{!! $from || $to ? '' : ' class="dnone"' !!} container="property" property="{{ $name }}">
    <input type="text" name="{{ $name }}-from" value="{{ $from ? $from->format('Y-m-d') : '' }}" class="date" placeholder="от" readonly{!! $from ? '' : ' disabled="disabled"' !!}> &#151;
    <input type="text" name="{{ $name }}-to" value="{{ $to ? $to->format('Y-m-d') : '' }}" class="date" placeholder="до" readonly{!! $to ? '' : ' disabled="disabled"' !!}>
</div>