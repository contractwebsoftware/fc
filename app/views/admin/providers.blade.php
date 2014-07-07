@section('content')
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<strong class="h2">Provider</strong>
			</div>
			<div class="col-xs-12 col-md-6 text-right">
				<a href="{{ action('AdminController@getNewProvider') }}" class="btn btn-primary">New Provider</a>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<ul class="nav nav-pills">
				  <li class="disabled"><a href="#">Filter:</a></li>
				  <li class="active"><a href="#">All</a></li>
				  <li><a href="#">Completed</a></li>
				  <li><a href="#">Junk</a></li>
				  <li><a href="#">Deleted</a></li>
				</ul>
			</div>
			<div class="col-xs-12 col-md-6 text-right">
				{{ Form::open(['action'=>'AdminController@getProviders','method'=>'GET']) }}
				<div class="input-group">
				  	<span class="input-group-addon">Search</span>
				  	<input type="text" class="form-control" name="q">
				  	<span class="input-group-btn">
			          <button class="btn btn-default" type="submit">Search</button>
			        </span>
				</div>
				{{ Form::close() }}
			</div>
		</div>
		<hr>
		<div class="table-responsive">
			<table class="table table-borders">
				<thead>
					<tr>
						<th>Status</th>
						<th>Provider Name</th>
						<th># Cases</th>
						<th>Phone</th>
						<th>Email</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach( $providers as $provider )
					<tr>
						<td>{{ $provider->status }}</td>
						<td>{{ $provider->business_name }}</td>
						<td>0</td>
						<td>{{ $provider->phone }}</td>
						<td>@if($provider->user != null) {{ $provider->user->email }} @endif</td>
						<td class="text-center">
							<div class="btn-group">
								<a href="{{ action('AdminController@getEditProvider',$provider->id) }}" class="btn btn-xs btn-default">
									<span class="glyphicon glyphicon-pencil"></span> Edit info
								</a>
								<a href="#" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?')">
									<span class="glyphicon glyphicon-trash"></span> Delete
								</a>
							</div>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
@stop