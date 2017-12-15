<div class="label checkbox" property="{{ $name }}"><span class="glyphicons glyphicons-ok-2"></span><span>{{ $title }}</span></div>
<div{!! $value ? '' : ' class="dnone"' !!} container="property" property="{{ $name }}">
    <p>
        <input type="radio" id="{{ $name }}_true" name="{{ $name }}" value="true"{{ $value === 'true' ? ' checked' : '' }}{{ $value === null ? ' disabled="true"' : '' }}>
        <label for="{{ $name }}_true">Да</label>    
    </p>
    <p>
        <input type="radio" id="{{ $name }}_false" name="{{ $name }}" value="false"{{ $value === 'false' ? ' checked' : '' }}{{ $value === null ? ' disabled="true"' : '' }}>
        <label for="{{ $name }}_false">Нет</label>
    </p>
    <p>
        <input type="radio" id="{{ $name }}_null" name="{{ $name }}" value=""{{ ! $value ? ' checked' : '' }}{{ $value === null ? ' disabled="true"' : '' }}>
        <label for="{{ $name }}_null">Не важно</label>
    </p>
</div>