<td class="editable" mode="view">
    <div  class="view-container">
    @if ($value)Да@else<span class="grey">Нет</span>@endif
    </div>
    <div class="edit-container">
    <p>
        <input type="checkbox" name="{{ $name }}" id="{{ $name }}_check" value="1"{{ $value ? ' checked' : '' }}>
        <label for="{{ $name }}_check"></label>
    </p>
    </div>
</td>