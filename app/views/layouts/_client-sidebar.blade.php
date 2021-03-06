<div id="sidebar" class="col-sm-3 hideInAdmin" >
	<ul id="step_navigation_menu" class="nav nav-pills nav-stacked span2">
    
          @foreach($steps_r as $step )
            <li class="<?=($step->step_number==Session::get('step')?'active':'')?>"><a href="steps{{ $step->step_number.'?provider_id='. Input::get('provider_id') }}">{{ $step->title }}</a></li>
          @endforeach  
	</ul>
	<div class="clear"></div>
        
        <?php
            $vid = "016.m4v";
            switch(Session::get('step')){
                case 1: $vid = "006.m4v"; break;
                case 2: $vid = "007.m4v"; break;
                case 3: $vid = "010.m4v"; break;//5
                case 4: $vid = "009.m4v"; break;
                case 5: $vid = "008.m4v"; break;//9
                case 6: $vid = "011.m4v"; break;
                case 7: $vid = "013.m4v"; break;//11
                case 8: $vid = "005.m4v"; break;//013
                case 9: $vid = "014.m4v"; break;
                case 10: $vid = "015.m4v"; break;
                case 11: $vid = "016.m4v"; break;
            }
        /*
         * 005 =
         * 006 = death cert information
         * 007 = family info
         * 008 = location address
         * 009 = line of authority
         * 010 =
         * 011 = death cert copies
         * 013 = shipping info
         * 014 =
         * 015 =
         * 016 =
         */
        ?>
        
        
        <div style="position: relative; display: block; width: 270px; height: 270px;margin-left:-20px;margin-top:15px;">
            <object type="application/x-shockwave-flash" data="{{ asset('/videos/jwplayer/jwplayer.flash.swf') }}" width="100%" height="100%" bgcolor="#e9f4ff" id="vid" name="vid" tabindex="0">
                <param name="allowfullscreen" value="true">
                <param name="allowscriptaccess" value="always">
                <param name="seamlesstabbing" value="true">
                <param name="wmode" value="opaque">
            </object>

            <script type="text/javascript">
                jwplayer("vid").setup({
                    file: "{{ asset('/videos/'.$vid) }}",
                    width: 270,
                    primary: 'flash',
                    <?=(Session::get('step')==11?'autostart: true,':'')?>
                    image: "{{ asset('img/forcremation-video-start.jpg') }}"
                });
            </script>

        </div>
</div>