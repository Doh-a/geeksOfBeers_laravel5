@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
:: Biere
@stop

{{-- New Laravel 4 Feature in use --}}
@section('styles')
@parent
body {
	background: #f2f2f2;
}

@stop

@section('user-context')

	
@stop

@section('more-content')


@stop

{{-- Content --}}
@section('content')
	<div class="panel panel-default">
		<h2>Chercher une bière</h2>
			<div id="scrollable-dropdown-menu-beers">
				<input class="typeahead" type="text" id="beersSearchBox" placeholder="Nom de la bière">
			</div>
		<br />
	</div>

	<div class="panel panel-default">
		<div class="panel-body">
			<h2><b>Top 5 des bières sur GeeksOfBeers :</b></h2>
			@foreach($topBeers as $tmpBiere)
				<div style="float : left; margin : 12px; width : 95px; padding : 5px; height : 240px;" align="center">
					<a href="biere/{{ $tmpBiere->id_biere }}">
						<div style="height : 60px">
								{!! Html::image((asset("assets/img/bieres/$tmpBiere->etiquette")), 'Logo', array("width" => "50px;")) !!}
						</div>
						{{$tmpBiere->nom_biere }}
					</a>
				</div>
			@endforeach

			<div style="width: 33%; float:left">
				<h2><b>Blondes :</b></h2>
				@foreach($topBlondBeers as $tmpBiere)
					<ol>
						<li>
							<a href="biere/{{ $tmpBiere->id_biere }}">
								{!! Html::image((asset("assets/img/bieres/$tmpBiere->etiquette")), 'Logo', array("width" => "50px;")) !!}
								{{$tmpBiere->nom_biere }}
							</a>
						</li>
					</ol>
				@endforeach
			</div>
			<div style="width: 33%; float:left">
				<h2><b>Blanches :</b></h2>
				@foreach($topWhiteBeers as $tmpBiere)
					<ol>
						<li>
							<a href="biere/{{ $tmpBiere->id_biere }}">
								{!! HTML::image((asset("assets/img/bieres/$tmpBiere->etiquette")), 'Logo', array("width" => "50px;")) !!}
								{{$tmpBiere->nom_biere }}
							</a>
						</li>
					</ol>
				@endforeach
			</div>
			<div style="width: 33%; float:left;">
				<h2><b>Noires :</b></h2>
				@foreach($topBlackBeers as $tmpBiere)
					<ol>
						<li>
							<a href="biere/{{ $tmpBiere->id_biere }}">
								{!! Html::image((asset("assets/img/bieres/$tmpBiere->etiquette")), 'Logo', array("width" => "50px;")) !!}
								{{$tmpBiere->nom_biere }}
							</a>
						</li>
					</ol>
				@endforeach
			</div>
		</div>
	</div>
@stop

@section('footer-scripts')
	<script>
		$(document).ready(function() {
			var foundBeers = new Bloodhound({
			  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
			  queryTokenizer: Bloodhound.tokenizers.whitespace,
			  limit: 10,
			  remote: {
					url: 'findBeer',
					// the json file contains an array of strings, but the Bloodhound
					// suggestion engine expects JavaScript objects so this converts all of
					// those strings
					replace: function(url, query) {
						return url + "/" + query;
					},
					ajax : {
						beforeSend: function(jqXhr, settings){
						   settings.data = $.param({q: $('beersSearchBox').val()})
						},
						type: "GET"
					}
				}
			});
			 
			// kicks off the loading/processing of `local` and `prefetch`
			foundBeers.initialize();
			 
			// passing in `null` for the `options` arguments will result in the default
			// options being used
			@include('partials/handlebars-template/found_beers')
		});
	</script>
@stop