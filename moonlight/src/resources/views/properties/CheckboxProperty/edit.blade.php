<p>
    <input type="checkbox" name="{{ $name }}" id="{{ $name }}_checkbox" value="1"{{ $value ? ' checked' : '' }}{{ $readonly ? ' readonly' : '' }}>
    <label for="{{ $name }}_checkbox">{{ $title }}</label>
</p>