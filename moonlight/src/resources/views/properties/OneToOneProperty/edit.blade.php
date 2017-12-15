<script>
$(function() {
    $(':text[name="{{ $name }}_autocomplete"]').autocomplete({
        serviceUrl: '{{ route('elements.autocomplete') }}',
        params: {
            item: '{{ $relatedClass }}'
        },
        onSelect: function (suggestion) {
            $('span[name="{{ $name }}"][container]').html(suggestion.value);
            $(':hidden[name="{{ $name }}"]').val(suggestion.id);
        },
        appendTo: $('span.autocomplete-container[name="{{ $name }}_auto"]'),
        minChars: 0
    });
    
    $('span[name="{{ $name }}"][reset]').click(function() {
        $('span[name="{{ $name }}"][container]').html('Не определено');
        $(':hidden[name="{{ $name }}"]').val('');
        $(':text[name="{{ $name }}_autocomplete"]').val('');
    });
});
</script>
<label>{{ $title }}:</label>
<span name="{{ $name }}" container>
@if ($value)
<a href="{{ route('element.edit', $value->classId) }}">{{ $value->name }}</a>
@else
Не определено
@endif
</span>
@if ( ! $readonly)
<br>
<input type="hidden" name="{{ $name }}" value="{{ $value ? $value->id : null }}">
<input type="text" name="{{ $name }}_autocomplete" value="" placeholder="ID или название">
<span name="{{ $name }}_auto" class="autocomplete-container"></span>
<span class="reset" name="{{ $name }}" reset><span class="halflings halflings-remove-circle"></span></span>
@endif