@section('content')


     <div class="row">
        <div class="col-xs-12 col-md-6">
            <strong class="h2">Clients</strong>
        </div>
        <div class="col-xs-12 col-md-6 text-right">
            @if(Sentry::getUser()->role=='admin')

                <button class="btn btn-primary" type="submit" onclick="if($('#create_client_provider_id').val()==''){alert('Select A Provider First');return false;}else {create_client($('#create_client_provider_id').val())}" style="float:right;">Create Client</button>
                <select name="provider_id" id="create_client_provider_id" style="float:right;margin-right:15px;">
                    <option value="" selected>Choose Client's Provider</option>
                    @foreach($providers as $this_provider)
                        <option value="{{$this_provider->id}}">{{$this_provider->business_name}}</option>
                    @endforeach
                </select>

            @else
                <a href="#" onclick="create_client('{{$provider->id}}')" class="btn btn-primary">New Client</a>
            @endif

            <script>
                function create_client(provider_id){
                    $.getJSON( "{{action("AdminController@getNewclient")}}", function( data ) {
                      var items = [];
                      if(data){
                         if(data.client_id)
                         window.location.href="{{ action('ClientController@getSteps1')}}?provider_id="+provider_id+"&client_id="+data.client_id;
                      }
                    });
                }
            </script>
        </div>
    </div>
    <hr>

	<div class="row">
		<div class="col-xs-12 col-md-6">
			<ul class="nav nav-pills">
			  <li class="disabled"><a href="#">Filter:</a></li>
			  <?php
                            if(array_key_exists('status', $_GET))$status = $_GET['status'];
                            else $status='';
                            if(array_key_exists('preneed', $_GET))$preneed = $_GET['preneed'];
                            else $preneed='';
                          ?>
			  <li {{ ($status=="" && !array_key_exists('q', $_GET)?'class="active"':'') }}><a href="{{ URL::to(action('AdminController@getCustomers') ."?". http_build_query(array('status'=>'2')) ) }}">All</a></li>
                          <!-- Active=0 Completed=2 Deleted=3 -->
                          <li {{ ($status=="0" && ($preneed=="0"||$preneed=="")?'class="active"':'') }}><a href="{{ URL::to(action('AdminController@getCustomers') ."?". http_build_query(array('status'=>'0')) ) }}">Active</a></li>
			  <li {{ ($status=="0" && $preneed=="1"?'class="active"':'') }}><a href="{{ URL::to(action('AdminController@getCustomers') ."?". http_build_query(array('status'=>'0','preneed'=>'1')) ) }}">Pre-need</a></li>
			  <li {{ ($status=="1"?'class="active"':'') }}><a href="{{ URL::to(action('AdminController@getCustomers') ."?". http_build_query(array('status'=>'1')) ) }}">Completed</a></li>
			  <li {{ ($status=="3"?'class="active"':'') }}><a href="{{ URL::to(action('AdminController@getCustomers') ."?". http_build_query(array('status'=>'3')) ) }}">Deleted</a></li>
                          
                        </ul>
		</div>
		<div class="col-xs-12 col-md-6 text-right">
			{{ Form::open(['action'=>'AdminController@getCustomers','method'=>'GET']) }}
			<div class="input-group">
			  	<input type="text" class="form-control" name="q" value="{{Input::get('q')}}">
			  	<span class="input-group-btn">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </span>
			</div>
			{{ Form::close() }}
		</div>
	</div>
        <hr><style>div.tooltip-inner{min-width: 250px;}</style>
	<div class="page-body">
            {{ $clients->appends(array('status' => Input::get('status'), 'q'=>Input::get('q'), 'preneed'=>Input::get('preneed')))->links() }}
            
            {{ Form::open(['action'=>'AdminController@postMassUpdateClients','class'=>'form-horizontal','role'=>'form']) }}
           
            
                <select name="mass_edit_type" style="float:left;margin-right:15px;">
                    <option value="">Select Mass Action</option>
                    <option value="delete">Delete</option>
                    <option value="undelete">UnDelete</option>
                    <option value="undelete">Active</option>
                    <option value="preneed">Pre-Need</option>
                    <option value="completed">Completed</option>
                </select>
                <input type="submit" name="mass_update" value="Update" />
               
                
            <table class="table table-striped client_table">
                <thead>
                    <tr>
                        <th style="width:70px"><input type="checkbox" onclick="checkall();" id="checkallcb" style="float: left;" />
                           <label for="checkallcb" style="cursor:pointer;white-space:nowrap;">All</label>
                        </th>
                        <th>Customer Name</th>
                        <th>Deceased Name</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Customer Email</th>
                        <th class="text-right">Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach( $clients as $client )
                        @if(($client->preneed == "y" && Input::get('preneed')=='1') || Input::get('preneed')!='1')
                        <tr >
                            <td ><input type="checkbox" class="clients_mass_action" name="edit_clients[{{$client->id}}]" value="{{$client->id}}" /></td>
                            <td >{{ $client->first_name.' '.$client->last_name }}</td>
                            <td >{{ $client->deceased_first_name.' '.$client->deceased_last_name }}</td>
                            <td >{{ $client->phone }}</td>
                            <td >{{ date('m/d/Y',strtotime($client->created_at)) }}</td>
                            <td >@if($client->user != null) {{ $client->user->email }} @endif</td>
                            <td class="text-right" >
                                <div data-toggle="tooltip" data-html="true" class="tooltips" data-placement="bottom"  
                        title="<div style='text-align:left;'><b>Date Created</b>: {{ date("m/d/Y",strtotime($client->created_at)) }}<br /><b>Agreed To FTC</b>: {{$client->agreed_to_ftc?'Yes':'No'}}<br /><b>Confirmed Legal Auth</b>: {{$client->confirmed_legal_auth?'Yes':'No'}}<br /><b>Confirmed Correct Info</b>: {{$client->confirmed_correct_info?'Yes':'No'}}<br /> <b>Relationship</b>: {{$client->relationship}}</div>">
                                <?php
                                    switch($client->status){
                                        case 0:echo 'Active';break;
                                        case 1:echo 'Completed';break;
                                        case 3:echo 'Deleted';break;
                                    }   
                                    if($client->preneed == "y")echo '/Pre-Need';
                                ?>
                                </div>
                            </td>
                            <td class="text-right">
                                    <a href="{{ action('AdminController@getEditClient',$client->id) }}" class="btn btn-xs btn-default">
                                            <span class="glyphicon glyphicon-pencil"></span>
                                    </a>&nbsp;
                                <?php
                                if($client->status == 3)echo '<a href="'.action('AdminController@getUnDeleteClient',$client->id).'" class="btn btn-xs btn-success" onclick="return confirm(\'Are you sure?\')"><span class="glyphicon glyphicon-trash"></span> UnDelete</a>';
                                else echo '<a href="'.action('AdminController@getDeleteClient',$client->id).'" class="btn btn-xs btn-danger" onclick="return confirm(\'Are you sure?\')"><span class="glyphicon glyphicon-trash"></span> </a>';
                                ?>

                            </td>
                        </tr>
                        @endif
                    @endforeach
            </tbody>
            </table>
            {{ Form::close() }}
            
            {{ $clients->appends(array('status' => Input::get('status'), 'q'=>Input::get('q'), 'preneed'=>Input::get('preneed')))->links() }}
 
	</div>

        <script>
            var allchecked=true;
            function checkall(){
                $('.clients_mass_action').prop('checked', allchecked);
                allchecked = !allchecked;
            }
       </script>
        
@stop