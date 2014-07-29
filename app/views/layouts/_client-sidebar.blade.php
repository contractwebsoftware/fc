<div id="sidebar" class="col-sm-3" >
	<ul id="step_navigation_menu" class="nav nav-pills nav-stacked span2">
    
          @foreach($steps_r as $step )
          <li class="<?=($step->step_number==Session::get('step')?'active':'')?>"><a href="steps{{ $step->step_number }}">{{ $step->title }}</a></li>
          @endforeach  
	</ul>
	<div class="clear"></div>
        
        <?php
            switch(Session::get('step')){
                case 1: $vid = "005.m4v"; break;
                case 2: $vid = "006.m4v"; break;
                case 3: $vid = "007.m4v"; break;
                case 4: $vid = "008.m4v"; break;
                case 5: $vid = "009.m4v"; break;
                case 6: $vid = "010.m4v"; break;
                case 7: $vid = "011.m4v"; break;
                case 8: $vid = "013.m4v"; break;
                case 9: $vid = "014.m4v"; break;
                case 10: $vid = "015.m4v"; break;
                case 11: $vid = "016.m4v"; break;
            }
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
                    image: "{{ asset('http://app.forcremation.com/images/forcremation-video-start.jpg') }}"
                });
            </script>

        </div>
</div>