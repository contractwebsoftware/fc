@section('content')
	{{ Form::open(['action'=>'AdminController@postSetting']) }}
		<fieldset>
			<legend>Change your password</legend>
			<p>
				{{ Form::label('oldpassword','Old Password') }}
				{{ Form::password('oldpassword') }}
			</p>
			<p>
				{{ Form::label('newpassword','New Password') }}
				{{ Form::password('newpassword') }}
			</p>
			<p>
				{{ Form::label('newpassword_confirmation','Confirm New Password') }}
				{{ Form::password('newpassword_confirmation') }}
			</p>
			<p>
				{{ Form::submit('Change Password',['class'=>'btn-block']) }}
			</p>
		</fieldset>
	{{ Form::close() }}
@stop