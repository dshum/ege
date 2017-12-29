<label>{{ $title }}:</label>
<span name="{{ $name }}" class="error"></span><br>
<div class="one container">
    <div><input type="text" class="many" item="{{ $relatedClass }}" property="{{ $name }}" name="{{ $name }}_autocomplete" value="" placeholder="ID или название"></div>
    <div class="add" property="{{ $name }}">Добавить</div>
</div>
<div class="many elements" name="{{ $name }}">
    @foreach ($elements as $element)
    <p><input type="checkbox" name="{{ $name }}[]" id="{{ $element['classId'] }}" checked value="{{ $element['id'] }}"><label for="{{ $element['classId'] }}">{{ $element['name'] }}</label></p>
    @endforeach
</div>