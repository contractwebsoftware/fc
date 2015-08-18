@extends('layouts.client')
@section('content')
<div class="col-sm-{{(Session::get('inAdminGroup')!=''?'12':'9')}}">

@if(Session::get('inAdminGroup') )
     <style>
        .hideInAdmin{display:none;}
    </style>
@endif


@if(Input::get('login')=='y')
    <script>
        $().ready(function() {
            $('#login_to_save').click();
        });
    </script>
@endif

@if(Session::get('inAdminGroup') && $client->id!='')
    <div class="row">
        <div class="col-md-4 pull-left"><a href="{{ action('AdminController@getCustomers') }}">Back to Clients</a></div> 
    </div>

    <div class="row">
        <div class="col-md-12" style="font-size:12px!important;text-align:center;">
        <b>Goto:</b>
            <a href="#step1">Cremation Plans</a>
            | <a href="#step2">Certificate Information</a>
            | <a href="#step3">Life Data</a>
            | <a href="#step4">Location of Pending Death</a>
            | <a href="#step5">Line of Authority</a><br />
            <a href="#step6">Authorized Person</a>
            | <a href="#step7">Certified Death Certificates</a>
            | <a href="#step8">Shipping Information</a>
            | <a href="#step9">Payment Summary</a>
            | <a href="#step10">Confirm/Submit</a>
        </div>
    </div>
    <fieldset>
        <div class="row">
            <div class="col-md-6"><b style="font-size:14px;color:#999;">Client</b></div>
            <div class="col-md-6"><b style="font-size:14px;color:#999;">Provider</b></div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h2><a href="#step6" >{{$client->first_name.' '.$client->last_name}}</a></h2>
                &nbsp; &nbsp; <b>Login Email</b>: <a href="mailto:{{ $client->User->email }}">{{ $client->User->email }}</a>
                &nbsp; [<i><a href="#" onclick="$('#edit-client-email').slideToggle();return false;"> Edit </a></i>]<br />
                <div id="edit-client-email" style="display:none;clear:both;float:none;margin-bottom:15px;width:100%;padding-left:35px;margin-top:15px;">
                    Edit Client Email and Username:<br />
                    {{ Form::open(['action'=>'ClientController@postUpdateEmail','class'=>'form-horizontal','role'=>'form']) }}
                    {{ Form::hidden('client_id',$client->id) }}
                    {{ Form::hidden('client[id]',$client->id) }}
                    {{ Form::hidden('old_client_email', $client->User->email) }}

                        <input name="client[email]" type="text" placeholder="Client Email" value="{{ $client->User->email }}"/>
                        Edit Password:<br />
                        <input name="password" type="password" placeholder="Client Password" value=""/>
                        <input name="confirm_password" type="password" placeholder="Confirm Password" value=""/>
                        <button class="pull-left"  type="submit" name="submit" value="submit" style="">Save</button>
                    {{ Form::close() }}
                    <br style="clear:both;float:none;"/>
                </div>
                &nbsp; &nbsp; <b>Phone</b>: {{$client->phone }}<br />
                &nbsp; &nbsp; <b>Address</b>: {{$client->address }} {{$client->apt }} {{$client->city }}, {{$client->state }}, {{$client->zip }}<br />
                &nbsp; &nbsp; <b>Created</b>: {{ date('m/d/Y', strtotime($client->User->created_at)) }}<br /><br />
                &nbsp; &nbsp;

            </div>
            <div class="col-md-6">
                <b>{{$provider->business_name}}</b><br />
                &nbsp; &nbsp; <b>Email</b>: <a href="mailto:{{$provider->email }}">{{$provider->email }}</a><br />
                &nbsp; &nbsp; <b>Phone</b>: {{$provider->phone }}<br />
                &nbsp; &nbsp; <b>Fax</b>: {{$provider->fax }}<br />
                &nbsp; &nbsp; <b>Address</b>: {{$provider->address }} {{$provider->city }}, {{$provider->state }}, {{$provider->zip }}<br />
                &nbsp; &nbsp; <b>Website</b>: <a href="{{$provider->website }}" target="_blank">{{$provider->website }}</a>
            </div>
        </div>


    </fieldset>


    <fieldset>
        <div class="row">
            <div class="col-md-12">
            <h3>Client Signed Documents</h3>
            <table>
                <thead>
                <tr>
                    <th style="width:90px;">Date</th>
                    <th>Forms Sent</th>
                    <th style="width:90px;">Status</th>
                    <th style="width:140px;" class="text-right">Original Document</th>
                    <th style="width:140px;" class="text-right">Signed Document</th>
                    <th style="width:120px;" class="text-right">History</th>
                </tr>

                @foreach( $provider->signature_docs['documents']['document'] as $key=> $signed_docs )

                    <tr>
                        <td >{{ date('m/d/Y h:i a',strtotime($signed_docs['created-at'].' - 7 hours')) }}</td>
                        <td >{{ $signed_docs['doc_types'] }}</td>
                        <td >{{ ucwords($signed_docs['state']) }}</td>

                        <td class="text-right">
                            <a href="{{ action('ClientController@getDownloadRightSignatureDoc', $signed_docs['pdf-url'] ) }}" target="_blank" class="btn btn-xs btn-default">
                                <span class="glyphicon glyphicon-list-alt"></span> &nbsp; View
                            </a>
                        </td>
                        <td class="text-right">
                            @if($signed_docs['signed-pdf-url'] != '')
                                <a href="{{ urldecode($signed_docs['signed-pdf-url']) }}" target="_blank" class="btn btn-xs btn-default">
                                    <span class="glyphicon glyphicon-pencil"></span> &nbsp; View
                                </a>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="#" onclick="getSignedDoc('{{$signed_docs['guid'] }}')" class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal">
                                <span class="glyphicon glyphicon-search"></span> &nbsp; History
                            </a>
                        </td>
                    </tr>

                @endforeach

            </table>
                <!-- Modal -->
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog"  style="width:800px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Client Document</h4>
                            </div>
                            <div class="modal-body" id="doc_info">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                <!--<button type="button" class="btn btn-primary">Save changes</button>-->
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function getSignedDoc(guid) {
                        $('#doc_info').html('Loading <i class="fa fa-gear"></i>');
                        $.get('{{ action('AdminController@getSignedDoc') }}/'+guid, function (data) {
                            $('#doc_info').html(data);
                        });
                    }
                </script>
            </div>
        </div>
    </fieldset>



    <div class="row">
        <div class="col-md-3" >
           <button class="pull-right" onclick="$('#choose_download_forms').slideToggle();return false;">Download Client Forms</button>
        </div>

        <div class="col-md-2" style="padding-left:18px;">
            @if($provider->rightsignature_secret != '')
                <button class="pull-left" onclick="$('#choose_sign_forms').slideToggle();return false;">Sign Forms</button>

            @endif
        </div>

        <div class="col-md-4" style="padding-left:0px;" >
           @if($provider->freshbooks_clients_enabled == '1' and $provider->freshbooks_clients_people == '1' and $provider->freshbooks_api_url != '' and $provider->freshbooks_api_token != '')
               {{ Form::open(['action'=>'ClientController@postAddBillingClient','class'=>'form-horizontal','role'=>'form']) }}
               {{ Form::hidden('client_id',$client->id) }}
               {{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}
                    <button class="pull-left" type="submit" name="submit" value="submit"><?=($client->fb_client_id !=''?'Recreate Invoice From Client Details':'Create Invoice')?></button>
               {{ Form::close() }}

               <?php
               if($client->fb_client_id  != ''){
                   $domain = str_replace('https://', '', $provider->freshbooks_api_url);
                   $domain = str_replace('/','', str_replace('api/2.1/xml-in','', str_replace('.freshbooks.com','', str_replace('http://','', str_replace('https://','',$domain)))));

                   //$domain = substr($domain, 0, strpos($domain, '.freshbooks.com'));

                  # echo '<br /><a style="margin-left:15px;margin-top:10px;font-weight:bold;" href="https://'.$domain.'.freshbooks.com/showUser?userid='.$client->fb_client_id.'" target="_blank">View Client In Freshbooks</a>';

               }
               ?>
            @endif
        </div>
        <div class="col-md-3 " >

        </div>
</div>
<br />



@if($client->fb_client_id !='' and $provider->freshbooks_clients_enabled == '1' and $provider->freshbooks_clients_invoice == '1' and $provider->freshbooks_api_url != '' and $provider->freshbooks_api_token != '')

        <!--
        {{ Form::open(['action'=>'ClientController@postInvoiceClient','class'=>'form-horizontal','role'=>'form']) }}
        {{ Form::hidden('client_id',$client->id) }}
        {{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}
        <button class="pull-left" type="submit" name="submit" value="submit" style=""><?=($client->fb_invoice_id !=''?'Update':'Create')?> Invoice</button>
        {{ Form::close() }}
        -->

        <?php
            /*
        if($client->fb_invoice_id  != ''){
            $domain = str_replace('https://', '', $provider->freshbooks_api_url);
            $domain = str_replace('/','', str_replace('api/2.1/xml-in','', str_replace('.freshbooks.com','', str_replace('http://','', str_replace('https://','',$domain)))));
            //$domain = substr($domain, 0, strpos($domain, '.freshbooks.com'));

            #echo '<br /><a style="margin-left:15px;margin-top:10px;font-weight:bold;" href="https://'.$domain.'.freshbooks.com/showInvoice?invoiceid='.$client->fb_invoice_id.'" target="_blank">Edit Invoice In Freshbooks</a>';

           # echo '<br /><a style="margin-left:15px;font-weight:bold;" href="'.$client->fb_invoice['invoice']['links']['edit'].'" target="_blank">Edit Invoice</a>';


        }

        //LIST INVOICE ITEMS
*/
    ?>



    @if(is_array($client->fb_invoice))
    @if(is_array($client->fb_invoice['invoice']) && array_key_exists('invoice_id', $client->fb_invoice['invoice']) )
        {{ Form::open(['action'=>'ClientController@postUpdateInvoiceItems','class'=>'form-horizontal','role'=>'form']) }}
        {{ Form::hidden('client_id',$client->id) }}
        {{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}
        {{ Form::hidden('fb_client_id', $client->fb_client_id) }}
        {{ Form::hidden('fb_invoice_id', $client->fb_invoice_id) }}

        <fieldset>
            <div class="row">
                <div class="col-md-12">
                    {{ '<a style="font-weight:bold;float:right;" href="'.$client->fb_invoice['invoice']['links']['edit'].'" target="_blank">Edit Invoice In Freshbooks</a>';
}}<h3>Client Invoice</h3>

                    <table>
                        <thead>
                        <tr>
                            <th style="width:90px;">Name</th>
                            <th>Description</th>
                            <th style="width:90px;">Unit Cost</th>
                            <th style="width:90px;">Quantity</th>
                            <th style="width:140px;" class="text-right">Line Total</th>
                        </tr>
                        </thead>

                            @foreach( $client->fb_invoice['invoice']['lines']['line'] as $key=> $item )
                                <?php
                                    if(is_array($item['name']))$item['name'] = implode(' ',$item['name']);
                                    if(is_array($item['description']))$item['description'] = implode(' ',$item['description']);
                                    if(is_array($item['unit_cost']))$item['unit_cost'] = implode(' ',$item['unit_cost']);
                                    if(is_array($item['quantity']))$item['quantity'] = implode(' ',$item['quantity']);
                                    if(is_array($item['amount']))$item['amount'] = implode(' ',$item['amount']);
                                ?>
                                <tr>
                                    <td>{{$item['name']}}</td>
                                    <td>{{$item['description']}}</td>
                                    <td>${{$item['unit_cost']}}</td>
                                    <td>{{$item['quantity']}}</td>
                                    <td class="text-right">${{$item['amount']}}</td>
                                </tr>
                            @endforeach
                        <tr>
                            <td colspan="3" style="border-bottom:0px;"></td>
                            <td ><b>Invoice Total</b></td>
                            <td class="text-right" >${{$client->fb_invoice['invoice']['amount']}}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="border:none;"></td>
                            <td><b>Paid To Date</b></td>
                            <td class="text-right">${{$client->fb_invoice['invoice']['paid']}}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="border:none;"></td>
                            <td><b>Balance</b></td>
                            <td class="text-right">${{$client->fb_invoice['invoice']['amount_outstanding']}}</td>
                        </tr>

                    </table>

                    <button class="pull-right" type="submit" name="save_invoice" value="submit">Save Invoice</button>

                    <br />
                    <hr>
                    <b>Email Message</b><br /><br />
                    <div style="margin-left:25px;">
                        <label><b>To:</b></label> {{$client->User->email}}
                        <!--<input type="text" name="email_to" value="{{$client->User->email}}" placeholder="Email To" />-->
                        <input type="text" name="invoice_subject" placeholder="Email Subject" value="New invoice {{trim($client->fb_invoice['invoice']['invoice_id'],'0')}} from {{$provider->business_name}}, sent using QuikFiles" />
                        <textarea name="invoice_message" type="text" placeholder="Email Message">To view your invoice from {{$provider->business_name}} for ${{$client->fb_invoice['invoice']['amount_outstanding']}}, or to download a PDF copy for your records, click the link below:

{{$client->fb_invoice['invoice']['links']['client_view']}}

or copy and paste the following URL into your browser: {{$client->fb_invoice['invoice']['links']['client_view']}}
                        </textarea>
                        <?php
                        $domain = str_replace('https://', '', $provider->freshbooks_api_url);
                        $domain = substr($domain, 0, strpos($domain, '.freshbooks.com'));
                        ?>
                        <br />


                        Best regards,<br />
                        {{$provider->business_name}} ({{$provider->email}})<br />
                    </div>
                    <hr>
                    <button class="pull-right" type="submit" name="send_invoice" value="send">Send Invoice</button>
                </div>
            </div>

        </fieldset>
        {{ Form::close() }}
    @endif
    @endif

@endif




<div style="border:1px solid #eee;height:420px;padding-left:15px;display:none;width:100%;margin-left:25px;width:80%;margin-bottom:15px;font-size:12px;" id="choose_download_forms" class="row form-group"><br />
    {{ Form::open(['action'=>'ClientController@postCustomerDocuments','class'=>'form-horizontal','role'=>'form','target'=>'_blank']) }}
    {{ Form::hidden('client_id',$client->id) }}
    {{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}
    <b>Choose the documents you would like to combine and download</b><br />
    <a href="#" onclick="downcheckall();return false;"><b>Select All</b></a><br />
    <?php $doc_forms = ProviderController::getDocumentTypes(); ?>
    @foreach($doc_forms as $key=>$value)
        <div class="row form-group">
          <div class="col-sm-12">
              <input type="checkbox" class="download-forms-cb" name="download_forms[customer_form_{{$key}}]" id="download_forms_{{$key}}" value="{{$value}}" />
              {{$value}}
          </div>
        </div>
    @endforeach
    <div class="row form-group">
      <div class="col-sm-12 "><button type="submit" name="submit" id="submit" value="submit" class="step_submit pull-left" >Download</button><br class="clear" /></div>
    </div>
    {{ Form::close() }}
</div>

<div style="border:1px solid #eee;height:420px;padding-left:15px;display:none;width:100%;margin-left:25px;width:80%;margin-bottom:15px;font-size:12px;" id="choose_sign_forms" class="row form-group"><br />
    {{ Form::open(['action'=>'ClientController@postSendFormSigning','class'=>'form-horizontal','role'=>'form','target'=>'_blank']) }}
    {{ Form::hidden('client_id',$client->id) }}
    {{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}
    {{ Form::hidden('return_redirect_url', true) }}
    <script>
        var downallchecked=true;
        function downcheckall(){
            $('.download-forms-cb').prop('checked', downallchecked);
            downallchecked = !downallchecked;
        }
        var signallchecked=true;
        function signcheckall(){
            $('.sign-forms-cb').prop('checked', signallchecked);
            signallchecked = !signallchecked;
        }
    </script>
    <b>Combine and Send these documents for signing </b><br />
    <a href="#" onclick="signcheckall();return false;"><b>Select All</b></a><br />
    <?php $doc_forms = ProviderController::getDocumentTypes(); ?>
    @foreach($doc_forms as $key=>$value)
        <div class="row form-group">
            <div class="col-sm-12">
                <input type="checkbox" class="sign-forms-cb" name="download_forms[customer_form_{{$key}}]" id="sign_forms_{{$key}}" value="{{$value}}" />
                {{$value}}
            </div>
        </div>
    @endforeach
    <div class="row form-group">
        <div class="col-sm-12 "><button type="submit" name="submit" id="submit" value="submit" class="step_submit pull-left" >Send For Signing</button><br class="clear" /></div>
    </div>
    {{ Form::close() }}
</div>

<script>
$().ready(function(){
//location.href="#step{{Session::get('step')}}";
});
</script>
@endif

@if(Session::get('step')==1 || Session::get('inAdminGroup')!='')

{{ Form::open(['action'=>'ClientController@postSteps2','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
{{ Form::hidden('client_id',$client->id) }}
{{ Form::hidden('step',Session::get('step')) }}
{{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}

<fieldset id="step1">
<div class="row">
   <div class="col-md-6"><h3 class="pull-left">Cremation Package</h3></div>
   <div class="col-md-6">

       @if( is_object($provider) )
           @if($provider->id == '1')
               <a href="#" {{ (Session::get('inAdminGroup')=='Provider'?'style="display:none"':'') }} onclick="$('#select_location').slideToggle();return false;" class="pull-right">
                   Select a different provider?
               </a>
           @endif
       @endif


   </div>
</div>

<div id="select_location" class="row pull-left" style="display:none;">
   <div class="col-md-3">
       <select id="state" name="state" class="pull-right">
           <option value="">--</option>
           <?php
           //print_r($states);
           foreach($states as $key=>$row){
               echo '<option value="'.$row->name_shor.'">'.$row->name_long.'</option>';
           }
           ?>
       </select>
   </div>
   <div class="col-md-3">
       <select id="city" name="city">
           <option value="">--</option>
       </select>
   </div>
   <div class="col-md-3">
       <select id="new_provider" name="new_provider">
           <option value="">--</option>
       </select>
   </div>
   <div class="col-md-2">
       <button class="btn btn-primary" id="choose_provider">Select Provider</button>
   </div>
   <br style="float:none;clear:both;" /><Br />
</div>

<div class="row form-group">
   <div class="col-sm-12">
       <div>
       <strong>Package A: ${{$provider->pricing_options->basic_cremation}}</strong> <br />
       {{$provider->pricing_options->package_a_desc!=''?$provider->pricing_options->package_a_desc:'Basic Service Fee, care of your loved one in climatically controlled environment, obtaining Cremation Authorizations and filing the Death Certificate with State of California @ $585, Cash Advance of disposition permit $12.00, Crematory fee, Cremation container and Basic urn @ $190.' }}  <br /><br />

       <strong>Package B: ${{$provider->pricing_options->premium_cremation}}</strong> <br />
       {{$provider->pricing_options->package_b_desc!=''?$provider->pricing_options->package_b_desc:'Premium Package includes all services of Package A plus an urn. Refer to the General Price List for our urn selection.' }}  <br /><br />
       </div>

       <strong><u>Select Your Package</u></strong><br />
       Please select a package from the drop down below:<br />
       <select name="cremains_info[package_plan]" id="package_plan" class="form-control">
           <option value="0">Select Plan</option>
           <option value="1" {{ ($client->CremainsInfo->package_plan=="1" || $client->CremainsInfo->package_plan=="" || $client->CremainsInfo->package_plan=="0"?'selected':'') }}>Plan A</option>
           <option value="2" {{ ($client->CremainsInfo->package_plan=="2"?'selected':'') }}>Plan B</option>
       </select><br />

       <!--
       <strong>Plan A Description</strong>:<br />
       Basic cremation services, care of your loved one in climatically controlled environment, local transportation, obtaining signatures and filing the death certificate, securing permits from the state, ordering certified copies of the death certificate, preparation for shipping of the urn with cremains inside the U.S. (Shipping for additional cost) Basic urn provided or you may chose an urn from our catalog for an additional cost.<br /><br />

       <strong>Plan B Description</strong>:<br />
       Same as plan A, but choose one of the Urns from our list, included in the cost.<br /><br />
       -->

       <b><u>Package Additions</u></b><br />
       <?php

           if($provider->pricing_options->custom1_text!='')echo '<div class="price-options included-'.$provider->pricing_options->custom1_included.'">
                        <label for="custom1_req">'.$provider->pricing_options->custom1_text.': $'.$provider->pricing_options->custom1.'</label>
                        <input id="custom1_req" type="checkbox" '.($provider->pricing_options->custom1_req=='Y'?'checked=checked readonly':'').' class="required-'.$provider->pricing_options->custom1_req.'" name="cremains_info[custom1]" '.($client->CremainsInfo->custom1=="1"?'checked=checked':'').' value="1" />
                        </div>';


           if($provider->pricing_options->custom2_text!='')echo '<div class="price-options included-'.$provider->pricing_options->custom2_included.'">
                        <label for="custom2_req">'.$provider->pricing_options->custom2_text.': $'.$provider->pricing_options->custom2.'</label>
                        <input id="custom2_req"  type="checkbox" '.($provider->pricing_options->custom2_req=='Y'?'checked=checked readonly':'').' class="required-'.$provider->pricing_options->custom2_req.'" name="cremains_info[custom2]" '.($client->CremainsInfo->custom2=="1"?'checked=checked':'').' value="1" />
                        </div>';

           if($provider->pricing_options->custom3_text!='')echo '<div class="price-options included-'.$provider->pricing_options->custom3_included.'">
                        <label for="custom3_req">'.$provider->pricing_options->custom3_text.': $'.$provider->pricing_options->custom3.'</label>
                        <input id="custom3_req" type="checkbox" '.($provider->pricing_options->custom3_req=='Y'?'checked=checked readonly':'').' class="required-'.$provider->pricing_options->custom3_req.'" name="cremains_info[custom3]" '.($client->CremainsInfo->custom3=="1"?'checked=checked':'').' value="1" />
                        </div>';
      ?>

       <br /><br />
   </div>
</div>


<div class="row form-group">
   <div class="col-sm-12">
       Please select the status:<br />
       <input name="deceased_info[medical_donation]" type="checkbox" value="1" {{ ($client->DeceasedInfo->medical_donation=="1"?'checked':'') }}> Medical Donation
           <a href="#" data-toggle="tooltip" data-placement="bottom" class="tooltips" title="Selecting a status helps define for the provider the type of help needed, it can always be changed at any time select which one best fits your situation at this time.">?</a><br />
       <select name="deceased_info[cremation_reason]" class="form-control">
           <option value="planning_for_future" {{ ($client->DeceasedInfo->cremation_reason=="planning_for_future"?'selected':'') }}>Planning for Future</option>
           <option value="a_death_is_pending" {{ ($client->DeceasedInfo->cremation_reason=="a_death_is_pending"?'selected':'') }}>A death is pending</option>
           <option value="a_death_has_occurred" {{ ($client->DeceasedInfo->cremation_reason=="a_death_has_occurred"?'selected':'') }}>A death has occurred</option>
       </select>

       <br />
       <font style="color:red;font-weight:bold;" class="hideInAdmin">If the Death has occurred, Please CALL US now, so we can begin to help.</font><br />

   </div>
</div>

<div class="row form-group">
   <div class="col-sm-10 warn-login-text"></div>
   <div class="col-sm-2"><button type="submit" name="submit" id="submit" value="submit" class="step_submit" >Continue</button><br class="clear" /></div>
</div>
</fieldset>
<style>
label {float:left;margin-right:15px;font-weight:normal;}
.price-options {clear:both;float:none;margin-left:15px;}
</style>
<script src="{{ asset('js/jquery.chained.remote.min.js') }}"></script>
<script>
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

$("#city").remoteChained("#state", "{{action('StepController@getCities')}}");
$("#new_provider").remoteChained("#city", "{{action('StepController@getProvidersByCity')}}");
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
{{ Form::close() }}

@endif

@if(Session::get('step')==2 || Session::get('inAdminGroup')!='')
{{ Form::open(['action'=>'ClientController@postSteps3','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
{{ Form::hidden('client_id',$client->id) }}
{{ Form::hidden('step',Session::get('step')) }}
{{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}

<fieldset id="step2">
<h3>Certificate Information <p></p></h3>
<div class="row form-group">
   <div class="col-sm-4"><input name="deceased_info[first_name]" type="text" placeholder="First Name" value="{{$client->DeceasedInfo->first_name}}" /></div>
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
    <div class="col-sm-6" style="@if(!Session::get('inAdminGroup') || !($client->id!=''))display:none;@endif">Date of Death<br /><input name="deceased_info[dod]" id="dod" class="calendar" type="text" placeholder="Date of Death MM/DD/YYYY" value="{{date('m/d/Y', strtotime($client->DeceasedInfo->dod))}}" /></div>

    <div class="col-sm-6">Date of Birth<br /><input name="deceased_info[dob]" id="dob" type="text" class="calendar" placeholder="Date of Birth MM/DD/YYYY" value="{{date('m/d/Y', strtotime($client->DeceasedInfo->dob))}}" /></div>
</div>

<div class="row form-group">
   <div class="col-sm-6">
       Items with potential additional fees<br />
       <select name="deceased_info[weight]" class="form-control">
            <option value="">Weight</option>
            <option value="weight_lt_250" {{ ($client->DeceasedInfo->weight=="weight_lt_250"?'selected':'') }} >Weight less than 250lbs - Add ${{$provider->pricing_options->weight_lt_250}}</option>
            <option value="weight_lt_300" {{ ($client->DeceasedInfo->weight=="weight_lt_300"?'selected':'') }} >Weight 251-300lbs - Add ${{$provider->pricing_options->weight_lt_300}}</option>
            <option value="weight_lt_350" {{ ($client->DeceasedInfo->weight=="weight_lt_350"?'selected':'') }} >Weight 301-350lbs - Add ${{$provider->pricing_options->weight_lt_350}}</option>
            <option value="weight_gt_350" {{ ($client->DeceasedInfo->weight=="weight_gt_350"?'selected':'') }} >Weight 351lbs - Add ${{$provider->pricing_options->weight_gt_350}}</option>
        </select>
   </div>
   <div class="col-sm-6"><br />
       <select name="deceased_info[has_pace_maker]" class="form-control">
           <option value="">Does deceased have a pacemaker?</option>
           <option value="0" {{ ($client->DeceasedInfo->has_pace_maker=="0"?'selected':'') }}>Deceased DOES NOT have a pacemaker</option>
           <option value="1" {{ ($client->DeceasedInfo->has_pace_maker=="1"?'selected':'') }}>Deceased DOES have a pacemaker- Add ${{$provider->pricing_options->pacemaker}}</option>
       </select>
   </div>
</div>
<br />
<div class="row form-group">
   <div class="col-sm-10 warn-login-text"></div>
   <div class="col-sm-2"><button type="submit" name="submit" value="submit" class="step_submit">Continue</button><br class="clear" /></div>
</div>
</fieldset>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>

<script>
/*
$("#dob").datepicker( {
   dateFormat: "yyyy-mm-dd",
   changeMonth: true,
   changeYear: true
});*/


</script>
{{ Form::close() }}

@endif
@if(Session::get('step')==3 || Session::get('inAdminGroup')!='')
{{ Form::open(['action'=>'ClientController@postSteps4','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
{{ Form::hidden('client_id',$client->id) }}
{{ Form::hidden('step',Session::get('step')) }}
{{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}

<fieldset id="step3">
<h3>Life Data <p>Additional information about the deceased</p></h3>

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

<div class="row form-group">
       <div class="col-sm-12"><input name="deceased_family_info[mthr_birth_city]" value="{{$client->DeceasedFamilyInfo->mthr_birth_city}}" type="text" placeholder="Mothers's birth state" ></div>
</div>


<div class="row form-group">
   <div class="col-sm-10 warn-login-text"></div>
   <div class="col-sm-2"><button type="submit" name="submit" value="submit" class="step_submit">Continue</button><br class="clear" /></div>
</div>
</fieldset>
{{ Form::close() }}

@endif
@if(Session::get('step')==4 || Session::get('inAdminGroup')!='')
{{ Form::open(['action'=>'ClientController@postSteps5','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
{{ Form::hidden('client_id',$client->id) }}
{{ Form::hidden('step',Session::get('step')) }}
{{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}

<fieldset id="step4">
<h3>Location of Pending Death <p>Please enter the location of the pending death or deceased.</p></h3>
Location of Deceased:<br>
<div class="row form-group">
   <div class="col-sm-5">
       <select name="deceased_info_present_loc[type_of_location]" class="form-control pull-left">
           <option value="">   Type  of  Location  </option>
           <option value="Hospital" {{ ($client->DeceasedInfoPresentLoc->type_of_location=="Hospital"?'selected':'') }}> Hospital  </option>
           <option value="Coroner" {{ ($client->DeceasedInfoPresentLoc->type_of_location=="Coroner"?'selected':'') }}> Coroner  </option>
           <option value="Assisted  Living" {{ ($client->DeceasedInfoPresentLoc->type_of_location=="Assisted  Living"?'selected':'') }}> Assisted  Living </option>
           <option value="Residence" {{ ($client->DeceasedInfoPresentLoc->type_of_location=="Residence"?'selected':'') }}> Residence </option>
           <option value="Other" {{ ($client->DeceasedInfoPresentLoc->type_of_location=="Other"?'selected':'') }}> Other </option>
       </select> &nbsp;
       <a href="#" data-toggle="tooltip" style="position:absolute;top:0px;right:0px;" data-placement="bottom" class="tooltips pull-right" title="Although all field are required it may be information unknown at the present time. This information will eventually be needed to complete the Death Certificate. Your provider will help you with this information.">?</a>
       <br class="clear" />
   </div>
   <div class="col-sm-7">
       <input name="deceased_info_present_loc[location]" value="{{$client->DeceasedInfoPresentLoc->location}}" type="text" placeholder="Location Name" />
   </div>
</div>
<div class="row form-group">
   <div class="col-sm-12">
       <Br />
       <strong>Address of deceased location:</strong><br />
       <strong style="color: #FF0000">All fields are required</strong>
       <input name="deceased_info_present_loc[address]" value="{{$client->DeceasedInfoPresentLoc->address}}" type="text" placeholder="Address">
   </div>
</div>
<div class="row form-group">
   <div class="col-sm-4">
       <input name="deceased_info_present_loc[city]" value="{{$client->DeceasedInfoPresentLoc->city}}" type="text" placeholder="City">
   </div>
   <div class="col-sm-4">
       <input name="deceased_info_present_loc[state]" value="{{$client->DeceasedInfoPresentLoc->state}}" type="text" placeholder="State">
   </div>
   <div class="col-sm-4">
       <input name="deceased_info_present_loc[zip]" value="{{$client->DeceasedInfoPresentLoc->zip}}" type="text" placeholder="ZIP">
   </div>
</div>


<div class="row form-group">
   <div class="col-sm-10 warn-login-text"></div>
   <div class="col-sm-2"><button type="submit" name="submit" value="submit" class="step_submit">Continue</button><br class="clear" /></div>
</div>
</fieldset>
{{ Form::close() }}

@endif
@if(Session::get('step')==5 || Session::get('inAdminGroup')!='')
{{ Form::open(['action'=>'ClientController@postSteps6','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
{{ Form::hidden('client_id',$client->id) }}
{{ Form::hidden('step',Session::get('step')) }}
{{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}

<fieldset class="step-line-of-authority" id="step5">
<h3>Line of Authority
   <p>Are you the right person to authorize cremation
       <a href="#" data-toggle="tooltip" data-placement="bottom" class="tooltips" title="Some states require more than one person at the same level of authority to sign the cremation authorization. Additional signatures will be obtained by your provider through email signature systems.">?</a>
   </p>
</h3>
<div class="row form-group">
   <div class="col-sm-12">
       <input type="radio" id="relationship0" name="client[relationship]" value="" {{ ($client->relationship==""?'checked="checked"':'') }} >
           <label for="relationship0" class="">(Select One)</label><br style="float:none;clear:both;"/>
       <input type="radio" id="relationship1" name="client[relationship]" value="Making arrangements for myself" {{ ($client->relationship=="Making arrangements for myself"?'checked="checked"':'') }} >
           <label for="relationship1">Making arrangements for myself</label><br  style="float:none;clear:both;"/>
       <input type="radio" id="relationship2" name="client[relationship]" value="Agent or durable power of attorney" {{ ($client->relationship=="Agent or durable power of attorney"?'checked="checked"':'') }}>
           <label for="relationship2">Agent or durable power of attorney</label><br  style="float:none;clear:both;"/>
       <input type="radio" id="relationship3" name="client[relationship]" value="Spouse (legally married)" {{ ($client->relationship=="Spouse (legally married)"?'checked="checked"':'') }}>
           <label for="relationship3">Spouse (legally married)</label><br  style="float:none;clear:both;"/>
       <input type="radio" id="relationship4" name="client[relationship]" value="Majority of surviving children" {{ ($client->relationship=="Majority of surviving children"?'checked="checked"':'') }}>
           <label for="relationship4">Majority of surviving children</label><br  style="float:none;clear:both;"/>
       <input type="radio" id="relationship5" name="client[relationship]" value="Surviving parents" {{ ($client->relationship=="Surviving parents"?'checked="checked"':'') }}>
           <label for="relationship5">Surviving parents</label><br  style="float:none;clear:both;"/>
       <input type="radio" id="relationship6" name="client[relationship]" value="Majority of surviving brothers and sisters" {{ ($client->relationship=="Majority of surviving brothers and sisters"?'checked="checked"':'') }}>
           <label for="relationship6">Majority of surviving brothers and sisters</label><br  style="float:none;clear:both;"/>
       <input type="radio" id="relationship7" name="client[relationship]" value="Majority of surviving nieces and nephews" {{ ($client->relationship=="Majority of surviving nieces and nephews"?'checked="checked"':'') }}>
           <label for="relationship7">Majority of surviving nieces and nephews</label><br  style="float:none;clear:both;"/>
       <input type="radio" id="relationship8" name="client[relationship]" value="Majority of surviving next of kin" {{ ($client->relationship=="Majority of surviving next of kin"?'checked="checked"':'') }}>
           <label for="relationship8">Majority of surviving next of kin</label><br style="float:none;clear:both;" />
       <input type="radio" id="relationship9" name="client[relationship]" value="None of these, but have legal right to authorize" {{ ($client->relationship=="None of these, but have legal right to authorize"?'checked="checked"':'') }}>
           <label for="relationship9">None of these, but have legal right to authorize</label><br  style="float:none;clear:both;"/>
       <input type="radio" id="relationship10" name="client[relationship]" value="None of these - just helping a friend" {{ ($client->relationship=="None of these - just helping a friend"?'checked="checked"':'') }}>
           <label for="relationship10">None of these - just helping a friend</label>
   </div>
</div>

<div class="row form-group">
   <div class="col-sm-10 warn-login-text"></div>
   <div class="col-sm-2"><button type="submit" name="submit" value="submit" class="step_submit">Continue</button><br class="clear" /></div>
</div>
</fieldset>
{{ Form::close() }}

@endif

@if(Session::get('step')==6 || Session::get('inAdminGroup')!='')
{{ Form::open(['action'=>'ClientController@postSteps7','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
{{ Form::hidden('client_id',$client->id) }}
{{ Form::hidden('provider_id',$provider->id) }}
{{ Form::hidden('step',Session::get('step')) }}

<fieldset class="step-authorized-person" id="step6">
<div class="row form-group">
   <div class="col-sm-12">
       <h3>Authorized Person <p>Person authorized to make the arrangements &nbsp; <a href="#" data-toggle="tooltip" data-placement="bottom" class="tooltips" title="Some states refer to this person as the informant. This is the person providing the information for the Death Certificate and the one authorized to request any changes needed.">?</a></p></h3>
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
                       <img src="{{ $product->image }}" style="max-width:200px;width:auto;height:auto;max-height:200px;" />
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
   <div class="col-sm-10 warn-login-text"></div>
   <div class="col-sm-2"><button type="submit" name="submit" value="submit" class="step_submit">Continue</button><br class="clear" /></div>
</div>
</fieldset>

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
</script>
{{ Form::close() }}

@endif
@if(Session::get('step')==7 || Session::get('inAdminGroup')!='')
{{ Form::open(['action'=>'ClientController@postSteps8','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
{{ Form::hidden('client_id',$client->id) }}
{{ Form::hidden('step',Session::get('step')) }}
{{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}

<fieldset id="step7">
<h3>Certified Death Certificates <p>Please tell us how you want to receive the death certificates</p></h3>
<div class="row form-group">
   <div class="col-sm-12">
       How many copies of the death certificate do you need? &nbsp; - &nbsp;
       Each Certificate: <span style="font-weight:bold;">${{$provider->pricing_options->deathcert_each}}</span><br>
       <input name="cremains_info[number_of_certs]" type="text" value="{{$client->CremainsInfo->number_of_certs}}" />

   </div>
</div>
<div class="row form-group">
   <div class="col-sm-9">
       What would you like us to do with the certificates?<br />
       <select name="cremains_info[cert_plan]" class="form-control">
           <option value="deathcert_wurn" {{ ($client->CremainsInfo->cert_plan=="deathcert_wurn"?'selected':'') }}>Mail certificate(s) with urn- Add ${{$provider->pricing_options->deathcert_wurn}}</option>
           <option value="deathcert_cep" {{ ($client->CremainsInfo->cert_plan=="deathcert_cep"?'selected':'') }}>Mail certificate(s) separately- Add ${{$provider->pricing_options->deathcert_cep}}</option>
           <option value="deathcert_pickup" {{ ($client->CremainsInfo->cert_plan=="deathcert_pickup"?'selected':'') }}>Pick up certificate(s) at our office- Add ${{$provider->pricing_options->deathcert_pickup}}</option>
       </select>
   </div>
   <div class="col-sm-3">
       <a href="#" data-toggle="tooltip" data-placement="bottom" class="tooltips pull-right" title="If the certificates are not going to be shipped with the urn then the shipping page is not required.">?</a>
   </div>
</div>
<div class="row form-group">
   <div class="col-sm-12 hideInAdmin"><br>
       <a  href='#' onclick="return false;">How many certified copies do I need?</a><br>
       <i><span class="auto-style9">Window not opening? Try disabling your popup blocker</span></i><br>
   </div>
</div>

<div class="row form-group">
   <div class="col-sm-10 warn-login-text"></div>
   <div class="col-sm-2"><button type="submit" name="submit" value="submit" class="step_submit">Continue</button><br class="clear" /></div>
</div>
</fieldset>
{{ Form::close() }}

@endif
@if(Session::get('step')==8 || Session::get('inAdminGroup')!='')
{{ Form::open(['action'=>'ClientController@postSteps9','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
{{ Form::hidden('client_id',$client->id) }}
{{ Form::hidden('step',Session::get('step')) }}
{{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}

<fieldset id="step8">
<h3>Shipping Information <p>Shipping Information for the cremains</p></h3>
<div class="row form-group">
   <div class="col-sm-5"><input type="text" placeholder="First Name" name="cremains_info[shipto_first_name]" value="{{$client->CremainsInfo->shipto_first_name}}" ></div>
   <div class="col-sm-2"><input type="text" placeholder="Middle" name="cremains_info[shipto_middle_name]" value="{{$client->CremainsInfo->shipto_middle_name}}" ></div>
   <div class="col-sm-5"><input type="text" placeholder="Last Name" name="cremains_info[shipto_last_name]" value="{{$client->CremainsInfo->shipto_last_name}}" ></div>
</div>
<div class="row form-group">
   <div class="col-sm-7"><input type="text" placeholder="Street Address" name="cremains_info[shipto_address]" value="{{$client->CremainsInfo->shipto_address}}" ></div>
   <div class="col-sm-5"><input type="text" placeholder="Apartment/Suite" name="cremains_info[shipto_apt]" value="{{$client->CremainsInfo->shipto_apt}}" ></div>
</div>
<div class="row form-group">
   <div class="col-sm-5"><input type="text" placeholder="City" name="cremains_info[shipto_city]" value="{{$client->CremainsInfo->shipto_city}}" ></div>
   <div class="col-sm-2"><input type="text" placeholder="State" name="cremains_info[shipto_state]" value="{{$client->CremainsInfo->shipto_state}}" ></div>
   <div class="col-sm-5"><input type="text" placeholder="ZIP" name="cremains_info[shipto_zip]" value="{{$client->CremainsInfo->shipto_zip}}" ></div>
</div>
<div class="row form-group">
   <div class="col-sm-6"><input type="text" placeholder="Phone Number" name="cremains_info[shipto_phone]" value="{{$client->CremainsInfo->shipto_phone}}" ></div>
   <div class="col-sm-6"><input type="text" placeholder="Email Address" name="cremains_info[shipto_email]" value="{{$client->CremainsInfo->shipto_email}}" > </div>
</div>
<div class="row form-group">
   <div class="col-sm-12">
       <select name="cremains_info[cremation_shipping_plan]" class="form-control">
           <option value="burial" {{ ($client->CremainsInfo->cremation_shipping_plan==""?'selected':'') }}> I will pick up at your facility - Add $0.00</option>
           <option value="scatter_on_land" {{ ($client->CremainsInfo->cremation_shipping_plan=="scatter_on_land"?'selected':'') }}> Scatter on Land (non-witness, non-recoverable) - Add ${{$provider->pricing_options->scatter_on_land}} </option>
           <option value="scatter_at_sea" {{ ($client->CremainsInfo->cremation_shipping_plan=="scatter_at_sea"?'selected':'') }}> Scatter at Sea (non-witness, non-recoverable) - Add ${{$provider->pricing_options->scatter_at_sea}} </option>
           <option value="ship_to_you" {{ ($client->CremainsInfo->cremation_shipping_plan=="ship_to_you"?'selected':'') }}> Ship to address via certified registered mail - Add ${{$provider->pricing_options->ship_to_you}} </option>
       </select>
   </div>
   <div class="col-sm-12"><span id="shippingtext" style="FONT-SIZE: small; FONT-WEIGHT: bold; text-align: center">We can only ship by registered U.S. mail. UPS, Fedex, or other providers will NOT ship remains.</span></div>
</div>


<div class="row form-group">
   <div class="col-sm-10 warn-login-text"></div>
   <div class="col-sm-2"><button type="submit" name="submit" value="submit" class="step_submit">Continue</button><br class="clear" /></div>
</div>
</fieldset>

{{ Form::close() }}

@endif
@if(Session::get('step')==9 || Session::get('inAdminGroup')!='')
{{ Form::open(['action'=>'ClientController@postSteps10','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
{{ Form::hidden('client_id',$client->id) }}
{{ Form::hidden('step',Session::get('step')) }}
{{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}


<fieldset id="step9">

<h3 class="hideInAdmin">FTC Disclosures <p>Legal Authorizations</p></h3>
<div class="row form-group hideInAdmin">
   <div class="col-sm-12" style="padding-left:30px;padding-right:0px;">
       <a href="#" onclick="$('#ftc_disclosures').slideToggle();return false;">Read FTC Disclosures</a><br />
       <div id="ftc_disclosures" style="height:300px;overflow-y:scroll;background-color:#fff;display:none;border:1px solid #999;">
<table border="1" style="border: thin solid #C0C0C0"><tbody><tr><th align="center" class="auto-style3" scope="col">Item #</th><th class="auto-style2" scope="col">Description</th><th class="auto-style2" scope="col">Explanation</th></tr><tr><td align="center" class="auto-style3">1</td><td class="auto-style2">Purchaser received a printed General Price List prior to discussing or upon beginning discussion of, the prices of funeral goods or funeral services, the overall type of funeral disposition, or the specific funeral goods or funeral services offered by the funeral home.</td><td class="auto-style2">You were able to view and print a price list prior to choosing the services. This is to assure that no pressure was applied to have you choose a specific option.</td></tr><tr><td align="center" class="auto-style3">2</td><td class="auto-style2">Purchaser was advised that State law does not require embalming except in certain special cases. If embalming was provided for a fee, it was done with purchaser's approval or the permission of someone authorized to give approval. Purchaser was not told that embalming is required for direct cremation, immediate burial, or a closed casket funeral without viewing or visitation when refrigeration is available and when state or local law does not require embalming.</td><td class="auto-style2">This is about embalming, which is a service that we don't provide. It is to assure that no one talked you into an embalming service.</td></tr><tr><td align="center" class="auto-style3">3</td><td class="auto-style2">A prepaid benefits contract for the deceased, if provided, was applicable to the funeral or cremation service provided.</td><td class="auto-style2">Some people purchase prepaid funeral contracts. If you had one, and provided the details to us, this says we abided by it.</td></tr><tr><td align="center" class="auto-style3">4</td><td class="auto-style2">Applicable Only to Merchandise: Purchaser was advised that the only warranties, expressed or implied, extended in connection with any funeral goods sold with the funeral service were the express written warranties, if any, extended by the manufacturers thereof. No express warranties, and no warranties of merchantability or fitness for a particular purpose, were extended by the funeral home to purchaser with respect to those funeral goods.</td><td class="auto-style2">This says that we didn't make any promises about the Urn for fitness of use. Only the manufacturer provides a warranty for the Urn.</td></tr><tr><td align="center" class="auto-style3">5</td><td class="auto-style2">Valuables placed with the deceased: Purchaser was told that anything of sentimental or monetary value placed on the body would receive special care, but that the funeral home or mortuary accepts no responsibility for such items.</td><td class="auto-style2">This applies to burial (even though we only offer cremations, we still have to have it on the form). It says that if there were any watches or anything of sentimental or monetary value with the deceased, that we accept no responsibility for them.</td></tr><tr><td align="center" class="auto-style3">6</td><td class="auto-style2">I agree to indemnify and hold the Crematory harmless from any and all claims or damages, including damage to the retort(s) or injuries suffered by the Crematory’s employees, which arise from my failure to timely notify the Crematory of any mechanical or radioactive implants in the body of the Decedent.</td><td class="auto-style2">This applies to pace makers or other electronic devices. It says that if you did not inform us of a pacemaker or other device, that damages can result at the crematory, and you may be held responsible.</td></tr><tr><td align="center" class="auto-style3">7</td><td class="auto-style2">Items such as personal mementos, jewelry, dental gold and silver, prostheses and other foreign materials placed in the cremation chamber with the Decedent will either be destroyed or rendered unrecognizable. If any such items are recovered from the cremation chamber I authorize the Crematory to dispose of them.</td><td class="auto-style2">This applies to jewelry or personal items that may be with the deceased. It says that the crematory can dispose of them.</td></tr><tr><td align="center" class="auto-style3">8</td><td class="auto-style2">The Cremation Process. I acknowledge the following: The human body burns with the container, or other material in the cremation chamber. Some bone fragments are not combustible at the incineration temperature and, as a result, remain in the cremation chamber. During the cremation, the contents of the chamber may be moved to facilitate incineration. The chamber is composed of ceramic or other material which disintegrates slightly during each cremation and the product of that disintegration is commingled with the cremated remains. Nearly all of the contents of the cremation chamber, consisting of the cremated remains, disintegrated chamber material, and small amounts of residue from previous cremations, are removed together and crushed, pulverized, or ground to facilitate interment or scattering. Some residue remains in the cracks and uneven places of the chamber. Periodically, the accumulation of this residue is removed and interred in a dedicated cemetery property, or scattered at sea.</td><td class="auto-style2">This applies to the cremation process itself. Periodically the cremation chamber is cleaned and some small amount of ashes may remain. These are disposed of at a cemetary or scattered at sea.</td></tr><tr><td align="center" class="auto-style3">9</td><td class="auto-style2">Time of Cremation. The cremation will take place after all required permits are obtained, this completed and signed Authorization is received by the Crematory, and after any scheduled funeral ceremony at which the decedent’s body is to be present has been concluded. The Crematory will perform the cremation according to its schedule (unless a specific date and time is requested), and at it’s discretion, without obtaining any further authorizations or instructions, unless the right of the person signing this document to authorize the cremation is contested by someone. In that event the Crematory may delay the cremation while it determines whether and how to proceed. The normal cremation process may take a minimum of 8 working days to a possible 18 days.</td><td class="auto-style2">This applies to the cremation process. The crematory will cremate based on their schedule without obtaining any further authorizations other than those provided in the forms. This process can take from 8 to 18 working days.</td></tr><tr><td align="center" class="auto-style3">10</td><td class="auto-style2">Casket or Urn provided by family: Purchaser understands if they provided alternative container for burial or cremation, the mortuary is relieved of any liability in the event such container is found not to be of adequate strength or structural quality for intended use.</td><td class="auto-style2">If you provided your own urn, this relieves us of any liability if it is not strong enough or does not hold up to shipping.</td></tr><tr><td align="center" class="auto-style3">11</td><td class="auto-style2">I authorize the Crematory to release the cremated remains back to the Funeral Home to take the action I’ve indicated with respect to the cremated remains. For your convenience, we offer a minimum fiberboard urn to hold the cremated remains.</td><td class="auto-style2">This tells the crematory that they can return the cremated remains to us and we will be responsible for following your instructions for shipping the remains to you, or scattering or burial if you have asked us to do so.</td></tr><tr><td align="center" class="auto-style3">12</td><td class="auto-style2">I understand that if the remains are not picked up within twenty (20) days after the cremation, the Funeral Home may deliver the remains to a licensed cemetery for final disposition in a manner which may make the remains non-recoverable.</td><td class="auto-style2">This says that if you don't make arrangements for us to ship the cremated remains or pick them up, that after 20 days, we may properly dispose of them at a cemetary or by scattering.</td></tr><tr><td align="center" class="auto-style3">13</td><td class="auto-style2">Remains will be mailed via U.S. Postal Service, registered with return receipt requested. I understand that the Funeral Home is acting solely as my agent in mailing the remains, and I agree that the Funeral Home shall not be liable if the remains are lost or damaged while in the custody of the U.S. Postal Service.</td><td class="auto-style2">This authorizes us to ship the cremated remains to you via US Postal service and that we are not held liable if they do not deliver them.</td></tr><tr><td align="center" class="auto-style3">14</td><td class="auto-style2">I understand that the Funeral Home is acting solely as my agent as an accommodation to me in arranging for the scattering of the remains. I agree that the Funeral Home shall not be liable for any failure to properly scatter the remains.</td><td class="auto-style2">If you requested us to scatter the remains for you, this authorizes us to do so and says we are not held liable if someone feels it was not properly done.</td></tr><tr><td align="center" class="auto-style3">15</td><td class="auto-style2">Obligation of Crematory; Limitation on Damages. The obligation of the Crematory shall be limited to the cremation of the Decedent and the disposition of the cremated remains as directed herein. I agree to release and hold the Crematory, its affiliated companies and their employees and agents harmless from any and all loss, damages, liability or causes of action (including attorneys’ fees and costs of litigation) in connection with the cremation and disposition of the cremated remains as authorized herein, or the failure to properly identify the Decedent or to take possession of or make arrangements for the permanent disposition of the cremated remains. No warranties, express or implied, are made by the Crematory and damages shall be limited to the refund of the fee paid for the cremation.</td><td class="auto-style2">This says you are authorizing the crematory to cremate the remains. You hold them harmless if a mistake is made and that any damage charges are limited to the amount they were paid for this service.</td></tr><tr><td align="center" class="auto-style3">16</td><td class="auto-style2">Estimated costs: Certain charges may be estimated and if the difference between such estimates and such actual charges is less than $ 10.00, no refund to you or billing us for the difference will be made.</td><td class="auto-style2">This says that we have provided an estimated cost and if there is a difference of $10 either owed to us or to you, that we will call it good without extra billing for the difference.</td></tr></tbody></table><br>
       </div>
       By clicking submit, you agree that you have read and understand the disclosures listed above.
   </div>
</div>

<br />
<h3>Payment Summary <p>Itemization of goods and services <a href="#" data-toggle="tooltip" data-placement="bottom" class="tooltips" title="If you need to change a choice that affects the summary then return to the page and change the selection your summary will update.">?</a></p>

@if($provider->ProviderPriceSheet!=null)
   <a style="font-weight:bold;font-size:12px;" href="{{asset('provider_files/'.$provider->id.'/'.$provider->ProviderPriceSheet->file_name)!=''?asset('provider_files/'.$provider->id.'/'.$provider->ProviderPriceSheet->file_name):'href="http://www.californiacremationservices.com/pricelist.pdf"'}}" target="_blank" >View Pricing Sheet</a>
@endif
</h3>

<div class="row ">
   <div class="col-sm-12" style="background-color:#fff;">
       <table style="width:100%;empty-cells:show;" align=right>
           <th width="45%">Name</th><th width="45%">Description</th><th width="50">Price</th>
           <?php
               if(is_array($client->sale_summary_r['report']))
               foreach($client->sale_summary_r['report'] as $key=>$value){
                   echo '<tr><td>'.$value['name'].'</td><td>'.$value['desc'].'</td><td >'.$value['price'].'</td></tr>';
               }
           ?>

       </table>
   <div class="col-sm-12 pull-right" style="text-align:right;font-weight:bold;font-size:16px;">Total Summary: $<?=$client->sale_summary_r['total']?></div>
   <br class="clear"/><br class="clear"/>
   </div>

</div>
<div class="row form-group">
   <div class="col-sm-10 warn-login-text"></div>
   <div class="col-sm-2"><button type="submit" name="submit" value="submit" class="step_submit">Continue</button><br class="clear" /></div>
</div>
</fieldset>
{{ Form::close() }}
@endif

@if(Session::get('step')==10 || Session::get('inAdminGroup')!='')
{{ Form::open(['action'=>'ClientController@postSteps11','class'=>'form-horizontal','role'=>'form','files'=>true]) }}
{{ Form::hidden('client_id',$client->id) }}
{{ Form::hidden('step',Session::get('step')) }}
{{ Form::hidden('provider_id', (is_object($provider)?$provider->id:'1')) }}


<fieldset class="step-review-print" id="step10">
<h3>Confirm/Submit <p>You're almost done!</p></h3>
<div class="row form-group {{($errors->first('Confirmed Legal Authorization Checkbox')!=''?'has-error has-feedback':'')}}">
   <div class="col-sm-12">
       <input id="confirmed_legal_auth" type="checkbox" value="1" name="client[confirmed_legal_auth]" {{($client->confirmed_legal_auth=="1"?'checked=checked':'')}}>
       <label for="confirmed_legal_auth">I confirm that the legally authorized person has provided these instructions</label><br>
   </div>
</div>
<div class="row form-group {{($errors->first('Your Name')!=''?'has-error has-feedback':'')}}">
   <div class="col-sm-12">
       Type name here: <input type="text" class="form-control" id="confirmed_legal_auth_name" name="client[confirmed_legal_auth_name]" value="{{$client->confirmed_legal_auth_name}}"><br>
       <input id="confirmed_correct_info" type="checkbox" value="1" name="client[confirmed_correct_info]" {{($client->confirmed_correct_info=="1"?'checked=checked':'')}}>
       <label for="confirmed_correct_info" >I confirm that the information provided is correct</label><br>
   </div>
</div>
<div class="row form-group {{($errors->first('Your Initials')!=''?'has-error has-feedback':'')}}">
   <div class="col-sm-12">
       Enter initials here: <input type="text" class="form-control" id="confirmed_correct_info_initial" name="client[confirmed_correct_info_initial]" value="{{$client->confirmed_correct_info_initial}}" />
       <!--
       <a class="btn btn-primary" id="download_forms" download="CremationDocuments{{date('Y-m-d')}}.pdf" target="_blank" href="{{ action('ClientController@getCustomerDocuments', array($client->id, $provider->id)) }}">Download Forms</a>
       <div id="loading_gif" style="display:none;">
       <br>
       <img src="/images/ajax-loader.gif"> <b>Downloading Forms... please wait</b>
       </div>-->

   </div>
</div>

<div class="row form-group">
   <div class="col-sm-12"><br>
       <label for="ssn" >Please enter the last 4 digits of your social security number</label>
       Type Last 4 of SSN here: <input type="text" class="form-control" id="ssn" name="client[ssn]" value="{{$client->ssn}}">
   </div>
</div>
<Br /><Br />
<div class="row form-group">
   <div class="col-sm-10 warn-login-text"></div>
   <div class="col-sm-2"><button type="submit" name="submit" value="submit" class="step_submit">Submit</button><br class="clear" /></div>
</div>
</fieldset>
<script>
@if(!Session::get('inAdminGroup') || $client->id=='')
function validate(){
   if(!$('#confirmed_legal_auth').prop('checked')){
       alert('Please Mark The Confirm Legal Authorization Checkbox');
       return false;
   }
   if($('#confirmed_legal_auth_name').val()==""){
       alert('Please Enter Your Name');
       return false;
   }
   if(!$('#confirmed_correct_info').prop('checked')){
       alert('Please Mark The Confirm Information Is Correct Checkbox');
       return false;
   }
   if($('#confirmed_correct_info_initial').val()==""){
       alert('Please Enter Your Initials');
       return false;
   }
   if($('#ssn').val()==""){
       alert('Please Enter The Last 4 Digits of Your Social Security Number');
       return false;
   }
}
$(function(){
   $('form').on('submit', function(){
       return validate();
   });
});
@endif
</script>
{{ Form::close() }}
@endif

@if(Session::get('step')==11)

<fieldset id="step11">
<h3>Thank You</h3>
<div class="row form-group">
   <div class="col-sm-12">
           Thank you for using our service!<br /><br />

           I understand your loss, and I hope that this simple process has made it easier as you pass through this very difficult time.<br /><br />

           {{$provider->business_name}} <br />
           Phone: {{$provider->phone}}<br />
           Fax: {{$provider->fax}}<br /><br />

           Email: <a href="mailto:{{$provider->email}}">{{$provider->email}}</a>
   </div>
</div>
</fieldset>

@endif               



</div>


@stop