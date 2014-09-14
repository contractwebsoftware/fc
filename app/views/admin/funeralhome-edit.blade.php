@section('content')
<div class="row">
    <div class="col-md-4 pull-left"><a href="{{ action('AdminController@getFuneralhomes') }}">< Back to Funeral Homes</a></div> 
</div>

<div class="row">
    
    <div class="col-xs-12">
            <h2>Edit Funeral Home</h2>
            <hr>
    </div>
    <div class="col-xs-12">
    {{ Form::open(['action'=>'AdminController@postUpdateFuneralhome','class'=>'form-horizontal','role'=>'form']) }}
        {{ Form::hidden("funeralhome[id]",$funeralhome->id) }}
        <fieldset>
            <legend>Funeral Home Information</legend>
            <div class="form-group" style="display:none;">
                    <label  class="col-sm-4" for="status">Funeral Home Status</label>
                    <div class="col-sm-8">
                        <select name="funeralhome[status]" id="status" class="form-control">
                            <option value="0" {{ ($funeralhome->status=='0') ? ' selected' : '' }}>UnApproved</option>
                            <option value="1" {{ ($funeralhome->status=='1') ? ' selected' : '' }}>Approved</option>
                            <option value="2" {{ ($funeralhome->status=='2') ? ' selected' : '' }}>Deleted</option>
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
            <label  class="col-sm-4" for="cat_primary">Category</label>
            <div class="col-sm-8"><input type="text" placeholder="Category" name="funeralhome[cat_primary]" id="cat_primary" class="form-control" value="{{ $funeralhome->cat_primary }}"></div>
        </div>
        <div class="form-group">
            <label  class="col-sm-4" for="cat_sub">Sub Category</label>
            <div class="col-sm-8"><input type="text" placeholder="Sub Category" name="funeralhome[cat_sub]" id="cat_sub" class="form-control" value="{{ $funeralhome->cat_sub }}"></div>
        </div>
        <div class="form-group">
            <label  class="col-sm-4" for="e_address">Address</label>
            <div class="col-sm-8"><input type="text" placeholder="Address" name="funeralhome[e_address]" id="e_address" class="form-control" value="{{ $funeralhome->e_address }}"></div>
        </div>
        <div class="form-group">
            <label  class="col-sm-4" for="e_city">City</label>
            <div class="col-sm-8"><input type="text" placeholder="City" name="funeralhome[e_city]" id="e_city" class="form-control" value="{{ $funeralhome->e_city }}"></div>
        </div>
        <div class="form-group">
            <label  class="col-sm-4" for="e_state">State</label>
            <div class="col-sm-8"><input type="text" placeholder="State" name="funeralhome[e_state]" id="e_state" class="form-control" value="{{ $funeralhome->e_state }}"></div>
        </div>
        <div class="form-group">
            <label  class="col-sm-4" for="e_postal">Zip</label>
            <div class="col-sm-8"><input type="text" placeholder="Zip" name="funeralhome[e_postal]" id="e_postal" class="form-control" value="{{ $funeralhome->e_postal }}"></div>
        </div> 
        <div class="form-group">
            <label  class="col-sm-4" for="e_zip_full">Full Zip</label>
            <div class="col-sm-8"><input type="text" placeholder="Full Zip" name="funeralhome[e_zip_full]" id="e_zip_full" class="form-control" value="{{ $funeralhome->e_zip_full }}"></div>
        </div>  
        <div class="form-group">
            <label  class="col-sm-4" for="e_country">Country</label>
            <div class="col-sm-8"><input type="text" placeholder="Country" name="funeralhome[e_country]" id="e_country" class="form-control" value="{{ $funeralhome->e_country }}"></div>
        </div>  
        <div class="form-group">
            <label  class="col-sm-4" for="loc_county">County</label>
            <div class="col-sm-8"><input type="text" placeholder="County" name="funeralhome[loc_county]" id="loc_county" class="form-control" value="{{ $funeralhome->loc_county }}"></div>
        </div>  
        <div class="form-group">
            <label  class="col-sm-4" for="loc_area_code">Area Code</label>
            <div class="col-sm-8"><input type="text" placeholder="Area Code" name="funeralhome[loc_area_code]" id="loc_area_code" class="form-control" value="{{ $funeralhome->loc_area_code }}"></div>
        </div>  
        <div class="form-group">
            <label  class="col-sm-4" for="loc_lat_poly">Latitude</label>
            <div class="col-sm-8"><input type="text" placeholder="Latitude" name="funeralhome[loc_lat_poly]" id="loc_lat_poly" class="form-control" value="{{ $funeralhome->loc_lat_poly }}"></div>
        </div>  
        <div class="form-group">
            <label  class="col-sm-4" for="loc_long_poly">Longitude</label>
            <div class="col-sm-8"><input type="text" placeholder="Longitude" name="funeralhome[loc_long_poly]" id="loc_long_poly" class="form-control" value="{{ $funeralhome->loc_long_poly }}"></div>
        </div>     
        <div class="form-group">
            <label  class="col-sm-4" for="web_url">Website URL</label>
            <div class="col-sm-8"><input type="text" placeholder="Website URL" name="funeralhome[web_url]" id="web_url" class="form-control" value="{{ $funeralhome->web_url }}"></div>
        </div>     
        <div class="form-group">
            <label  class="col-sm-4" for="web_meta_title">Website Meta Title</label>
            <div class="col-sm-8"><input type="text" placeholder="Website Meta Title" name="funeralhome[web_meta_title]" id="web_meta_title" class="form-control" value="{{ $funeralhome->web_meta_title }}"></div>
        </div>      
        <div class="form-group">
            <label  class="col-sm-4" for="web_meta_desc">Website Meta Desc</label>
            <div class="col-sm-8"><input type="text" placeholder="Website Meta Desc" name="funeralhome[web_meta_desc]" id="web_meta_desc" class="form-control" value="{{ $funeralhome->web_meta_desc }}"></div>
        </div>      
        <div class="form-group">
            <label  class="col-sm-4" for="web_meta_keys">Website Meta Keys</label>
            <div class="col-sm-8"><input type="text" placeholder="Website Meta Keys" name="funeralhome[web_meta_keys]" id="web_meta_keys" class="form-control" value="{{ $funeralhome->web_meta_keys }}"></div>
        </div>     
  
  
  
  
  
  
                        

            <div class="form-group">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block">Update</button>
                </div>
            </div>
        </fieldset>
    {{ Form::close() }}
    </div>
</div>

@stop