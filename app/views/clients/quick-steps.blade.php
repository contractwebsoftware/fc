@extends('layouts.client')
@section('content')
<?php
    if(!is_null(Session::get('no-frame')))$noframe = Session::get('no-frame');
    else $noframe = false;
?>
<div class="col-sm-12">

@if(Input::get('login')=='y')
    <script>
        $().ready(function() {
            $('#login_to_save').click();
        });
    </script>
@endif

<?php
$save_button = 'Continue to Save';
?>


{{ Form::open(['action'=>'ClientController@postQuickStep','class'=>'form-horizontal','role'=>'form','files'=>true]) }}


{{ Form::hidden('client_id',$client->id) }}
{{ Form::hidden('step',Session::get('step')) }}
{{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}


<fieldset id="step2">
    <h3>Death Certificate Information<p></p></h3>
    <div class="row form-group">
        <div class="col-sm-4"><input name="deceased_info[first_name]" type="text" placeholder="First Name of the person you are arranging for" value="{{$client->DeceasedInfo->first_name}}" /></div>
        <div class="col-sm-4"><input name="deceased_info[middle_name]" type="text" placeholder="Middle" value="{{$client->DeceasedInfo->middle_name}}"  /></div>
   <div class="col-sm-4"><input name="deceased_info[last_name]" type="text" placeholder="Last Name" value="{{$client->DeceasedInfo->last_name}}" /></div>
</div>
<div class="row form-group">
   <div class="col-sm-12"><input name="deceased_info[aka]" type="text" placeholder="Also Known As (First/Middle/Last)" value="{{$client->DeceasedInfo->aka}}"/></div>
</div>
<div class="row form-group">
   <div class="col-sm-6"><input name="deceased_info[ssn]" type="text" placeholder="Social Security Number (SSN) - Optional" value="{{$client->DeceasedInfo->ssn}}"/></div>
   <div class="col-sm-6">
       <select name="deceased_info[gender]" class="form-control">
           <option {{ ($client->DeceasedInfo->gender==""?'selected':'') }} value="">Gender</option>
           <option value="Male" {{ ($client->DeceasedInfo->gender=="Male"?'selected':'') }}>Male</option>
           <option value="Female" {{ ($client->DeceasedInfo->gender=="Female"?'selected':'') }}>Female</option>
       </select>
   </div>
</div>
<div class="row form-group">
   <div class="col-sm-12"><input name="deceased_info[address]" type="text" placeholder="Street Address" value="{{$client->DeceasedInfo->address}}"/></div>
</div>
<div class="row form-group">
   <div class="col-sm-12"><input name="deceased_info[apt]" type="text" placeholder="Apartment/Suite" value="{{$client->DeceasedInfo->apt}}"/></div>
</div>
<div class="row form-group">
   <div class="col-sm-4"><input name="deceased_info[city]" type="text" placeholder="City" value="{{$client->DeceasedInfo->city}}"/></div>
   <div class="col-sm-4"><input name="deceased_info[state]" type="text" placeholder="State" value="{{$client->DeceasedInfo->state}}"/></div>
   <div class="col-sm-4"><input name="deceased_info[zip]" type="text" placeholder="ZIP" value="{{$client->DeceasedInfo->zip}}"/></div>
</div>
<div class="row form-group">
   <div class="col-sm-6"><input name="deceased_info[county]" type="text" placeholder="County of Residence" value="{{$client->DeceasedInfo->county}}"/></div>
   <div class="col-sm-6"><input name="deceased_info[yrs_in_county]" type="text" placeholder="Yrs In County" value="{{$client->DeceasedInfo->yrs_in_county}}"/></div>
</div>

<div class="row form-group">
   <div class="col-sm-12"><input name="deceased_info[birth_city_state]" type="text" placeholder="City and State of Birth" value="{{$client->DeceasedInfo->birth_city_state}}"/></div>
</div>
<div class="row form-group">
   <div class="col-sm-6">
       <select name="deceased_info[marriage_status]" class="form-control">
           <option value="">Marital Status</option>
           <option value="Never Married" {{ ($client->DeceasedInfo->marriage_status=="Never Married"?'selected':'') }}>Never Married</option>
           <option value="Married" {{ ($client->DeceasedInfo->marriage_status=="Married"?'selected':'') }}>Married</option>
           <option value="CA Registered Dom. Partner" {{ ($client->DeceasedInfo->marriage_status=="CA Registered Dom. Partner"?'selected':'') }}>CA Registered Dom. Partner</option>
           <option value="Divorced" {{ ($client->DeceasedInfo->marriage_status=="Divorced"?'selected':'') }}>Divorced</option>
           <option value="Widowed" {{ ($client->DeceasedInfo->marriage_status=="Widowed"?'selected':'') }}>Widowed</option>
           <option value="Unknown" {{ ($client->DeceasedInfo->marriage_status=="Unknown"?'selected':'') }}>Unknown</option>
       </select>
   </div>
   <div class="col-sm-6"><input name="deceased_info[phone]" type="text" placeholder="Phone Number"  value="{{$client->DeceasedInfo->phone}}"/></div>
</div>
<div class="row form-group">
   <div class="col-sm-12">
       <select name="deceased_info[race]" class="form-control">
           <option value="">Race</option>
           <option value="Hispanic or Latino" {{ ($client->DeceasedInfo->race=="Hispanic or Latino"?'selected':'') }}>Hispanic or Latino</option>
           <option value="American Indian or AK Native" {{ ($client->DeceasedInfo->race=="American Indian or AK Native"?'selected':'') }}>American Indian or AK Native</option>
           <option value="Asian" {{ ($client->DeceasedInfo->race=="Asian"?'selected':'') }}>Asian</option>
           <option value="Black or African American" {{ ($client->DeceasedInfo->race=="Black or African American"?'selected':'') }}>Black or African American</option>
           <option value="Native Hawaiian or other Pac. Islander" {{ ($client->DeceasedInfo->race=="Native Hawaiian or other Pac. Islander"?'selected':'') }}>Native Hawaiian or other Pac. Islander</option>
           <option value="White or European" {{ ($client->DeceasedInfo->race=="White or European"?'selected':'') }}>White or European</option>
       </select>
   </div>
</div>

<div class="row form-group">
    <div class="col-sm-6" style="@if(!Session::get('inAdminGroup') || !($client->id!=''))display:none;@endif">Date of Death<br />
        <?php
            $dod = date('m/d/Y', strtotime($client->DeceasedInfo->dod));
            if($dod == '01/01/1970' || $dod == '11/30/-0001')$dod = '';
        ?>
        <input name="deceased_info[dod]" id="dod" class="calendar" type="text" placeholder="Date of Death MM/DD/YYYY" value="{{ $dod }}" /></div>

    <div class="col-sm-12">Date of Birth<br />
        <?php
            $dob = date('m/d/Y', strtotime($client->DeceasedInfo->dob));
            if($dob == '01/01/1970' || $dob == '11/30/-0001')$dob = '';
        ?>
        <input name="deceased_info[dob]" id="dob" type="text" class="calendar" placeholder="Date of Birth MM/DD/YYYY" value="{{ $dob }}" /></div>
</div>

<br />


</fieldset>



<fieldset id="step3">
<h3>Family Information <p>Additional information about the deceased</p></h3>

<div class="row form-group">
   <div class="col-sm-6">
       School Information<Br />
       <select name="deceased_info[schooling_level]" class="form-control">
               <option value="">(Select One)</option>
               <option value="No formal schooling" {{ ($client->DeceasedInfo->schooling_level=="No formal schooling"?'selected':'') }}>No formal schooling</option>
               <option value="Elementary School" {{ ($client->DeceasedInfo->schooling_level=="Elementary School"?'selected':'') }}>Elementary School</option>
               <option value="Middle School" {{ ($client->DeceasedInfo->schooling_level=="Middle School"?'selected':'') }}>Middle School</option>
               <option value="High School" {{ ($client->DeceasedInfo->schooling_level=="High School"?'selected':'') }}>High School</option>
               <option value="Some college, no degree" {{ ($client->DeceasedInfo->schooling_level=="Some college, no degree"?'selected':'') }}>Some college, no degree</option>
               <option value="Associates Degree" {{ ($client->DeceasedInfo->schooling_level=="Associates Degree"?'selected':'') }}>Associates Degree</option>
               <option value="Bachelors Degree" {{ ($client->DeceasedInfo->schooling_level=="Bachelors Degree"?'selected':'') }}>Bachelors Degree</option>
               <option value="Masters Degree" {{ ($client->DeceasedInfo->schooling_level=="Masters Degree"?'selected':'') }}>Masters Degree</option>
               <option value="Doctorate" {{ ($client->DeceasedInfo->schooling_level=="Doctorate"?'selected':'') }}>Doctorate</option>
               <option value="Unknown" {{ ($client->DeceasedInfo->schooling_level=="Unknown"?'selected':'') }}>Unknown</option>
       </select>
   </div>
   <div class="col-sm-6">
       Military<Br />
       <select name="deceased_info[military]" class="form-control">
               <option value="">(Select One)</option>
               <option value="Yes" {{ ($client->DeceasedInfo->military=="Yes"?'selected':'') }}>Yes</option>
               <option value="No" {{ ($client->DeceasedInfo->military=="No"?'selected':'') }}>No</option>
               <option value="Unknown" {{ ($client->DeceasedInfo->military=="Unknown"?'selected':'') }}>Unknown</option>
       </select>
   </div>
</div>

<div class="row form-group">
   <div class="col-sm-12"><input name="deceased_info[occupation]" value="{{$client->DeceasedInfo->occupation}}" type="text" placeholder="Usual occupation for most of life" ></div>
</div>

<div class="row form-group">
   <div class="col-sm-6"><input name="deceased_info[business_type]" value="{{$client->DeceasedInfo->business_type}}" type="text" placeholder="Business Type or Industry"></div>
   <div class="col-sm-6"><input name="deceased_info[years_in_occupation]" value="{{$client->DeceasedInfo->years_in_occupation}}" type="text" placeholder="Yrs Occupation" /></div>
</div>


<strong>Family Information</strong><br />
<i>Surviving spouse/SRDP</i><br />

<div class="row form-group">
   <div class="col-sm-5"><input name="deceased_family_info[srdp_first_name]" value="{{$client->DeceasedFamilyInfo->srdp_first_name}}" type="text" placeholder="First Name"></div>
   <div class="col-sm-2"><input name="deceased_family_info[srdp_middle_name]" value="{{$client->DeceasedFamilyInfo->srdp_middle_name}}" type="text" placeholder="Middle" ></div>
   <div class="col-sm-5"><input name="deceased_family_info[srdp_last_name]" value="{{$client->DeceasedFamilyInfo->srdp_last_name}}" type="text" placeholder="Maiden Name" ></div>
</div>

<i>Father</i><br />

<div class="row form-group">
   <div class="col-sm-5"><input name="deceased_family_info[fthr_first_name]" value="{{$client->DeceasedFamilyInfo->fthr_first_name}}" type="text" placeholder="First Name" ></div>
   <div class="col-sm-2"><input name="deceased_family_info[fthr_middle_name]" value="{{$client->DeceasedFamilyInfo->fthr_middle_name}}" type="text" placeholder="Middle" ></div>
   <div class="col-sm-5"><input name="deceased_family_info[fthr_last_name]" value="{{$client->DeceasedFamilyInfo->fthr_last_name}}" type="text" placeholder="Last Name" ></div>
</div>

<div class="row form-group">
   <div class="col-sm-12"><input name="deceased_family_info[fthr_birth_city]" value="{{$client->DeceasedFamilyInfo->fthr_birth_city}}" type="text" placeholder="Father's birth state" ></div>
</div>

<i>Mother</i><br />

<div class="row form-group">
   <div class="col-sm-5"><input name="deceased_family_info[mthr_first_name]" value="{{$client->DeceasedFamilyInfo->mthr_first_name}}" type="text" placeholder="First Name" ></div>
   <div class="col-sm-2"><input name="deceased_family_info[mthr_middle_name]" value="{{$client->DeceasedFamilyInfo->mthr_middle_name}}" type="text" placeholder="Middle" ></div>
   <div class="col-sm-5"><input name="deceased_family_info[mthr_last_name]" value="{{$client->DeceasedFamilyInfo->mthr_last_name}}" type="text" placeholder="Maiden Name" ></div>
</div>



</fieldset>



    <fieldset class="step-authorized-person" id="step6">
        <div class="row form-group">
            <div class="col-sm-12">
                <h3>Authorized Informant <p>Person authorized to make the arrangements &nbsp; <a href="#" data-toggle="tooltip" data-placement="bottom" class="tooltips" title="Some states refer to this person as the informant. This is the person providing the information for the Death Certificate and the one authorized to request any changes needed.">?</a></p></h3>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-sm-5"><input type="text" name="client[first_name]" id="client_first_name" value="{{($client->first_name=="unregistered"?'':$client->first_name)}}" placeholder="First Name" ></div>
            <div class="col-sm-2"><input type="text" name="client[middle_name]" id="client_middle_name" value="{{$client->middle_name}}" placeholder="Middle" ></div>
            <div class="col-sm-5"><input type="text" name="client[last_name]" id="client_last_name" value="{{($client->last_name=="unregistered"?'':$client->last_name)}}" placeholder="Last Name" ></div>
        </div>
        <div class="row form-group">
            <div class="col-sm-9"><input type="text" name="client[address]" id="client_address" value="{{$client->address}}" placeholder="Street Address" ></div>
            <div class="col-sm-3"><input type="text" name="client[apt]" id="client_apt" value="{{$client->apt}}" placeholder="Apartment/Suite" ></div>
        </div>
        <div class="row form-group">
            <div class="col-sm-12"><input type="text" name="client[phone]" id="client_phone" value="{{$client->phone}}" placeholder="Phone Number" ></div>
        </div>
        <div class="row form-group">
            <div class="col-sm-5"><input type="text" name="client[city]" id="client_city" value="{{$client->city}}" placeholder="City" ></div>
            <div class="col-sm-2"><input type="text" name="client[state]" id="client_state" value="{{$client->state}}" placeholder="State" ></div>
            <div class="col-sm-5"><input type="text" name="client[zip]" id="client_zip" value="{{$client->zip}}" placeholder="ZIP" ></div>
        </div>
        <div class="row form-group">
            <div class="col-sm-12">Your plans for the cremated remains &nbsp; <a href="#" data-toggle="tooltip" data-placement="bottom" class="tooltips" title="Plans for the cremains are not always requested by the state, if a permit is issued by the state for the cremains then this information is most often requested.">?</a></div>
        </div>
        <div class="row form-group">
            <div class="col-sm-12">
                <select name="cremains_info[cremain_plan]" class="form-control">
                    <option value="burial" {{ ($client->CremainsInfo->cremain_plan=="burial"?'selected':'') }}> Burial </option>
                    <option value="kept_at_residence" {{ ($client->CremainsInfo->cremain_plan=="kept_at_residence"?'selected':'') }}> Kept at residence </option>
                    <option value="scatter_on_land" {{ ($client->CremainsInfo->cremain_plan=="scatter_on_land"?'selected':'') }}> We scatter on land </option> <!--- Add ${{$provider->pricing_options->scatter_on_land}} -->
                    <option value="you_scatter_on_land" {{ ($client->CremainsInfo->cremain_plan=="you_scatter_on_land"?'selected':'') }}> You scatter on land </option>
                    <option value="scatter_at_sea" {{ ($client->CremainsInfo->cremain_plan=="scatter_at_sea"?'selected':'') }}> We scatter at sea </option> <!--- Add ${{$provider->pricing_options->scatter_at_sea}} -->
                    <option value="you_scatter_on_sea" {{ ($client->CremainsInfo->cremain_plan=="you_scatter_on_sea"?'selected':'') }}> You scatter at sea </option>
                </select>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-sm-12"><br />
                Name and Address of person(s) who will keep the cremated remains at their residence, or county and state of ocean water if scatter at sea:<br>
                <input type="checkbox" id="keeper_of_cremains">
                <label for="keeper_of_cremains">Use address above</label><br>
                <textarea name="cremains_info[keeper_of_cremains]" rows="2" cols="20" id="keeper_of_cremains_field">{{$client->CremainsInfo->keeper_of_cremains}}</textarea>
            </div>
        </div>

        <Br /><Br />
        <h3>Select An Urn</h3>
        <hr>
        <div class="row">
            <div class="col-sm-12">
                <div id="owl-example" class="owl-carousel owl-theme" data-toggle="buttons" style="max-width:600px;">
                    @foreach( $products as $product )
                        <?php $product->product_id = ($product->product_id==''?$product->id:$product->product_id); ?>
                        <div class="item">
                            <table style="border:none;">
                                <tr><td style="width:230px;">
                                        <img src="{{ str_replace('http:','https:',$product->image) }}" style="max-width:200px;width:auto;height:auto;max-height:200px;" />
                                    </td><td valign="top">
                                        <h4>{{ $product->name }}</h4><Br />
                                        Description: {{ $product->description }}<Br /><Br />
                                        <label class="btn btn-primary {{$client_product->product_id == $product->product_id?'active':''}}" for="client_product_id{{ $product->product_id }}">
                                            <input name="client_product[product_id]" style="display:none;" id="client_product_id{{ $product->product_id }}" value="{{ $product->product_id }}" {{$client_product->product_id == $product->product_id?'checked="true"':''}} type="radio"> &nbsp; <b>Select</b> &nbsp; ${{ $product->price }}
                                            <input name="client_product[price][{{ $product->product_id }}]" value="{{ $product->price }}" type="hidden" />
                                        </label>
                                    </td></tr>
                            </table>
                        </div>
                    @endforeach
                </div>
                <hr>

            </div>
        </div>

        <div class="row form-group">
            <div class="col-sm-12" >
                <label for="client_product_note">Send Your Provider A Note About Your Urn Order</label>
                <i><b>*</b> Urns do not come inscribed or engraved. Personalization is responsibility of client.</i><BR />
                <textarea name="client_product[note]" id="client_product_note" style="width:100%;height:100px;">{{ $client_product->note }}</textarea>

            </div>
        </div>



        <div class="row form-group">
            <div class="col-sm-7 warn-login-text"></div>
            <div class="col-sm-3"><img src="{{ asset('img/ssl-seal.png') }}" style="margin-left:5px;margin-right:15px;" /></div>
            <div class="col-sm-2"><button type="submit" name="submit" value="submit" class="step_submit" onclick="$('.step_submit').attr('disabled',true);">{{$save_button}}</button><br class="clear" /></div>
        </div>
    </fieldset>


{{ Form::close() }}



<link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
<style>
    label {float:left;margin-right:15px;font-weight:normal;}
    .price-options {clear:both;float:none;margin-left:15px;}
</style>
<script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script src="{{ asset('js/jquery.chained.remote.min.js') }}"></script>


<!-- Important Owl stylesheet -->
<link rel="stylesheet" href="{{asset('packages/owl-carousel/owl.carousel.css')}}">
<link rel="stylesheet" href="{{asset('packages/owl-carousel/owl.theme.css')}}">
<style>#owl-example table td{border:none!important;}</style>
<script src="{{asset('packages/owl-carousel/owl.carousel.min.js')}}"></script>
<script>

    function updateAddr(){
        var html = $('#client_first_name').val() +" "+ $('#client_middle_name').val() +" "+ $('#client_last_name').val() + "\n"+
                $('#client_address').val() +" "+ $('#client_city').val() +", "+ $('#client_state').val() +" "+ $('#client_zip').val() + "\n" +
                "Apt: "+$('#client_apt').val() +", phone: "+ $('#client_phone').val();
        if($('#keeper_of_cremains').prop('checked'))$('#keeper_of_cremains_field').val(html);
    }

    $(function(){
        var carousel = $("#owl-example").owlCarousel({
            navigation : true, // Show next and prev buttons
            slideSpeed : 300,
            paginationSpeed : 400,
            singleItem:true
        });
        carousel.trigger('owl.jumpTo', {{($client_product->product_id-1) }})
        $('#keeper_of_cremains').click(function(){ updateAddr(); });
        $('.step-authorized-person').on('change', function(){ updateAddr() });
        $('.step-authorized-person').on('keyup', function(){ updateAddr() });
    });

    $('#submit').click(function(){
        if($('#package_plan').val()=='0'){alert('Please Select A Package Plan');return false;}


    });

    function plan_options(){
        if($('#package_plan').val()=='1')$('.included-1, .included-3').slideDown();
        else $('.included-1').hide();
        if($('#package_plan').val()=='2')$('.included-2, .included-3').slideDown();
        else $('.included-2').hide();
    }
    plan_options();

    $('#package_plan').on('change',function(){
        plan_options();

    });

    $('.required-Y,.required-y').on('click change blur focus',function(){
        //alert($(this).prop('checked',checked'));
        $(this).prop('checked',true);
    });

    //$("#city").remoteChained("#state", "{{action('StepController@getCities')}}");
    //$("#new_provider").remoteChained("#city", "{{action('StepController@getProvidersByCity')}}");
    $("#new_provider").remoteChained("#state", "{{action('StepController@getProvidersByState')}}");
    $('#new_provider').on('change', function(){
        $("#new_provider option:contains('funeralhome-')").attr("disabled",true);
        $("#new_provider option[value*='funeralhome-']").attr('disabled', true );

        var val = $(this).val();
        if(val == "" || val.toLowerCase().indexOf("funeralhome-") >= 0){
            $("#new_provider option:contains('provider-')").attr("selected",true);
            $("#new_provider option[value*='provider-']").attr('selected', true );
        }
    });

    $("#choose_provider").click(function(){
        //alert('going to ?set_zip='+$("#zip").val());
        window.location.href="{{action('ClientController@getSteps1')}}?provider_id="+$("#new_provider").val();
        return false;
    });

</script>


@if(Session::get('step')=="1-1")
    <script>window.location.href="#step1-1";</script>
    <?php
        Session::set('step','1');
    ?>
@endif

@stop