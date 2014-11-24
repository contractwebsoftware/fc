@section('content')



<!-- Table Hover -->
    <div class="block-area" id="basic">

    {{ Form::open(['action'=>'UserController@postUpdateUser','role'=>'form','class'=>'', 'enctype'=>'multipart/form-data']) }}
        {{ Form::hidden("user[id]",$user->id) }}

        <div class="block-area" id="basic">
            <h3 class="block-title">User Information</h3>
            <div class="tile p-15">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control input-sm" name="user[first_name]" id="first_name" value="{{$user->first_name}}" />
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control input-sm" name="user[last_name]" id="last_name" value="{{$user->last_name}}" />
                </div>
                <div class="form-group">
                    <label for="email">Login Email</label>
                    <input type="text" class="form-control input-sm" name="user[email]" id="email" value="{{$user->email}}" />
                </div>
                <div class="form-group">
                    <label for="password">Set Password</label>
                    <input type="text" class="form-control input-sm" name="user[password]" id="password" value=""  autocomplete="off" />
                </div>
                <div class="form-group">
                    <label for="activated">Account is Active</label>
                    <input type="checkbox" class="form-control input-sm" name="user[activated]" id="activated" value="1" {{$user->activated==1 || $user->id==''?'checked':''}} >
                </div>

                <div class="form-group">
                    <label for="super_admin">Super Admin Account - <i style="font-weight:normal;">Allowed to edit other admin users</i></label>

                    <input type="checkbox" class="form-control input-sm" name="user[super_admin]" id="super_admin" value="1" {{ $user->super_admin==1 ? 'checked':'' }} >
                </div>

                <div class="form-group">
                    Created: {{date('m/d/Y h:i a',strtotime($user->created_at))}}<br />
                    Last Logged In: {{date('m/d/Y h:i a',strtotime($user->last_login))}}<br />
                </div>

            </div>

        </div>

        <button type="submit" class="btn btn-sm m-t-10">Save</button>
        <a href="{{ action('UserController@getManageUsers') }}" class="btn btn-sm m-t-10">Cancel</a>

    {{ Form::close() }}

    </div>

@stop