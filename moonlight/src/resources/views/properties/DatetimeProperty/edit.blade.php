<label>{{ $title }}:</label><br>
@if ($readonly && $value)
{{ $value->format('d.m.Y') }}, <small>{{ $value->format('H:i:s') }}</small>
@elseif ($readonly)
<span class="grey">Не определено</span>
@else
<script>
$(function() {
    $(':hidden[name="{{ $name }}_date"]').calendar({
        triggerElement: 'span[name="{{ $name }}_show"]',
        dateFormat: '%Y-%m-%d',
        selectHandler: function() {
            $(':hidden[name="{{ $name }}_date"]').val(this.date.print(this.dateFormat));
            $('span[name="{{ $name }}_show"]').html(this.date.print('%d.%m.%Y'));
        }
    });
    
    $('span[name="{{ $name }}"][reset]').click(function() {
        $(':hidden[name="{{ $name }}_date"]').val('');
        $(':text[name="{{ $name }}_hour"]').val('');
        $(':text[name="{{ $name }}_minute"]').val('');
        $(':text[name="{{ $name }}_second"]').val('');
        $('span[name="{{ $name }}_show"]').html('Не определено');
    });
});
</script>
<input type="hidden" name="{{ $name }}_date" value="{{ $value ? $value->format('Y-m-d') : null }}">
<span class="dashed hand" name="{{ $name }}_show">{{ $value ? $value->format('d.m.Y') : 'Не определено' }}</span>,&nbsp;
<input type="text" name="{{ $name }}_hour" class="time" value="{{ $value ? $value->format('H') : null }}"> : 
<input type="text" name="{{ $name }}_minute" class="time" value="{{ $value ? $value->format('i') : null }}"> : 
<input type="text" name="{{ $name }}_second" class="time" value="{{ $value ? $value->format('s') : null }}">
<span class="reset" name="{{ $name }}" reset>&#215;</span>
@endif