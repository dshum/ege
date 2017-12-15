<script>
$(function() {
    $(':text[name="{{ $name }}_copy_autocomplete"]').autocomplete({
        serviceUrl: '{{ route('elements.autocomplete') }}',
        params: {
            item: '{{ $relatedClass }}'
        },
        onSelect: function (suggestion) {
            $('span[name="{{ $name }}_copy"][container]').html(suggestion.value);
            $(':hidden[name="{{ $name }}_copy"]').removeAttr('disabled').val(suggestion.id);
        },
        appendTo: $('span.autocomplete-container[name="{{ $name }}_copy_auto"]'),
        minChars: 0
    });
    
    $('span[name="{{ $name }}_copy"][unset]').click(function() {
        $('span[name="{{ $name }}_copy"][container]').html('Не определено');
        $(':hidden[name="{{ $name }}_copy"]').removeAttr('disabled').val('');
        $(':text[name="{{ $name }}_copy_autocomplete"]').val('');
    });
    
    $('span[name="{{ $name }}_copy"][reset]').click(function() {
        $('span[name="{{ $name }}_copy"][container]').html('Как есть');
        $(':hidden[name="{{ $name }}_copy"]').attr('disabled', 'disabled');
        $(':text[name="{{ $name }}_copy_autocomplete"]').val('');
    });
});
</script>
<label>{{ $title }}:</label>
<span name="{{ $name }}_copy" container>Как есть</span><br>
<input type="hidden" name="{{ $name }}_copy" copy="{{ $name }}" value="{{ $value ? $value->id : null }}" disabled="disabled">
<input type="text" name="{{ $name }}_copy_autocomplete" value="" placeholder="ID или название" class="one">
<span name="{{ $name }}_copy_auto" class="autocomplete-container"></span>
<span class="reset" name="{{ $name }}_copy" unset><span class="halflings halflings-remove-circle"></span></span>
<span class="reset" name="{{ $name }}_copy" reset><span class="halflings halflings-repeat"></span></span>