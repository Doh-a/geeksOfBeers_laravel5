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
					<img src="../assets/img/avatars/{{ Auth::user()->getAvatarId() }}_40.jpg" alt="Your avatar" class="img-rounded"> <b>Vous et cette bi&egrave;re</b>
				</div>
				<div id="user-context-content">
					<div id="spinner-user-content" class="spinner">
					  <div class="bounce1"></div>
					  <div class="bounce2"></div>
					  <div class="bounce3"></div>
					</div>
					<div id="user-loaded-content" class="hidden-content">
						<h2>Votre note : </h2>
						<div class="rating" style="font-size:2em;">
							<span class="rating-num" id="user_rate_num">
							@if($biereUser != null)
								{{ $biereUser->note_biere }}
							@else
								Non not&eacute;e
							@endif
							</span>
							<input class="rating" data-max="5" data-min="1" data-clearable="&nbsp;" data-empty-value="-1" id="user-rating" name="user-rating" type="number" @if($biereUser != null) value="{{ round($biereUser->note_biere) }}" @endif />
						</div>
						<br />
						<h2>Votre commentaire : </h2>
						
						<div id="myCommentForm">
							{!! Form::open(array('route' => 'beers.new_comment', 'method' => 'post', 'id' => 'newCommentForm')) !!}
								<div class="control-group">
									{!! Form::label('beer_myDescription', 'Description') !!} 
									<div class="controls">
										{!! Form::textarea('beer_myDescription', null, ['size' => '35x5']) !!}
									</div>
								</div>
							<div class="control-group">
								<div class="controls">{!! Form::submit('Envoyer mon commentaire !') !!}</div>
							</div>
							{!! Form::close() !!}						
						</div>

						<div id="myCommentDiv" class="com-box">
							<div id="myCommentDivText"></div>
							<br />
							<div class=".com-footer">
								<a href="#" id="deleteMyComment">Supprimer</a>
								<span id="linkToMyDiscussion" class="pull-right">
									<a href="">X r&eacute;ponses</a>
								</span>
							</div>
						</div>
						
						<h2>Vos amis et cette bière : </h2>
						
						<div id="friendsRateChart" style="width: 250px; height: 250px; margin: 0 auto"></div>
						
						<div id="friendsRatesFive" style="background-color : #9FC05A; width : 100% height : 60px;"></div>
						<div id="friendsRatesFour" style="background-color : #ADD633; width : 100% height : 60px;"></div>
						<div id="friendsRatesThree" style="background-color : #FFD834; width : 100% height : 60px;"></div>
						<div id="friendsRatesTwo" style="background-color : #FFB234; width : 100% height : 60px;"></div>
						<div id="friendsRatesOne" style="background-color : #FF8B5A; width : 100% height : 60px;"></div>
					</div>
				</div>
			@endif
		</div>
	</div>
@stop

@section('more-content')

<div class="panel panel-default">
	<div class="panel-body">
		<b>Dans la même brasserie</b>
		
		@foreach($bieresBrewery as $tmpBiere)
			{!! $tmpBiere !!}
		@endforeach
	</div>
</div>
@stop

{{-- Content --}}
@section('content')
<div class="panel panel-default">
	<div class="biere-infosPrincipales">
		<div id="alert_placeholder"></div>
		<h1>{{{ $biere->nom_biere }}}</h1>
		<div id="image-box">
			{!! Html::image((asset("assets/img/bieres/$biere->id_biere/$biere->etiquette")), "Logo $biere->nom_biere", array("width" => "200px;", "align" => "left", "padding" => "5px;")) !!}	
		</div>
		<div id="infos-box">
			<div id="infos-biere">
				<h2>Informations</h2>
				<b>Brass&eacute;e par : </b> {!! link_to("brasserie/$biere->brasserie", $biere->brasserie()->nom_brasserie) !!}<br />
				<b>Couleur : </b>{!! link_to("couleur/", $biere->couleur()->nom_couleur) !!}<br />
				<b>Fermentation : </b> {!! link_to("fermentation/", $biere->fermentation()->nom_fermentation) !!}<br />
				<b>Maltage : </b> {!! link_to("maltage/", $biere->maltage()->nom_maltage) !!}<br />
				<b>Classification (am&eacute;ricaine) : </b>{{ $biere->typeAmericain()->nom_type }}<br />
				<b>Classification (belge) : </b>{{ $biere->typeBelge()->nom_type }}
			</div>
			<div id="beer-description">
				<h2>Description</h2>
				{{{ $biere->description }}}
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-body">
		<div id="image-box">
			<div id="rating-box">
				<h2>Evaluation</h2>
				<div class="rate-container">
				  <div class="rate-inner">
					<div class="rating">
					  <span class="rating-num">{{ $biereAverageRate }}</span>
					  <div class="rating-users">
						<i class="icon-user"></i> {{ $biereTotalVotes }} total
					  </div>
					</div>
					
					<div class="rate-histo">
					  <div class="five histo-rate">
						<span class="histo-star">
						  <i class="active icon-star"></i> 5           </span>
						<span class="bar-block">
						  <span id="bar-five" class="bar">
							<span>{{ $biereRates[5] }}</span>&nbsp;
						  </span> 
						</span>
					  </div>
					  
					  <div class="four histo-rate">
						<span class="histo-star">
						  <i class="active icon-star"></i> 4           </span>
						<span class="bar-block">
						  <span id="bar-four" class="bar">
							<span>{{ $biereRates[4] }}</span>&nbsp;
						  </span> 
						</span>
					  </div> 
					  
					  <div class="three histo-rate">
						<span class="histo-star">
						  <i class="active icon-star"></i> 3           </span>
						<span class="bar-block">
						  <span id="bar-three" class="bar">
							<span>{{ $biereRates[3] }}</span>&nbsp;
						  </span> 
						</span>
					  </div>
					  
					  <div class="two histo-rate">
						<span class="histo-star">
						  <i class="active icon-star"></i> 2           </span>
						<span class="bar-block">
						  <span id="bar-two" class="bar">
							<span>{{ $biereRates[2] }}</span>&nbsp;
						  </span> 
						</span>
					  </div>
					  
					  <div class="one histo-rate">
						<span class="histo-star">
						  <i class="active icon-star"></i> 1           </span>
						<span class="bar-block">
						  <span id="bar-one" class="bar">
							<span>{{ $biereRates[1] }}</span>&nbsp;
						  </span> 
						</span>
					  </div>
					</div>
				  </div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('footer-scripts')
<script src="../assets/js/rating/bootstrap-rating-input.min.js" type="text/javascript"></script>
<script src="../assets/js/charts/highcharts.js"></script>
<script type="text/javascript">

/*
For stackoverflow question: http://stackoverflow.com/questions/17859134/how-do-i-create-rating-histogram-in-jquery-mobile-just-like-rating-bar-in-google#17859134
*/

$(document).ready(function() {
  $('.bar span').hide();
  $('#bar-five').animate({
     width: '{{ $bierePercents[5] }}%'}, 1000);
  $('#bar-four').animate({
     width: '{{ $bierePercents[4] }}%'}, 1000);
  $('#bar-three').animate({
     width: '{{ $bierePercents[3] }}%'}, 1000);
  $('#bar-two').animate({
     width: '{{ $bierePercents[2] }}%'}, 1000);
  $('#bar-one').animate({
     width: '{{ $bierePercents[1] }}%'}, 1000);
 
  setTimeout(function() {
    $('.bar span').fadeIn('slow');
  }, 1000);
  
	@if (Auth::check())
		$( "#user-rating" ).change(function() {
			$.getJSON( "../userBiere/{{ $biere->id_biere }}/{{ Auth::user()->getAuthIdentifier() }}/" + $( '#user-rating' ).val(), 
				function(data)
				{
					if($( '#user-rating' ).val() == -1)
						$("#user_rate_num").html("Non not&eacute;e");
					else
						$("#user_rate_num").text($( '#user-rating' ).val());
				}
			)
		});
		
		$.getJSON( "../userBiereComment/{{ $biere->id_biere }}/{{ Auth::user()->getAuthIdentifier() }}", 
			function(data)
			{
				if(data == 0)
				{
					$("#myCommentForm").css("display", "block");
					$("#myCommentDiv").css("display", "none");
				} else {
					$("#myCommentForm").css("display", "none");
					$("#myCommentDiv").css("display", "block");
					$("#myCommentDivText").text(data.commentaire);
					$("#beer_myDescription").val(data.commentaire);
				}			
			}
		)
		
		$.getJSON( "../userFriendsRates/{{ $biere->id_biere }}/{{ Auth::user()->getAuthIdentifier() }}", 
			function(data)
			{
				$('#friendsRateChart').highcharts({
					chart: {
						plotBackgroundColor: null,
						plotBorderWidth: 0,
						plotShadow: false
					},
					title: {
						text: '<h2>' + data.average + ' / 5</h2> <br />Note moyenne',
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
								enabled: false,
								distance: -50,
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
								name:'Un',   
								y : data.un,
								color : "#FF8B5A"
							},
							{
								name:'Deux',   
								y : data.deux,
								color : "#FFB234"
							},
							{
								name:'Trois',   
								y : data.trois,
								color : "#FFD834"
							},
							{
								name:'Quatre',   
								y : data.quatre,
								color : "#ADD633"
							},
							{
								name:'Cinq',   
								y : data.cinq,
								color : "#9FC05A"
							},
						]
					}],
				});	
				
				for(i = 0; i < data.total; i++)
				{
					if(data[i].note > 0 && data[i].note <= 1)
					{
						$('#friendsRatesOne').append('<div style="float:left"><a href="../user/' + data[i].id_user + '"><img src="../assets/img/avatars/' + data[i].avatar + '_40.jpg" alt="' + data[i].username + ' avatar" class="img-rounded"></a><br />' + data[i].username + '</div>');
						$('#friendsRatesOne').height(60);
					}
					if(data[i].note > 1 && data[i].note <= 2)
					{
						$('#friendsRatesTwo').append('<div style="float:left"><a href="../user/' + data[i].id_user + '"><img src="../assets/img/avatars/' + data[i].avatar + '_40.jpg" alt="' + data[i].username + ' avatar" class="img-rounded"></a><br />' + data[i].username + '</div>');
						$('#friendsRatesTwo').height(60);
					}
					if(data[i].note > 2 && data[i].note <= 3)
					{
						$('#friendsRatesThree').append('<div style="float:left"><a href="../user/' + data[i].id_user + '"><img src="../assets/img/avatars/' + data[i].avatar + '_40.jpg" alt="' + data[i].username + ' avatar" class="img-rounded"></a><br />' + data[i].username + '</div>');
						$('#friendsRatesThree').height(60);
					}
					if(data[i].note > 3 && data[i].note <= 4)
					{
						$('#friendsRatesFour').append('<div style="float:left"><a href="../user/' + data[i].id_user + '"><img src="../assets/img/avatars/' + data[i].avatar + '_40.jpg" alt="' + data[i].username + ' avatar" class="img-rounded"></a><br />' + data[i].username + '</div>');
						$('#friendsRatesFour').height(60);
					}
					if(data[i].note > 4)
					{
						$('#friendsRatesFive').append('<div style="float:left"><a href="../user/' + data[i].id_user + '"><img src="../assets/img/avatars/' + data[i].avatar + '_40.jpg" alt="' + data[i].username + ' avatar" class="img-rounded"></a><br />' + data[i].username + '</div>');
						$('#friendsRatesFive').height(60);
					}
				}
			}
		)
		
		$("#spinner-user-content").css("display", "none");
		$("#user-loaded-content").css("display", "initial");
  
		$("#newCommentForm").submit(function() {
			$.post(
				$( this ).prop( 'action' ),
				{
					"_token": $( this ).find( 'input[name=_token]' ).val(),
					"beer": {{ $biere->id_biere }},
					"comment": $( '#beer_myDescription' ).val()
				},
				function( data ) {
					$("#beer_myDescription").val(data.commentaire);
					$("#myCommentForm").css("display", "none");
					$("#myCommentDiv").css("display", "block");
					$("#myCommentDivText").text(data.commentaire);
				},
				'json'
			);
				
			return false;
		});	
	@endif
});
</script>
@stop