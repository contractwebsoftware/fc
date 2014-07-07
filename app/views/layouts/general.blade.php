<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Forcremation</title>
	<link rel="stylesheet" href="{{ asset('packages/Bootflat/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/ie-only.css') }}" />
	<![endif]-->
	@section('header')
	@show
</head>
<body>
	<div id="container">
		@yield('header')
			@if ( !Sentry::check() )
				@include('layouts._frontpage-header')
			@else
				{{-- take the groups --}}
				<?php $group = json_decode(Sentry::getUser()->getGroups()); ?>
				@if( $group[0]->name == 'Provider')
					@include('layouts._provider-header')
				@elseif( $group[0]->name == 'Admin')
					@include('layouts._admin-header')
				@else
				@endif
			@endif
		@show
		<div id="content">
			@include('layouts.notification')
			@yield('content')
		</div>
		<div id="footer"></div>
	</div>
	@section('footer')
	<script src="{{ asset('packages/Bootflat/js/jquery-1.10.1.min.js') }}"></script>
	<script src="{{ asset('packages/Bootflat/js/bootstrap.min.js') }}"></script>
	<script>
		$(function(){

		})
	</script>
	@show
</body>
</html>