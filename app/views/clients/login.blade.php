@extends('layouts.general')
@section('content')
<div>
	<h2 class="text-center">Log in. <small>Enter your email address and password below</small></h2>
	{{ Form::open(array('action' => 'ClientController@postLogin')) }}
		<div class="form-group">
			<label for="email">Email Address</label>
			<input class="form-control" type="text" name="email" required>
		</div>
		<div class="form-group">
			<label for="password">Password</label>
			<input class="form-control" type="password" name="password">
		</div>
		<div class="form-group"><label for="remember"><input type="checkbox" name="remember" id="remember"> Remember me</label></div>
		<div class="form-group"><input class="form-control btn btn-primary" type="submit" value="login" class="button"></div>
	{{ Form::close() }}
	<p class="text-center"><a href="#">Forgot your password?</a></p>
</div>
@stop