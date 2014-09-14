@section('content')
	<div class="row">
		<div class="col-xs-12 col-md-8 col-md-offset-2">
			<h2>Add New Funeral Home</h2>
			<hr>
		</div>
		<div class="col-xs-12 col-md-8 col-md-offset-2">
			{{ Form::open(['action'=>'AdminController@postFuneralhome','class'=>'form-horizontal','role'=>'form']) }}
				<fieldset>
					<legend>Funeral Home Information</legend>
                                        <div class="form-group" style="display:none;">
                                            <label  class="col-sm-4" for="status">Funeral Home Status</label>
                                            <div class="col-sm-8">
                                                <select name="funeralhome[status]" id="status" class="form-control">
                                                    <option value="0" >UnApproved</option>
                                                    <option value="1" >Approved</option>
                                                    <option value="2" >Deleted</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label  class="col-sm-4" for="biz_name">Funeral Home Name</label>
                                            <div class="col-sm-8"><input type="text" placeholder="Business Name" name="funeralhome[biz_name]" id="biz_name" class="form-control" value="{{ $funeralhome->biz_name }}"></div>
                                        </div>
                                        <div class="form-group">
                                            <label  class="col-sm-4" for="biz_info">Funeral Home Info</label>
                                            <div class="col-sm-8"><input type="text" placeholder="Business Info" name="funeralhome[biz_info]" id="biz_info" class="form-control" value="{{ $funeralhome->biz_info }}"></div>
                                        </div>
                                        <div class="form-group">
                                            <label  class="col-sm-4" for="biz_phone">Phone</label>
                                            <div class="col-sm-8"><input type="text" placeholder="Phone" name="funeralhome[biz_phone]" id="biz_phone" class="form-control" value="{{ $funeralhome->biz_phone }}"></div>
                                        </div>    
                                        <div class="form-group">
                                            <label  class="col-sm-4" for="biz_phone_ext">Phone Ext.</label>
                                            <div class="col-sm-8"><input type="text" placeholder="Phone Ext." name="funeralhome[biz_phone_ext]" id="biz_phone_ext" class="form-control" value="{{ $funeralhome->biz_phone_ext }}"></div>
                                        </div>      
                                        <div class="form-group">
                                            <label  class="col-sm-4" for="biz_fax">Fax</label>
                                            <div class="col-sm-8"><input type="text" placeholder="Fax" name="funeralhome[biz_fax]" id="biz_fax" class="form-control" value="{{ $funeralhome->biz_fax }}"></div>
                                        </div>     
                                        <div class="form-group">
                                            <label  class="col-sm-4" for="biz_email">Email</label>
                                            <div class="col-sm-8"><input type="text" placeholder="Email" name="funeralhome[biz_email]" id="biz_email" class="form-control" value="{{ $funeralhome->biz_email }}"></div>
                                        </div>   
					<div class="form-group">
						<div class="col-sm-12">
							<button type="submit" class="btn btn-primary btn-block">Add Provider</button>
						</div>
					</div>
				</fieldset>
			{{ Form::close() }}
		</div>
	</div>
@stop