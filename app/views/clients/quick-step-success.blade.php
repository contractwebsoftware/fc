@extends('layouts.client')
@section('content')
<?php
    if(!is_null(Session::get('no-frame')))$noframe = Session::get('no-frame');
    else $noframe = false;

    if(is_object(Session::get('provider'))){
        $provider = Session::get('provider');
        $provider_name = 'from '.$provider->name;
    }
else $provider_name = '';
?>
<div class="col-sm-12">

    <div name="success" id="success">

        <div class="alert alert-success">
            <h3>Success, A Representative {{ $provider->name }} will call to confirm with the next regular business day</h3>
        </div>
    </div>
</div>
<script>
    window.scrollTo(0,0);
    window.location.href="#success";
    $('body').scrollTo(0,0);
    $("#success")[0].scrollIntoView();
    document.getElementById("success").scrollIntoView();
</script>


@stop