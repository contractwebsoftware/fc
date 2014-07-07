<div id="sidebar" class="col-sm-2" >
	<ul id="step_navigation_menu" class="nav nav-pills nav-stacked span2">
    
          @foreach($steps_r as $step )
          <li class="<?=($step->step_number==Session::get('step')?'active':'')?>"><a href="steps{{ $step->step_number }}">{{ $step->title }}</a></li>
          @endforeach  
	</ul>
	<div class="clear"></div>
</div>