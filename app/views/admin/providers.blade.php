@section('content')
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<strong class="h2">Providers</strong>
			</div>
			<div class="col-xs-12 col-md-6 text-right">
				<a href="{{ action('AdminController@getNewProvider') }}" class="btn btn-primary">New Provider</a>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-xs-12 col-md-6 text-right">
				{{ Form::open(['action'=>'AdminController@getProviders','method'=>'GET']) }}
				<div class="input-group">
				  	<input type="text" class="form-control" name="q">
				  	<span class="input-group-btn">
                                            <button class="btn btn-primary" type="submit">Search</button>
                                        </span>
                                        
				</div>
                                <input type="checkbox" name="include_deleted" value="1" /> Include Deleted
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
						<td><?php
                                                        switch($provider->provider_status){
                                                            case 0:echo 'UnApproved';break;
                                                            case 1:echo 'Approved';break;
                                                            case 2:echo 'Deleted';break;
                                                            default:echo 'UnApproved';break;
                                                        }       
                                                    ?>
                                                </td>
						<td>{{ $provider->business_name }}</td>
						<td>{{ $provider->client_count }}</td>
						<td>{{ $provider->phone }}</td>
						<td>@if($provider->user != null) {{ $provider->user->email }} @endif</td>
						<td class="text-center">
							<div class="btn-group">
								<a href="{{ action('AdminController@getEditProvider',$provider->id) }}" class="btn btn-xs btn-default">
									<span class="glyphicon glyphicon-pencil"></span> Edit info
								</a>
                                                            <?php
                                                            if($provider->provider_status == 2)echo '<a href="'.action('AdminController@getUnDeleteProvider',$provider->id).'" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure?\')"><span class="glyphicon glyphicon-trash"></span> UnDelete</a>';
                                                            else echo '<a href="'.action('AdminController@getDeleteProvider',$provider->id).'" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure?\')"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
                                                            ?>
							</div>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
@stop