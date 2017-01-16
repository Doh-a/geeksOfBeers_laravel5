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
					<img src="../assets/img/avatars/{{ Auth::user()->getAvatarId() }}_40.jpg" alt="Your avatar" class="img-rounded" /> <b>Vous et cette brasserie</b>
				</div>
				<div id="user-context-content">
					<div id="spinner-user-content" class="spinner">
					  <div class="bounce1"></div>
					  <div class="bounce2"></div>
					  <div class="bounce3"></div>
					</div>
					<div id="user-loaded-content" class="hidden-content">
						<button type="button" id="buttonFan" class="btn btn-default" align="right">Je suis fan !</button>
						
						<h2>Votre commentaire </h2>
			
						<div id="myCommentForm">
							{!! Form::open(array('route' => 'brewery.new_comment', 'method' => 'post', 'id' => 'newCommentForm')) !!}
								<div class="control-group">
									{!! Form::label('brewery_myDescription', 'Description') !!} 
									<div class="controls">
										{!! Form::textarea('brewery_myDescription', null, ['size' => '35x5']) !!}
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
						
						<h2>Quelques stats</h2>
					
						<div id="achievementChart" style="width: 250px; height: 250px; margin: 0 auto"></div>
						
						<br />
						
						<div id="myRatesChart" style="width: 250px; height: 400px; margin: 0 auto"></div>
					</div>
				</div>
			@endif
		</div>
	</div>
@stop

@section('more-content')

<div class="panel panel-default">
	<div class="panel-body">
		<b>Bi&egrave;res brass&eacute;es</b>
		
		@foreach($bieres as $biere)
			{!! $biere !!}
		@endforeach
	</div>
</div>
@stop

{{-- Content --}}
@section('content')
<div class="panel panel-default">
	<div id="brasserie-infosPrincipales">
		<div id="alert_placeholder"></div>
		<h1>{{{ $brasserie->nom_brasserie }}}</h1>
		<div id="image-box">
			{!! Html::image((asset("assets/img/brasseries/$brasserie->img")), 'Logo', array("width" => "200px;")) !!}
			<div id="colorsChart" style="min-width: 150px; height: 250px; max-width: 600px; margin: 0 auto"></div>
		</div>
		<div id="infos-box" class="brasserie-infos-box">
			<div id="infos-brasserie">
				<h2>Informations</h2>
				<b>Pays : </b> {{ $country->country_name }} {!! HTML::image((asset("assets/img/flags/$country->alpha_2.png")), 'Drapeau', array("width" => "16px;")) !!}<br />
				<b>Existe depuis : </b> {{ $brasserie->founded }}<br />
				<b>Bi&egrave;res brass&eacute;es : </b> {{ $beersTotal }}<br />
				<b>Bi&egrave;res encore en vente : </b> {{ $beersProduced }}<br />
				<b>Site web : </b> {!! link_to("$brasserie->website", $brasserie->website, array('target' => '_blank')) !!}<br />
			</div>
			
			<div id="description-brasserie">
				<h2>Description</h2>
				{{{ $brasserie->description_brasserie }}}
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-body">
		<h2>Fans ({{$fansTotal}})</h2>
		@foreach($fans as $tmpFan)
			<a href="../user/{{ $tmpFan->id }}"><img src="../assets/img/avatars/{{ $tmpFan->getAvatarId() }}_40.jpg" alt="Your avatar" class="img-rounded"></a>
		@endforeach
		<br />
		<div id="image-box">
			<div id="rating-box">
				<h2>Evaluation</h2>
				<div class="rate-container">
				  <div class="rate-inner">
					<div class="rating">
					  <span class="rating-num">{{ $breweryAverageRate }}</span>
					  <div class="rating-users">
						<i class="icon-user"></i> {{ $breweryTotalVotes }} total
					  </div>
					</div>
					
					<div class="rate-histo">
					  <div class="five histo-rate">
						<span class="histo-star">
						  <i class="active icon-star"></i> 5           </span>
						<span class="bar-block">
						  <span id="bar-five" class="bar">
							<span>{{ $breweryRates[5] }}</span>&nbsp;
						  </span> 
						</span>
					  </div>
					  
					  <div class="four histo-rate">
						<span class="histo-star">
						  <i class="active icon-star"></i> 4           </span>
						<span class="bar-block">
						  <span id="bar-four" class="bar">
							<span>{{ $breweryRates[4] }}</span>&nbsp;
						  </span> 
						</span>
					  </div> 
					  
					  <div class="three histo-rate">
						<span class="histo-star">
						  <i class="active icon-star"></i> 3           </span>
						<span class="bar-block">
						  <span id="bar-three" class="bar">
							<span>{{ $breweryRates[3] }}</span>&nbsp;
						  </span> 
						</span>
					  </div>
					  
					  <div class="two histo-rate">
						<span class="histo-star">
						  <i class="active icon-star"></i> 2           </span>
						<span class="bar-block">
						  <span id="bar-two" class="bar">
							<span>{{ $breweryRates[2] }}</span>&nbsp;
						  </span> 
						</span>
					  </div>
					  
					  <div class="one histo-rate">
						<span class="histo-star">
						  <i class="active icon-star"></i> 1           </span>
						<span class="bar-block">
						  <span id="bar-one" class="bar">
							<span>{{ $breweryRates[1] }}</span>&nbsp;
						  </span> 
						</span>
					  </div>
					</div>
				  </div>
				</div>
			</div>
		</div>
		<div id="infos-box">
			<div id="coms-box">
				<h2>Commentaires</h2>
				
				<div id="spinner-comments-content" class="spinner">
				  <div class="bounce1"></div>
				  <div class="bounce2"></div>
				  <div class="bounce3"></div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('footer-scripts')

@if (Auth::check())
<script src="../assets/js/charts/highcharts.js"></script>
<script type="text/javascript">

$( document ).ready(function() {
	
	//Display rates
	$(document).ready(function() {
		  $('.bar span').hide();
		  $('#bar-five').animate({
			 width: '{{ $breweryPercents[5] }}%'}, 1000);
		  $('#bar-four').animate({
			 width: '{{ $breweryPercents[4] }}%'}, 1000);
		  $('#bar-three').animate({
			 width: '{{ $breweryPercents[3] }}%'}, 1000);
		  $('#bar-two').animate({
			 width: '{{ $breweryPercents[2] }}%'}, 1000);
		  $('#bar-one').animate({
			 width: '{{ $breweryPercents[1] }}%'}, 1000);
		 
		  setTimeout(function() {
			$('.bar span').fadeIn('slow');
		  }, 1000);
		});

	//Get user datas about the brewery
	$.getJSON( "../brasseriesUser/{{ $brasserie->id_brasserie }}/{{ Auth::user()->getAuthIdentifier() }}", 
		function(data)
		{
			$("#spinner-user-content").css("display", "none");
			$("#user-loaded-content").css("display", "initial");

			if(data.userBrasserieCom != '')
			{
				$("#brewery_myDescription").val(data.userBrasserieCom);
				$("#myCommentForm").css("display", "none");
				$("#myCommentDiv").css("display", "block");
				$("#myCommentDivText").text(data.userBrasserieCom);
			}
			
			if(data.userBrasserieFan == 1)
			{
				$("#buttonFan").removeClass("btn-default");
				$("#buttonFan").addClass("btn-danger");
				$("#buttonFan").text("Je ne suis plus fan.");
			}
			else
			if(data.userBrasserieFan == 0)
			{
				$("#buttonFan").removeClass("btn-default");
				$("#buttonFan").addClass("btn-primary");
				$("#buttonFan").text("Je suis fan !");
			}
			
			$('#achievementChart').highcharts({
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: 0,
					plotShadow: false
				},
				title: {
					text: '<h2>' + Math.round(data.tested * 100 / data.total) + '%</h2> <br />bieres degustees.',
					align: 'center',
					verticalAlign: 'middle',
					y: 80
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
					name: '% de bieres deja degustees de chez cette brasserie.',
					innerSize: '50%',
					data: [
						['Degustees',   data.tested],
						['A tester',       data.total - data.tested],
					]
				}],
			});
			
			$('#myRatesChart').highcharts({
					chart: {
						type: 'bar'
					},
					title: {
						text: 'Vos notes',
						align: 'left'
					},
					xAxis: {
						categories: data.beersName,
						title: {
							text: null
						}
					},
					yAxis: {
						min: 0,
						max : 5,
						title: {
							text: 'Note',
							align: 'high'
						},
						labels: {
							overflow: 'justify'
						}
					},
					plotOptions: {
						bar: {
							dataLabels: {
								enabled: true
							}
						}
					},
					legend: {
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'top',
						x: 10,
						y: -10,
						floating: true,
						borderWidth: 1,
						backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor || '#FFFFFF'),
						shadow: true,
					},
					credits: {
						enabled: false
					},
					series: [{
						name: 'Site',
						data: data.communityRates
					},{
						name: 'Amis',
						
					}, {
						name: 'Vos notes',
						data: data.communityRates
					}]
			});
		});
	
    
	$(function () {
		var chart;
			
			// Build the chart
			$('#colorsChart').highcharts({
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: 'Types de bieres brassees'
				},
				tooltip: {
					pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
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
				series: [{
					type: 'pie',
					name: 'Type de bieres',
					data: [
						@foreach($colorsRate as $tmpColor)
							[' {{ $tmpColor['nom_couleur'] }}', {{ $tmpColor['count'] }} ],
						@endforeach
					]
				}]
			});
		});
		
		//Get the comments
		$.getJSON( "../brasseriesComments/{{ $brasserie->id_brasserie }}/10/0", 
		function(data)
		{
			$("#spinner-comments-content").css("display", "none");
			
			for (index = 0; index < data.length; ++index) {
				$("#coms-box").append(
					"<div class=\"com-box\">" +
						"<div>" +
							"<a href=\"../user/" + data[index].id_user + "\">" +
								"<img src=\"../assets/img/avatars/" + data[index].avatar + "_40.jpg\" class=\"img-rounded\" align=\"left\" />" +
								"<b>" + data[index].username + "</b>" +
							"</a><br />" +
							"<span style=\"font-size : 0.7em;\"><i>Le " + data[index].updated_at + "</i></span>" +
						"</div>" +
						"<div style=\"margin-top:5px;\">" + data[index].commentaire + "</div>" +
						"<div class=\"com-footer\">" +
							"<a href \"#\">Commenter</a>" +
							"<span class=\"pull-right\">" +
								"<i id=\"like"+data[index].commentaire_id +"\" class=\"glyphicon glyphicon-thumbs-up\"></i>" +
								"<div id=\"like"+data[index].commentaire_id +"-bs3\"></div>" +
								"<i id=\"dislike"+data[index].commentaire_id +"\" class=\"glyphicon glyphicon-thumbs-down\"></i>"+
								"<div id=\"dislike"+data[index].commentaire_id +"-bs3\"></div>" +
							"</span>" +
						"</div>" +
					"</div>"
					);
			}
		});
		
		//Plus or minus a comment
		$(document).on("click", 'i.glyphicon-thumbs-up, i.glyphicon-thumbs-down', function(){    
			var $this = $(this),
			c = $this.data('count');    
			if (!c) c = 0;
			c++;
			$this.data('count',c);
			$('#'+this.id+'-bs3').html(c);
		});
		
		//I'm a new fan, or not fan any more :
		$("#buttonFan").click(function() {
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
						
				$.getJSON( "../brasserieFanAction/{{ $brasserie->id_brasserie }}/"+newValue, 
					function(data)
					{
						if(data == 1)
						{
							$("#buttonFan").removeClass("btn-default");
							$("#buttonFan").addClass("btn-danger");
							$("#buttonFan").text("Je ne suis plus fan.");
						}
						else
						if(data == 0)
						{
							$("#buttonFan").removeClass("btn-default");
							$("#buttonFan").addClass("btn-primary");
							$("#buttonFan").text("Je ne suis fan !");
						}
					});
			}
		});
		
		$("#deleteMyComment").click(function() {
			$.getJSON( "../brasserieUserDeleteComment/{{ $brasserie->id_brasserie }}",
				function(data)
				{
					if(data == 1)
					{
						$("#myCommentForm").css("display", "block");
						$("#myCommentDiv").css("display", "none");
					}
				}
			);
			return false;
		});
		
		$("#newCommentForm").submit(function() {
			
			$.post(
				$( this ).prop( 'action' ),
				{
					"_token": $( this ).find( 'input[name=_token]' ).val(),
					"brewery": {{ $brasserie->id_brasserie }},
					"comment": $( '#brewery_myDescription' ).val()
				},
				function( data ) {
					;
				},
				'json'
			);
			
			return false;
		});
	}
)
</script>
@endif

@stop