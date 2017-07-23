@extends('layouts.client')
@section('content')
<?php
    if(!is_null(Session::get('no-frame')))$noframe = Session::get('no-frame');
    else $noframe = false;
?>
<div class="col-sm-12">

    <div name="success" id="success">

        <div class="alert alert-success">
            <h3>Success, A Representative from Rogue Valley Cremation will call to confirm with the next regular business day</h3>
        </div>
    </div>
</div>
<script>window.scrollTo(0);
    window.location("#success");</script>


@stop