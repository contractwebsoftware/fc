@section('content')
	<h2>Customers</h2>
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
	<div class="page-body">
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>Customer Name</th>
					<th>Deceased Name</th>
					<th>Phone</th>
					<th>Email</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="5">No Customers Data</td>
				</tr>
			</tbody>
		</table>
	</div>
@stop