<div id="notif-box">
	@if (count($errors->all()) > 0)
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h3>Oops, Something went wrong!</h3>
		<p>Please fix these errors:</p>
		<ul>
		@foreach($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
	</div>
	@endif

	@if ($message = Session::get('success'))
	<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h3>Success</h3>
		{{ $message }}
	</div>
	@endif

	@if ($message = Session::get('error'))
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h3>Error</h3>
		{{ $message }}
	</div>
	@endif

	@if ($message = Session::get('warning'))
	<div class="alert alert-warning">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h3>Warning</h3>
		{{ $message }}
	</div>
	@endif

	@if ($message = Session::get('info'))
	<div class="alert alert-info">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h3>Info</h3>
		{{ $message }}
	</div>
	@endif
</div>