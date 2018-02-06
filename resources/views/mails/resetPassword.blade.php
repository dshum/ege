<p>
@foreach ($introLines as $line)
{{ $line }}<br>
@endforeach
</p>

<p><a href="{{ $actionUrl }}" target="_blank">{{ $actionText }}</a></p>

<p>
@foreach ($outroLines as $line)
{{ $line }}<br>
@endforeach
<p>