<script>
$(function() {
    $(':text[name="{{ $name }}_move_autocomplete"]').autocomplete({
        serviceUrl: '{{ route('elements.autocomplete') }}',
        params: {
            item: '{{ $relatedClass }}'
        },
        onSelect: function (suggestion) {
            $('span[name="{{ $name }}_move"][container]').html(suggestion.value);
            $(':hidden[name="{{ $name }}_move"]').removeAttr('disabled').val(suggestion.id);
        },
        appendTo: $('span.autocomplete-container[name="{{ $name }}_move_auto"]'),
        minChars: 0
    });
    
    $('span[name="{{ $name }}_move"][unset]').click(function() {
        $('span[name="{{ $name }}_move"][container]').html('Не определено');
        $(':hidden[name="{{ $name }}_move"]').removeAttr('disabled').val('');
        $(':text[name="{{ $name }}_move_autocomplete"]').val('');
    });
    
    $('span[name="{{ $name }}_move"][reset]').click(function() {
        $('span[name="{{ $name }}_move"][container]').html('Как есть');
        $(':hidden[name="{{ $name }}_move"]').attr('disabled', 'disabled');
        $(':text[name="{{ $name }}_move_autocomplete"]').val('');
    });
});
</script>
<label>{{ $title }}:</label>
<span name="{{ $name }}_move" container>Как есть</span><br>
<input type="hidden" name="{{ $name }}_move" move="{{ $name }}" value="{{ $value ? $value->id : null }}" disabled="disabled">
<input type="text" name="{{ $name }}_move_autocomplete" value="" placeholder="ID или название" class="one">
<span name="{{ $name }}_move_auto" class="autocomplete-container"></span>
<span class="reset" name="{{ $name }}_move" unset><span class="halflings halflings-remove-circle"></span></span>
<span class="reset" name="{{ $name }}_move" reset><span class="halflings halflings-repeat"></span></span>