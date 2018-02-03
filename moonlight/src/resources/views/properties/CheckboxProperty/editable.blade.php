<td class="editable" mode="view">
    <div  class="view-container">
        @if ($value)Да@else<span class="grey">Нет</span>@endif
    </div>
    <div class="edit-container">
        <input type="hidden" name="editing[{{ $element->id }}][{{ $name }}]" value="{{ $value }}">
        <div class="checkbox{{ $value ? ' checked' : '' }}" name="editing[{{ $element->id }}][{{ $name }}]"></div>
    </div>
</td>