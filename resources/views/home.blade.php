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
			<div id="spinner-details-right-content" class="spinner">
			  <div class="bounce1"></div>
			  <div class="bounce2"></div>
			  <div class="bounce3"></div>
			</div>
			<div id="details-loaded-content" class="hidden-content">
				<h2>Découvrez des bières :</h2>
				<ul>
					<li id="liBeers"></li>
					<li>Des suggestions personnalisées.</li>
					<li>Des conseils par vos amis.</li>
					<li>Des suggestions géographiques.</li>
					<li>Des catégories pour facilement retrouver vos types de bières favorites.</li>
					<li>Votre wishlist à vous</li>
				</ul>
				<h2>Notez des bières :</h2>
				<ul>
					<li>Et gagnez des badges.</li>
					<li>Retrouver en soirée quelle bière vous avez aimé la dernière fois.</li>
				</ul>
				<h2>Discutez de bière :</h2>
				<ul>
					<li>Donnez vos avis sur les bières et brasseries.</li>
					<li>Discutez en avec d'autres membres.</li>
					<li>Suggérez des bières à des amis.</li>
					<li>Déclarez que vous êtes fan d'une brasserie.</li>
				</ul>
			</div>
		</div>
	</div>
@stop

@section('more-content')

<div class="panel panel-default">
	<div class="panel-body">
		<div id="spinner-flux-right-content" class="spinner">
		  <div class="bounce1"></div>
		  <div class="bounce2"></div>
		  <div class="bounce3"></div>
		</div>
		<div id="flux-loaded-content" class="hidden-content">
			<h2>Dernières activités : </h2>
		</div>
	</div>
</div>
@stop

{{-- Content --}}
@section('content')
<div class="panel panel-default">
	<div id="user-listElems" style="overflow:auto">
		<div id="half-left" style="width : 50%; float : left;" align="center">
			<h1>Découvrez, notez et discutez bière !</h1>
			<div style="margin-left : 30px; float : left;">
				<form action="account/register">
					<input type="submit" class="btn btn-success" value="M'inscrire !" />
				</form>
			</div>
			<div style="margin-right : 30px; float : right;">
				<form action="account/login">
					<input type="submit" class="btn btn-success" value="Me connecter !" />
				</form>
			</div>
		</div>
		<div id="half-right" style="width : 50%; float : right; " align="center">
			<div id="spinner-half-right-content" class="spinner">
			  <div class="bounce1"></div>
			  <div class="bounce2"></div>
			  <div class="bounce3"></div>
			</div>
			<div id="half-right-loaded-content" class="hidden-content"></div>
		</div>
		<br />
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-body">
		<h2>Geeks of beers c'est :</h2>
		<div id="spinner-stats-content" class="spinner">
		  <div class="bounce1"></div>
		  <div class="bounce2"></div>
		  <div class="bounce3"></div>
		</div>
		<div id="stats-loaded-content" class="hidden-content">
			<div id="stats-top-left" style="float : left; width : 50%">
				<div id="graphBeers" style="min-width: 250px; height: 150px; margin: 0 auto"></div>
				<div id="graphBreweries" style="min-width: 250px; height: 150px; margin: 0 auto"></div>
				<div id="countriesChart" style="min-width: 150px; height: 250px; max-width: 600px; margin: 0 auto"></div>
			</div>
			<div id="stats-top-right" style="float : right; width : 50%">
				<div id="gradesColumns" style="min-width: 310px; height: 200px; margin: 0 auto"></div>
				<div id="bestBeer"></div>
				<div id="bestBrewery"></div>
			</div>
		</div>
	</div>
</div>
@stop

@section('footer-scripts')
<script src="assets/js/jquery.timeago.js" type="text/javascript"></script>
<script src="assets/js/charts/highcharts.js"></script>
<script type="text/javascript">

/*
For stackoverflow question: http://stackoverflow.com/questions/17859134/how-do-i-create-rating-histogram-in-jquery-mobile-just-like-rating-bar-in-google#17859134
*/

$(document).ready(function() {
	
	$('#timelinesTab a').click(function (e) {
	  e.preventDefault()
	  $(this).tab('show')
	})
	
	jQuery.timeago.settings.strings = {
	   // environ ~= about, it's optional
	   prefixAgo: "il y a",
	   prefixFromNow: "d'ici",
	   seconds: "moins d'une minute",
	   minute: "environ une minute",
	   minutes: "environ %d minutes",
	   hour: "environ une heure",
	   hours: "environ %d heures",
	   day: "environ un jour",
	   days: "environ %d jours",
	   month: "environ un mois",
	   months: "environ %d mois",
	   year: "un an",
	   years: "%d ans"
	};
	
	$.getJSON("homeBeers", 
		function(data)
		{
			for (i = 0; i < data.length; i++)
			{
				var beerView = '<a href="biere/' + data[i].id_biere + '"><div style="background-image: url(assets/img/bieres/' + data[i].id_biere + '/' + data[i].etiquette + '); background-size : 100% auto;background-position: center; height: 70px; width: 100%; border: 1px solid black; font-size : 1.5em; color : white;" align="center">' + data[i].nom_biere + '<br />' + data[i].avg_grade + '/5 (' + data[i].rate_count + ' votes)</div></a>';
				$('#half-right-loaded-content').append(beerView);
			}
			
			$("#spinner-half-right-content").css("display", "none");
			$("#half-right-loaded-content").css("display", "initial");
		}
	);
	
	$.getJSON("homeStats", 
		function(data)
		{
			// Format the beers per month for the graph :
			var beersPerMonthKeys = [];
			var beersPerMonthValues = [];
			var tmpBeersPerMonthCounter = 0;
			for (var key in data.beersPerMonth) {
				if (data.beersPerMonth.hasOwnProperty(key)) {
						beersPerMonthKeys[tmpBeersPerMonthCounter] = key;
						beersPerMonthValues[tmpBeersPerMonthCounter] = data.beersPerMonth[key];
						tmpBeersPerMonthCounter++;
				}
			}

			$('#graphBeers').highcharts({
				xAxis: {
					categories: beersPerMonthKeys
				},
				yAxis: {
					title: {
						enabled:false
					},
					plotLines: [{
						value: 0,
						width: 1,
						color: '#808080'
					}]
				},
				tooltip: {
					pointFormat: 'Bières sur le site : <b>{point.y}</b>'
				},
				title : {
					text : data.totalBeers + ' bières'
				},
				legend: {
					enabled : false,
				},
				credits: {
					enabled: false
				},
				series: [{
					data: beersPerMonthValues
				}]
			});
			
			// Format the breweries per month for the graph :
			var breweriesPerMonthKeys = [];
			var breweriesPerMonthValues = [];
			var tmpBreweriesPerMonthCounter = 0;
			for (var key in data.breweriesPerMonth) {
				if (data.breweriesPerMonth.hasOwnProperty(key)) {
						breweriesPerMonthKeys[tmpBreweriesPerMonthCounter] = key;
						breweriesPerMonthValues[tmpBreweriesPerMonthCounter] = data.breweriesPerMonth[key];
						tmpBreweriesPerMonthCounter++;
				}
			}
			
			$('#graphBreweries').highcharts({
				xAxis: {
					categories: breweriesPerMonthKeys
				},
				yAxis: {
					title: {
						enabled:false
					},
					plotLines: [{
						value: 0,
						width: 1,
						color: '#808080'
					}]
				},
				title : {
					text : data.totalBreweries + ' brasseries'
				},
				tooltip: {
					pointFormat: 'Brasseries sur le site : <b>{point.y}</b>'
				},
				legend: {
					enabled : false,
				},
				credits: {
					enabled: false
				},
				series: [{
					data: breweriesPerMonthValues
				}]
			});
			
			$('#gradesColumns').highcharts({
				chart: {
					type: 'column'
				},
				title: {
					text: data.totalVotes + ' notes'
				},
				xAxis: {
					categories: [
						'1',
						'2',
						'3',
						'4',
						'5'
					]
				},
				yAxis: {
					min: 0,
					title: {
						enabled : false
					}
				},
				plotOptions: {
					column: {
						pointPadding: 0.02,
						borderWidth: 0
					}
				},
				series: [{
					data: [
							{ y : data.gradesRepartition[1], 
								color : "#FF8B5A" },
							{ y : data.gradesRepartition[2], 
								color : "#FFB234" },
							{ y : data.gradesRepartition[3],
								color : "#FFD834" },
							{ y : data.gradesRepartition[4],
								color : "#ADD633" },
							{ y :data.gradesRepartition[5],
								color : "#9FC05A" }
							]
				}],
				legend: {
					enabled : false,
				},
				credits: {
					enabled: false
				}
			});
			
			$('#countriesChart').highcharts({
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: data.totalCountries + ' pays'
				},
				tooltip: {
					pointFormat: '<b>{point.y}</b>'
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: false
						},
						showInLegend: true
					}
				},
				credits: {
					enabled: false
				},
				legend: {
					enabled : false,
				},
				series: [{
					type: 'pie',
					data: data.countriesList
				}]
			});
			 var bestBeerDiv = 	'<div id="short_biere_view_' + data.bestBeer.id_biere + '">' +
									'<h2>Bière la mieux notée : </h2>' +
									'<a href="biere/' + data.bestBeer.id_biere + '">' +
									'<img src="assets/img/bieres/' +  data.bestBeer.id_biere + '/' + data.bestBeer.etiquette + '" alt="Logo ' + data.bestBeer.nom_biere + '" width="70px" align="left" /></a>' +
									'<b><a href="biere/' + data.bestBeer.id_biere + '">' + data.bestBeer.nom_biere + '</a></b><br />' +
									'<b>Note moyenne : </b>' + data.bestBeer.avg_grade +
									'<br />' +
								'</div><br />';
			$('#bestBeer').html(bestBeerDiv);
			
			var bestBreweryDiv = '<div id="short_brewery_view_' + data.bestBrewery.id_brasserie + '">' +
									'<h2>Brasserie la plus appréciée : </h2>' +
									'<a href="brasserie/' + data.bestBrewery.id_brasserie + '"><img src="assets/img/brasseries/' + data.bestBrewery.img + '" alt="Logo ' + data.bestBrewery.nom_brasserie + '" width="70px" align="left" /></a>' +
									'<b><a href="brasserie/' + data.bestBrewery.id_brasserie + '">' + data.bestBrewery.nom_brasserie + '</a></b><br />' +
									'<b>Nombre de fans : </b>' + data.bestBrewery.fans_count +
									'<br />' +
								'</div>';
			$('#bestBrewery').html(bestBreweryDiv);
	
			$("#spinner-stats-content").css("display", "none");
			$("#stats-loaded-content").css("display", "initial");
			
			// Fill the list of details in the left column: 
			$('#liBeers').html(data.totalBeers + " bières recensées dans " + data.totalBreweries + " brasseries.");
			$("#spinner-details-right-content").css("display", "none");
			$("#details-loaded-content").css("display", "initial");
		}
	);
	
	$.getJSON( "timeline/-1", 
		function(data)
		{
			for (i = 0; i < data.length; i++)
			{
				var newEventDiv = '<li id="event-' + data[i].eventId + '" class="streamItem"><a href="user/' + data[i].userid + '"><img src="' + data[i].img + '" class="img-rounded" style="margin : 5px; float : left;" />';
				newEventDiv += '<b>' + data[i].username + '</b></a> ';
				newEventDiv  += '<abbr class="timeago" id="date_' + data[i].eventDate.date + '" title="'+ data[i].eventDate.date + '">' + data[i].eventDate.date + '</abbr>';
				newEventDiv += '<p>' + data[i].mainMessage + '</p>'
				newEventDiv += '</li>';
				$("#flux-loaded-content").append(newEventDiv);
				
				jQuery("abbr.timeago").timeago();
			}

			$("#spinner-flux-right-content").css("display", "none");
			$("#flux-loaded-content").css("display", "initial");
		}
	);
});
</script>	
@stop