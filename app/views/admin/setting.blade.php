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
        
        @if(Sentry::getUser()->role=='admin')
                        
        {{ Form::open(['action'=>'AdminController@postAdminSetting']) }}
        <fieldset>
            <legend>Administrator Settings</legend>
            <p>
                {{ Form::label('provider_registration_letter','Provider Registration Usage Policy') }}
                <textarea id="provider_registration_letter" name="admin_settings[provider_registration_letter]" class="form-control customer_form_ta" >{{$admin_settings['provider_registration_letter']}}</textarea>
            </p>        

            <p>
                {{ Form::label('provider_registration_message','Provider Registration Message After Signup') }}
                <textarea id="provider_registration_message" name="admin_settings[provider_registration_message]" class="form-control customer_form_ta" >{{$admin_settings['provider_registration_message']}}</textarea>
            </p> 
            <p>
                {{ Form::submit('Save Settings',['class'=>'btn-block']) }}
            </p>
        </fieldset>                
        {{ Form::close() }}
        
        
        <script src="//cdn.ckeditor.com/4.4.4/full/ckeditor.js"></script>
        <script>
             CKEDITOR.replace( 'admin_settings[provider_registration_letter]',
                    {
                       // extraPlugins : 'uicolor',
                        height: '300px',
                    }); 
             CKEDITOR.replace( 'admin_settings[provider_registration_message]',
                {
                   // extraPlugins : 'uicolor',
                    height: '300px',
                });
        </script>
        @endif
        
@stop