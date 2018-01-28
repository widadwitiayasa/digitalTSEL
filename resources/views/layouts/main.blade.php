<!doctype html>
	<html lang="en">
	<head>
		@include('layouts.header')
		<title>@yield('title')</title>
	</head>

	<body>
	@yield('content')
	</body>
	
	@include('layouts.footer')
</html>