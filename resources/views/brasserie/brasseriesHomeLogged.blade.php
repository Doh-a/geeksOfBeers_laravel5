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
			@if (Auth::check())
				<div id="user-context-title">
					<img src="assets/img/avatars/{{ Auth::user()->getAvatarId() }}_40.jpg" alt="Your avatar" class="img-rounded" /> <b>Vous et les brasseries</b>
				</div>
				<div id="user-context-content">
					<div id="spinner-user-content" class="spinner">
					  <div class="bounce1"></div>
					  <div class="bounce2"></div>
					  <div class="bounce3"></div>
					</div>
					<div id="user-loaded-content" class="hidden-content">
						<h2>Vous avez testé : </h2>
						<span class="rating-num" id="user_total_breweries"></span><br /><b>brasseries</b>
						<div id="testedChart" style="min-width: 150px; height: 250px; max-width: 600px; margin: 0 auto"></div>
						<div id="countriesChart" style="min-width: 150px; height: 250px; max-width: 600px; margin: 0 auto"></div>
						<h2>Votre top 5 : </h2>
						<div id="userTop5" class="list-group"></div>
					</div>
				</div>
			@endif
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
	<h2>Chercher une brasserie</h2>
		<div id="scrollable-dropdown-menu-breweries">
			<input class="typeahead" type="text" id="brewerySearchBox" placeholder="Nom de la brasserie">
		</div>
	<br />
</div>

<div class="panel panel-default">
	<div class="panel-body">
		<h2>{{ count($unknwonBreweries) }} brasseries que vous ne connaissez pas encore (et au hasard) :</b></h2>
		<div style="overflow:auto">
			@foreach($unknwonBreweries as $tmpBrewery)
				<div class="fiche_brasserie">
					<div class="logo_brasserie_fiche">
						<a href="brasserie/ {{ $tmpBrewery->id_brasserie }}">
							<img src="assets/img/brasseries/{{ $tmpBrewery->img }}" alt="Logo" class="img_logo_fiche_brasserie" />
						</a>
					</div>
					<b>{{ link_to("brasserie/$tmpBrewery->id_brasserie", $tmpBrewery->nom_brasserie ) }}</b>
				</div>
			@endforeach
		</div>
		
		
		<h2>{{ count($unknwonBreweriesByGradesFriends) }} brasseries que vous ne connaissez pas encore mais que vos amis ont aimé :</h2>
		<div style="overflow:auto">
			@foreach($unknwonBreweriesByGradesFriends as $tmpBrewery)
				<div class="fiche_brasserie">
					<div class="logo_brasserie_fiche">
						<a href="brasserie/ {{ $tmpBrewery->id_brasserie }}">
							<img src="assets/img/brasseries/{{ $tmpBrewery->img }}" alt="Logo" class="img_logo_fiche_brasserie" />
						</a>
					</div>
					<b>{{ link_to("brasserie/$tmpBrewery->id_brasserie", $tmpBrewery->nom_brasserie ) }}</b>
				</div>
			@endforeach
		</div>
		
		<h2>{{ count($breweriesByGradesFriends) }} brasseries les plus appriéciées de vos amis :</h2>
		<div style="overflow:auto">
			@foreach($breweriesByGradesFriends as $tmpBrewery)
				<div class="fiche_brasserie">
					<div class="logo_brasserie_fiche">
						<a href="brasserie/ {{ $tmpBrewery->id_brasserie }}">
							<img src="assets/img/brasseries/{{ $tmpBrewery->img }}" alt="Logo" class="img_logo_fiche_brasserie" />
						</a>
					</div>
					<b>{{ link_to("brasserie/$tmpBrewery->id_brasserie", $tmpBrewery->nom_brasserie ) }}</b>
				</div>
			@endforeach
		</div>
		
		<h2>{{ count($unknwonBreweriesByGrades) }} brasseries que vous ne connaissez pas les mieux notées par la communauté :</h2>
		<div style="overflow:auto">
			@foreach($unknwonBreweriesByGrades as $tmpBrewery)
				<div class="fiche_brasserie">
					<div class="logo_brasserie_fiche">
						<a href="brasserie/ {{ $tmpBrewery->id_brasserie }}">
							<img src="assets/img/brasseries/{{ $tmpBrewery->img }}" alt="Logo" class="img_logo_fiche_brasserie" />
						</a>
					</div>
					<b>{{ link_to("brasserie/$tmpBrewery->id_brasserie", $tmpBrewery->nom_brasserie ) }}</b>
				</div>
			@endforeach
		</div>
		
		<h2>Les {{ count($breweriesByGrades) }} brasseries les plus appréciées sur ce site web :</h2>
		<div style="overflow:auto">
			@foreach($breweriesByGrades as $tmpBrewery)
				<div class="fiche_brasserie">
					<div class="logo_brasserie_fiche">
						<a href="brasserie/ {{ $tmpBrewery->id_brasserie }}">
							<img src="assets/img/brasseries/{{ $tmpBrewery->img }}" alt="Logo" class="img_logo_fiche_brasserie" />
						</a>
					</div>
					<b>{{ link_to("brasserie/$tmpBrewery->id_brasserie", $tmpBrewery->nom_brasserie ) }}</b>
				</div>
			@endforeach
		</div>
	</div>
</div>
@stop

@section('footer-scripts')
	<script src="assets/js/charts/highcharts.js"></script>
	
	<script>
		$(document).ready(function() {
			var foundBreweries = new Bloodhound({
			  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
			  queryTokenizer: Bloodhound.tokenizers.whitespace,
			  limit: 10,
			  remote: {
					url: 'findBrewery',
					// the json file contains an array of strings, but the Bloodhound
					// suggestion engine expects JavaScript objects so this converts all of
					// those strings
					replace: function(url, query) {
						return url + "/" + query;
					},
					ajax : {
						beforeSend: function(jqXhr, settings){
						   settings.data = $.param({q: $('brewerySearchBox').val()})
						},
						type: "GET"
					}
				}
			});
			 
			// kicks off the loading/processing of `local` and `prefetch`
			foundBreweries.initialize();
			 
			// passing in `null` for the `options` arguments will result in the default
			// options being used
			@include('partials/handlebars-template/found_breweries')
			
			$.getJSON( "breweriesUserStats/{{ Auth::user()->getAuthIdentifier() }}", 
				function(result)
				{
					$('#user_total_breweries').html(result.myTotalBreweries);
					$('#testedChart').highcharts({
						chart: {
							plotBackgroundColor: null,
							plotBorderWidth: 0,
							plotShadow: false
						},
						title: {
							text: '<h2>' + result.myPercent + ' % </h2> <br />des brasseries du site',
							align: 'center',
							verticalAlign: 'middle',
							y: 40
						},
						tooltip: {
							pointFormat: ''
						},
						credits: {
							enabled: false
						},
						plotOptions: {
							pie: {
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									distance: -25,
									style: {
										fontWeight: 'bold',
										color: 'white',
										textShadow: '0px 1px 2px black'
									}
								},
								startAngle: -90,
								endAngle: 90,
								center: ['50%', '75%']
							}
						},
						series: [{
							type: 'pie',
							innerSize: '50%',
							data: [
								{
									name:'Testées',   
									y : result.myTotalBreweries,
									color : "#FF8B5A"
								},
								{
									name:'A tester',   
									y : result.totalBreweries - result.myTotalBreweries,
									color : "#FFB234"
								}
							]
						}],
					});	
					
					var processed_countries = new Array();  
					//Populate countries :
					for (i = 0; i < result.myCountries.length; i++){
                        processed_countries.push([result.myCountries[i].country_name, parseInt(result.myCountries[i].total)]);
                    }
					
					$('#countriesChart').highcharts({
						chart: {
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false
						},
						title: {
							text: 'Pays des brasseries'
						},
						tooltip: {
							pointFormat: '<b>{point.y} brasserie</b> ({point.percentage:.1f}%)'
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: false
								},
								showInLegend: true,
								format: '<b>{point.y} brasserie</b> ({point.percentage:.1f}%)'
							}
						},
						credits: {
							enabled: false
						},
						series: [{
							type: 'pie',
							data: processed_countries
						}]
					});
					
					//Populate top 5 :
					for (i = 0; i < result.bestBreweries.length; i++){
                        $('#userTop5').append('<a href="brasserie/' + result.bestBreweries[i].id_brasserie +'" class="list-group-item">' + result.bestBreweries[i].nom_brasserie + ' (' + result.bestBreweries[i].avg_grade + ')</a>');
                    }
				}
			);
			

			$("#spinner-user-content").css("display", "none");
			$("#user-loaded-content").css("display", "initial");
		});
	</script>
@stop