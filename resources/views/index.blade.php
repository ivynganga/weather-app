@extends('layout')


@section('content')

<div class="row"></div>
	<div class="col-md-6">
		<form method="POST" action="/weather">
			@csrf
			<div class="input-group mb-3">
				<input type="text" class="form-control" id="city" placeholder="City" name="city" value="{{ old('city') }}">
				<input type="text" class="form-control" placeholder="Country" name="country" aria-describedby="button-addon2" value="{{ old('country') }}">
				<button class="btn btn-outline-secondary search-button" type="submit" id="button-addon2"><ion-icon name="search"></ion-icon></button>
			</div>
			@if ($errors->any())
				<div class="row form-errors">
					<div class="col-md-1">
						<ion-icon size="large" name="warning-outline"></ion-icon>
					</div>
					<div class="col-md-11">
						<ul>
							@foreach ($errors->all() as $error)
								<li> {{ $error }} </li>
							@endforeach
						</ul>
					</div>
				</div>
			@endif

			@if (session()->has('input-error'))
				<div class="row form-errors">
					<div class="col-md-1">
						<ion-icon size="large" name="warning-outline"></ion-icon>
					</div>
					<div class="col-md-11">
						<p> {{ session()->get('input-error') }} </p>
					</div>
				</div>
			@endif

		</form>

		@if (session()->has('success'))
		<div>
			<p>{{ session('success') }}</p>
		</div>
			
		@endif


		@if (session('city') && session('country_code') )
			<div class="row">
				<div class="card">
					<div class="card-body">
						<h3 class="result-title">{{ session('city') }}, {{ session('country_code') }}</h3>
						@if (session('temp'))
							<span class="result-temperature">{{ number_format(session('temp')) }}&#176;</span>
						@endif
						@if (session('temp_high') && session('temp_low'))
							<div>
								<div><p class="result-temp-range">High {{ number_format(session('temp_high')) }}&#176;</p></div>
								<div><p class="result-temp-range">Low {{ number_format(session('temp_low')) }}&#176;</p></div>
							</div>
						@endif
					</div>
				</div>
			</div>
		@else
			@if (is_array($data) && sizeof($data) > 0)
				@if (strlen(trim($data['city'])) > 0 && strlen(trim($data['country_code'])) > 0)
					<div class="row">
						<div class="card">
							<div class="card-body">
								<h3 class="result-title">{{ $data['city'] }}, {{ $data['country_code'] }}</h3>
								@if ($data['temp'])
									<span class="result-temperature">{{ number_format($data['temp']) }}&#176;</span>
								@endif
								@if ($data['temp_high'] && $data['temp_low'])
									<div>
										<div><p class="result-temp-range">High {{ number_format($data['temp_high']) }}&#176;</p></div>
										<div><p class="result-temp-range">Low {{ number_format($data['temp_low']) }}&#176;</p></div>
									</div>
								@endif
							</div>
						</div>
					</div>
				@else 
					<div class="row form-errors">
						<div class="col-md-1">
							<ion-icon size="large" name="warning-outline"></ion-icon>
						</div>
						<div class="col-md-11">
							<span>Something went wrong. Please try again later.</span>
						</div>
					</div>
				@endif
			@else 
				<div class="row form-errors">
					<div class="col-md-1">
						<ion-icon size="large" name="warning-outline"></ion-icon>
					</div>
					<div class="col-md-11">
						<span>Something went wrong. Please try again later.</span>
					</div>
				</div>
			@endif
		@endif
	</div>


	<div class="col-md-6"></div>
</div>









@endsection