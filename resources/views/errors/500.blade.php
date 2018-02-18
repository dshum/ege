<!DOCTYPE html>
<html>
<head>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Ошибка 500</title>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
	<div class="container">
		<nav>
			<logo><a href="{{ route('welcome') }}"><i class="fa fa-graduation-cap"></i>ЕГЭ по биологии</a></logo>
		</nav>
		<main>
			<h1>Ой! Ошибка.</h1>
			@if (config('app.env') == 'local')
			<p>{{ $exception->getMessage() }}</p>
			<p>{{ $exception->getFile() }} ({{ $exception->getLine() }})</p>
			<p><small>{!! nl2br($exception->getTraceAsString()) !!}</small></p>
			@endif
			<p>Мы скоро ее исправим.</p>
			<p class="ascii">
{o,o}<br>
./)_)<br>
&nbsp;&nbsp;" "
			</p>
		</main>
		<footer>
			<div>Тесты ЕГЭ по биологии, {{ date('Y') }}</div>
		</footer>
	</div>
</body>
</html>