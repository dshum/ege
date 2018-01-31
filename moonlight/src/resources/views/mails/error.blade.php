<p>
Class: {{ $exception }}<br>
Message: {{ $e->getMessage() }}<br>
File: {{ $e->getFile() }}<br>
Line: {{ $e->getLine() }}<br>
Code: {{ $e->getCode() }}<br>
Trace: {{ nl2br($e->getTraceAsString()) }}<br>
</p>

@if ($count)
<p>{{ $count }} error(s) per {{ $diff }} sec</p>
@endif

<p>
Server: {{ $server }}<br>
URI: {{ $uri }}<br>
IP: {{ $ip }}<br>
IP2: {{ $ip2 }}<br>
UserAgent: {{ $useragent }}<br>
Referer: {{ $referer }}<br>
Request method: {{ $method }}<br>
</p>

<p>GET vars:<br><pre>{{ $get }}</pre></p>

<p>POST vars:<br><pre>{{ $post }}</pre></p>

<p>COOKIE vars:<br><pre>{{ $cookie }}</pre></p>

<p>Message sent: {{ $date->format('Y-m-d H:i:s') }}</p>