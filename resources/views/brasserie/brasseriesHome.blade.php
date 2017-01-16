@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
:: Brasserie
@stop

{{-- New Laravel 4 Feature in use --}}
@section('styles')
@parent
body {
	background: #f2f2f2;
}

@stop

@section('user-context')

	<div class="panel panel-default">
		<div class="panel-body">
			
		</div>
	</div>
@stop

@section('more-content')

<div class="panel panel-default">
	<div class="panel-body">

	</div>
</div>
@stop

{{-- Content --}}
@section('content')
<div class="panel panel-default">
	
</div>

<div class="panel panel-default">
	<div class="panel-body">
		<div style="height : 300px;">
			<b>{{ count($randomBreweries) }} brasseries au hasard :</b><br />
			@foreach($randomBreweries as $tmpBrewery)
				<div style="background-color : #DDDDDD; float : left; margin : 12px; width : 170px; padding : 5px; height : 240px;">
					<div style="height : 190px">
						<a href="brasserie/{{ $tmpBrewery->id_brasserie }}">
							{!! Html::image((asset("assets/img/brasseries/$tmpBrewery->img")), 'Logo', array("width" => "160px;")) !!}
						</a>
					</div>
					{!! link_to("brasserie/$tmpBrewery->id_brasserie", $tmpBrewery->nom_brasserie ) !!}
				</div>
			@endforeach
		</div>
	</div>
</div>
@stop

@section('footer-scripts')

@stop