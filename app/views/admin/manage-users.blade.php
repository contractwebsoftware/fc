@section('content')
<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-6">
				<strong class="h2">Providers</strong>
			</div>
			<div class="col-xs-12 col-md-6 text-right">
				<a href="{{ action('UserController@getNewUser') }}" class="btn btn-primary">Add User</a>
			</div>
		</div>
		<hr>


        {{ Form::open(['action'=>'UserController@getManageUsers','method'=>'GET']) }}
        <div class="input-group ">
            <input type="text" class="form-control" name="q" >
            <span class="input-group-btn">
                <button class="btn btn-primary" type="submit">Search Users</button>
            </span>
        </div><BR />
        {{ Form::close() }}


        {{ $users->appends(array('q'=>Input::get('q') ))->links() }}

        <div class="table-responsive">
            <table class="table table-borders">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Active</th>
                        <th>Login</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Last Login</th>
                        <th>Created</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{$user->id}}</td>
                        <td>{{($user->activated == '1'?'<font color=green>Active</font>':'<font color=red>Disabled</font>')}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->first_name}}</td>
                        <td>{{$user->last_name}}</td>
                        <td>{{date('m/d/Y h:i a',strtotime($user->last_login ))}}</td>
                        <td>{{date('m/d/Y h:i a',strtotime($user->created_at ))}}</td>
                        <td><a href="{{ action('UserController@getEditUser',$user->id) }}" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span> </a>&nbsp;
                            <a href="{{action('UserController@getDeleteUser',$user->id)}}" class="btn btn-xs btn-danger" onclick="return confirm('Delete This User?')"><span class="glyphicon glyphicon-trash"></span> </a>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $users->appends(array('q'=>Input::get('q') ))->links() }}

    </div>

@stop