@section('content')
	<div class="row">
		<div class="col-xs-12">
			<h2>Admins</h2>
			<hr>
			<div class="table-responsive">
				<table class="table-borders">
					<thead>
						<tr>
							<th>#</th>
							<th>Admin Name</th>
							<th>Email</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach( $admins as $admin )
						<tr>
							<td>{{ ++$i }}</td>
							<td>{{ $admin->first_name." ".$admin->last_name }}</td>
							<td>{{ $admin->email }}</td>
							<td class="text-center">
								<div class="btn-group">
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
			<h2>Providers</h2>
			<hr>
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>#</th>
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
							<td>{{ ++$j }}</td>
							<td>{{ $provider->provider }}</td>
							<td>0</td>
							<td>{{ $provider->phone }}</td>
							<td>{{ $provider->user->email }}</td>
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
			<h2>Customers</h2>
			<hr>
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Phone</th>
							<th>Email</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="5">No Customers Data</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
@stop