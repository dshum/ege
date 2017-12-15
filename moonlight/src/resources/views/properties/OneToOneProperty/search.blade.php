<script>
$(function() {
    $(':text[name="{{ $name }}_autocomplete"]').autocomplete({
        serviceUrl: '{{ route('elements.autocomplete') }}',
        params: {
            item: '{{ $relatedClass }}'
        },
        onSelect: function (suggestion) {
            $(':hidden[name="{{ $name }}"]').val(suggestion.id);
        },
        appendTo: $('span.autocomplete-container[name="{{ $name }}_auto"]'),
        minChars: 0
    });
});
</script>
<div class="label one" property="{{ $name }}"><span class="glyphicons glyphicons-tag"></span><span>{{ $title }}</span></div>
<div{!! $value ? '' : ' class="dnone"' !!} container="property" property="{{ $name }}">
    <input type="hidden" name="{{ $name }}" value="{{ $value ? $value['id'] : null }}"{!! $value ? '' : ' disabled="disabled"' !!}>
    <input type="text" name="{{ $name }}_autocomplete" value="{{ $value ? $value['name'] : null }}" placeholder="ID или название"{!! $value ? '' : ' disabled="disabled"' !!}>
    <span name="{{ $name }}_auto" class="autocomplete-container"></span>
</div>