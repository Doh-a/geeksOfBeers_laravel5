@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
:: User
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
			@if (Auth::check() && Auth::user()->getAuthIdentifier() != $user->getAuthIdentifier())
				<div id="user-context-title">
					<img src="../assets/img/avatars/{{ Auth::user()->getAvatarId() }}_40.jpg" alt="Your avatar" class="img-rounded" /> <b>Vous et {{ $user->username }}</b>
				</div>
				<div id="user-context-content">
					<br />
					<button type="button" id="buttonFriend" class="btn btn-default" align="right">M'abonner</button>
				</div>
			@else
				@if (Auth::check())
					<p>Hey, that's you !</p>
				@endif
			@endif
		</div>
	</div>
@stop

@section('more-content')

<div class="panel panel-default">
	<div class="panel-body">
		<b>Plus de stats</b>
		<br />
		<h2>Top 5 brasseries</h2>
		
		<ol>
			@foreach($breweriesTested as $brewery)
				<li><a href="../brasserie/{{ $brewery->id_brasserie }}">{{ $brewery->nom_brasserie }} ({{ $brewery->averageRate }})</a></li>
			@endforeach
		</ol>
		
		<h2>Type de bi&egrave;re</h2>
		
		<ul>
			@if ($bestColor != null)
				<li><a href="../couleur/{{ $bestColor->couleur }}">{{ $bestColor->nom_couleur }}</a></li>
			@endif
			@if ($bestFermentation != null)
				<li><a href="../couleur/{{ $bestFermentation->fermentation }}">{{ $bestFermentation->nom_fermentation }}</a></li>
			@endif
			@if ($bestMalting != null)
				<li><a href="../couleur/{{ $bestMalting->maltage }}">{{ $bestMalting->nom_maltage }}</a></li>
			@endif
			@if ($bestType != null)
				<li><a href="../couleur/{{ $bestType->type }}">{{ $bestType->nom_type }}</a></li>
			@endif
			@if ($bestType2 != null)
				<li><a href="../couleur/{{ $bestType2->type2 }}">{{ $bestType2->nom_type2 }}</a></li>
			@endif
		</ul>

	</div>
</div>
@stop

{{-- Content --}}
@section('content')
<div class="panel panel-default">
	<div id="user-infosPrincipales">
		<div id="alert_placeholder"></div>
		<h1>{{ $user->username }}</h1>
		<div id="mainUserDescription">
			<div id="image-box">
				<img src="../assets/img/avatars/{{ $user->getAvatarId() }}_150.jpg" alt="Your avatar" class="img-rounded" style="margin-left : 10px" />
			</div>
			<div id="infos-box" class="user-infos-box">
				<div id="infos-user">
					<h2>Stats</h2>
					<b>Inscrit le : </b> {{ date("d M Y",strtotime($user->created_at)) }}<br />
					<b>Bi&egrave;res not&eacute;es : </b> {{ $totalBeersRates }}<br />
					<b>Brasseries connues : </b> {{ $totalBreweriesRates }}<br />
					
					<h2>Description</h2>
					<p>{{$user->description }}</p>
				</div>
			</div>
		</div>
		
		<div id="userGraphs">
			<div id="image-box">
				<div id="ratesRepartitionGraph" style="min-width: 150px; height: 250px; max-width: 600px; margin: 0 auto"></div>
			</div>
			<div id="infos-box" class="user-infos-box">
				<div id="ratesPerMonthGraph" style="height: 250px; margin: 0 auto"></div>
			</div>
		</div>
	</div>
</div>

	
<div class="panel panel-default">
	<div id="user-listElems">
		<div id="user-listElemsNavbar">
			<ul class="nav nav-tabs">
				<li>
					<a id="contentFlux" href="#">Flux</a>
				</li>
				<li class="active">
					<a id="contentBieres" href="#">Bi&egrave;res</a>
				</li>
				<li>
					<a id="contentBrasseries" href="#">Brasseries</a>
				</li>
				<li>
					<a id="contentAmis" href="#">Amis</a>
				</li>
				<li>
					<a id="contentPhotos" href="#">Photos</a>
				</li>
			</ul>
		</div>
		<div id="user-listElemsContent">
			<div id="spinner-elems-content" class="spinner">
			  <div class="bounce1"></div>
			  <div class="bounce2"></div>
			  <div class="bounce3"></div>
			</div>
		</div>
	</div>
</div>

@stop

@section('footer-scripts')
<script src="../assets/js/charts/highcharts.js"></script>
<script type="text/javascript">

$( document ).ready(function() {

	$(function () {
		@if (Auth::check())
			if({{ $userFriend }} == 1)
				{
					$("#buttonFriend").removeClass("btn-default");
					$("#buttonFriend").addClass("btn-danger");
					$("#buttonFriend").text("Me desabonner.");
				}
				else
				if({{ $userFriend }} == 0)
				{
					$("#buttonFriend").removeClass("btn-default");
					$("#buttonFriend").addClass("btn-primary");
					$("#buttonFriend").text("M'abonner");
				}
		@endif
		
		var chart;
			
		// Build the chart
		$('#ratesRepartitionGraph').highcharts({
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false
			},
			title: {
				text: 'Repartition des notes'
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					cursor: 'pointer',
					dataLabels: {
						enabled: false
					},
				}
			},
			credits: {
				enabled: false
			},
			series: [{
				type: 'pie',
				name: 'Type de bieres',
				data: [
					@for ($i = 1; $i < 6; $i++)
						['{{ $i }} / 5', {{ $rates[$i] }} ],
					@endfor
				]
			}]
		});
	});
		
	$('#ratesPerMonthGraph').highcharts({
		title: {
			text: 'Notes par mois',
			x: -20 //center
		},
		xAxis: {
			categories: [
				@foreach($ratingPerMonth as $tmpRating)
					'{{ $tmpRating->month }}/{{ $tmpRating->year }}',
				@endforeach
			]
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
			valueSuffix: ' notes'
		},
		legend: {
			enabled : false
		},
		credits: {
			enabled: false
		},
		series: [{
			data: [
				@foreach($ratingPerMonth as $tmpRating)
					{{ $tmpRating->total }},
				@endforeach
			]
		}]
	});
	
	//I'm a new fan, or not fan any more :
	$("#buttonFriend").click(function() {
		if($(this).hasClass("btn-default"))
			;
		else
		{
			var newValue = -1;
			if($(this).hasClass("btn-danger"))
				newValue = 0;
			else
				if($(this).hasClass("btn-primary"))
					newValue = 1;
					
			$.getJSON( "../userFriendAction/{{ $user->getAuthIdentifier() }}"+"/"+newValue, 
				function(data)
				{
					if(data == 1)
					{
						$("#buttonFriend").removeClass("btn-default");
						$("#buttonFriend").addClass("btn-danger");
						$("#buttonFriend").text("Me desabonner");
					}
					else
					if(data == 0)
					{
						$("#buttonFriend").removeClass("btn-default");
						$("#buttonFriend").addClass("btn-primary");
						$("#buttonFriend").text("M'abonner");
					}
				});
		}
	});
	
	//Get user datas about the brewery
	$.getJSON( "../userBiere/{{ $user->getAuthIdentifier() }}", 
	function(data)
	{
		$("#spinner-elems-content").css("display", "none");
	
		for(var i = 0; i < data.length; i++)
		{
			$("#user-listElemsContent").append('<div style="float:left; align : center; margin : 10px; width : 80px; height : 120px;"><a href="../biere/'+data[i].id_biere+'"><img src="../assets/img/bieres/'+data[i].id_biere+'/'+data[i].etiquette+'" style="max-width : 80px; max-height :80px;" /><br />' + data[i].nom_biere +"</div>");
		}
		
		$("#user-listElemsContent").css("height", 30 + Math.round(i/5) *120);
	});

})
</script>
@stop