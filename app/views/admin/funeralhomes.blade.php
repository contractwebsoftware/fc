@section('content')

    <div class="container-fluid">
        
        <div class="row">
                <div class="col-xs-12 col-md-6">
                        <strong class="h2">Providers</strong>
                </div>
                <div class="col-xs-12 col-md-6 text-right">
                        <a href="{{ action('AdminController@getNewFuneralhome') }}" class="btn btn-primary">New Funeral Home</a>
                </div>
        </div>
        <hr>
        <div class="row">
                <div class="col-xs-12 col-md-6 text-right">
                        {{ Form::open(['action'=>'AdminController@getFuneralhomes','method'=>'GET']) }}
                        <div class="input-group">
                                <input type="text" class="form-control" name="q">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </span>

                        </div>
                        <input type="checkbox" name="include_deleted" value="1" />Include Deleted
                        &nbsp; <input type="checkbox" name="include_state" value="1" />Search Only State
                        &nbsp; <input type="checkbox" name="include_state" value="1" />Search Only City
                        {{ Form::close() }}
                </div>
        </div>
        <hr>

     {{ $funeral_homes->links() }}

     {{ Form::open(['action'=>'AdminController@postMassUpdateFuneralHomes','class'=>'form-horizontal','role'=>'form']) }}

        <select name="mass_edit_type" style="float:left;margin-right:15px;">
            <option value="">Select Mass Action</option>
            <option value="delete">Delete</option>
            <option value="undelete">UnDelete</option>
        </select>
        <input type="submit" name="mass_update" value="Update" />


        <table class="table table-striped client_table">
        <thead>
            <tr>
                <th style="width:70px"><input type="checkbox" onclick="checkall();" id="checkallcb" style="float: left;" />
                   <label for="checkallcb" style="cursor:pointer;white-space:nowrap;">All</label>
                </th>
                <!--<th>Status</th>-->
                <th>Name</th>
                <th>City</th>
                <th>State</th>
                <th>Zip</th>
                <th>Phone</th>
                <th>Website</th>
                <th>Created</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
            @foreach( $funeral_homes as $home )
            <tr>
                <td ><input type="checkbox" class="clients_mass_action" name="edit_funeralhomes[{{$home->id}}]" value="{{$home->id}}" /></td>
                <!--<td><?php
                    switch($home->status){
                        case 0:echo 'UnApproved';break;
                        case 1:echo 'Approved';break;
                        case 2:echo 'Deleted';break;
                        default:echo 'UnApproved';break;
                    }       
                    ?>
                </td>-->
                <td>{{ $home->biz_name }}</td>
                <td>{{ $home->e_city }}</td>
                <td>{{ $home->e_state }}</td>
                <td>{{ $home->e_postal }}</td>
                <td>{{ $home->biz_phone }}</td>
                <td>{{ ($home->web_url!=''?'<a href="'.$home->web_url.'" target="_blank">Link</a>':'') }}</td>
                <td>{{ ($home->created_at?date('m/d/Y',strtotime($home->created_at)):'') }}</td>

                <td class="text-center">
                    <div class="btn-group">
                        <a href="{{ action('AdminController@getEditFuneralhome',$home->id) }}" class="btn btn-xs btn-default">
                            <span class="glyphicon glyphicon-pencil"></span> Edit info
                        </a>
                        <?php
                        if($home->status == 2)echo '<a href="'.action('AdminController@getUnDeleteFuneralhome',$home->id).'" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure?\')"><span class="glyphicon glyphicon-trash"></span> UnDelete</a>';
                        else echo '<a href="'.action('AdminController@getDeleteFuneralhome',$home->id).'" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure?\')"><span class="glyphicon glyphicon-trash"></span> Delete</a>';
                        ?>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>


    {{ Form::close() }}
    {{ $funeral_homes->links() }}

    </div>
    <script>
        var allchecked=true;
        function checkall(){
            $('.clients_mass_action').prop('checked', allchecked);
            allchecked = !allchecked;
        }
   </script>
       
@stop