<!DOCTYPE html>
<html>
<head>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>404</title>
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
            <h1>Ошибка 404</h1>
			<p>Страница не найдена.</p>
			<p class="ascii">
(\ (\<br>
( -.-)<br>
o_(")(")
			</p>
		</main>
		<footer>
			<div>Тесты ЕГЭ по биологии, {{ date('Y') }}</div>
		</footer>
	</div>
</body>
</html>