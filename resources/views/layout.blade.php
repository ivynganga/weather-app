<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Weather App</title>

	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/style.css') }}" rel="stylesheet">
	{{--<link href="{{ asset('/fontawesome/css/all.css')}}" rel="stylesheet">--}}
	
	<script src="{{ asset('js/app.js') }}" defer></script>
	<script src="{{ asset('js/style.js') }}" defer></script>
</head>


<body>
	<div class="container-fluid maincontent">
		<div class="header">
			<h1>@yield('heading')</h1>
		</div>

		<div class="content">
			@yield('content')
		</div>
	</div>
	
	<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
	<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>
</html>