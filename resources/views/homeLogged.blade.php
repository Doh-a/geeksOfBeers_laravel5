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
			<div id="user-context-title">
				<img src="assets/img/avatars/{{ Auth::user()->getAvatarId() }}_40.jpg" alt="Your avatar" class="img-rounded"> <b>Vous et Geeks of beers</b>
			</div>
			<div id="user-context-content">
				<div id="spinner-user-content" class="spinner">
				  <div class="bounce1"></div>
				  <div class="bounce2"></div>
				  <div class="bounce3"></div>
				</div>
				<div id="user-loaded-content" class="hidden-content">
					<h2>Vos stats bières : </h2>
					<div id="content-stats-beers"></div>
					<div id="ratesRepartitionGraph" style="min-width: 250px; height: 250px; max-width: 600px; margin: 0 auto"></div>
					<h2>Vos stats amis : </h2>
					<div id="content-stats-friends"></div>
					<h2>Vos stats site : </h2>
					<div id="content-stats-site"></div>
				</div>
			</div>
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
	<div id="user-listElems" style="overflow:auto">
		<div id="user-listElemsNavbar">
			<ul class="nav nav-tabs" id="timelinesTab">
				<li>
					<a id="contentFlux" href="#" id="privateTimeline">Moi</a>
				</li>
				<li class="active">
					<a id="contentBieres" href="#" id="timeline">Flux</a>
				</li>
				<li>
					<a id="contentBrasseries" href="#" id="friendsTimeline">Amis</a>
				</li>
				<li>
					<a id="contentAmis" href="#" id="wholeTimeline">Tout</a>
				</li>
				<li>
					<a id="contentPhotos" href="#" id="gradesTimeline">Notes</a>
				</li>
				<li>
					<a id="contentPhotos" href="#" id="commentsTimeline">Discussions</a>
				</li>
			</ul>
		</div>
		<div id="user-listElemsContent">
			<div id="spinner-elems-content" class="spinner">
			  <div class="bounce1"></div>
			  <div class="bounce2"></div>
			  <div class="bounce3"></div>
			</div>
			<div id="mainContent" class="hidden-content">
				<ol id="timeline" class="streamList"></ol>
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
	
	$.getJSON( "timeline/{{ Auth::user()->getAuthIdentifier() }}", 
		function(data)
		{
			for (i = 0; i < data.length; i++)
			{
				var newEventDiv = '<li id="event-' + data[i].eventId + '" class="streamItem"><a href="user/' + data[i].userid + '"><img src="' + data[i].img + '" class="img-rounded" style="margin : 5px; float : left;" />';
				newEventDiv += '<b>' + data[i].username + '</b></a> ';
				newEventDiv  += '<abbr class="timeago" id="date_' + data[i].eventDate.date + '" title="'+ data[i].eventDate.date + '">' + data[i].eventDate.date + '</abbr>';
				newEventDiv += '<p style="float : right; margin-right : 30px;"><a href="#" id="like-' + data[i].eventId + '" class="likebutton">';
				if(data[i].myLike == 1)
					newEventDiv += '<i class="step fi-like size-18" style="color: #B8D30B;"></i> '; 
				else
					newEventDiv += '<i class="step fi-like size-18"></i> ';
				newEventDiv += data[i].likesCount + '</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" id="dislike-' + data[i].eventId + '" class="dislikebutton">';
				if(data[i].myLike == 0)
					newEventDiv += '<i class="step fi-dislike size-18" style="color: #FF0000;"></i> ';
				else
					newEventDiv += '<i class="step fi-dislike size-18"></i> ';
				newEventDiv += data[i].dislikesCount + '</a></p>';
				newEventDiv += '<p>' + data[i].mainMessage + '</p>'
				
				if(data[i].comments != null && data[i].comments.length > 0)
				{
					newEventDiv += '<div id="commentsAboutThisEvent">';
					for (j = 0; j < data[i].comments.length; j++)
					{
						var newCommentDiv = '<div style="margin-left : 50px; border : 1px #DDD solid; padding : 5px;"><img src="assets/img/avatars/' + data[i].comments[j].avatar_id + '_40.jpg" alt="Your avatar" height="20" class="img-rounded">';
						newCommentDiv += '<b>' + data[i].comments[j].username + '</b> ';
						newCommentDiv  += '<abbr class="timeago" title="' + data[i].comments[j].created_at.date + '">' + data[i].comments[j].created_at.date + '</abbr>';
						newCommentDiv += '<p style="margin-left : 25px;">' + data[i].comments[j].comment + '</p></div>';
						newEventDiv += newCommentDiv;
					}
					newEventDiv += '</div>';
				}
				else
					newEventDiv += '<div id="commentsAboutThisEvent"></div>';
				newEventDiv += '<form class="form-horizontal" role="form" action="commentEvent.php">';
				newEventDiv += '<div class="input-group" style="margin-left : 50px">';
				newEventDiv += '<span class="input-group-addon" id="sizing-addon1"><img src="assets/img/avatars/{{ Auth::user()->getAvatarId() }}_40.jpg" alt="Your avatar" height="20" class="img-rounded"></span>';
				newEventDiv += '<input type="text" id="comment-event" class="form-control" placeholder="Commentez" aria-describedby="sizing-addon1">';
				newEventDiv += '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
				newEventDiv += '<span class="input-group-btn"><button class="btn btn-default" id="button-comment-event-' + data[i].eventId + '" type="submit">Envoyez</button></span></form></div>';
				newEventDiv += '</li>';
				$("#timeline").append(newEventDiv);
				
				jQuery("abbr.timeago").timeago();
			}
			
			$("#spinner-elems-content").css("display", "none");
			$("#mainContent").css("display", "initial");
		}
	);
	
	$.getJSON( "userStats/{{ Auth::user()->getAuthIdentifier() }}", 
		function(data)
		{
			$("#content-stats-beers").html('<b>' + data.totalBeersRated + '</b> bières notées' + '<br />Note moyenne : <b>' + data.averageBeerRate + '</b> <br /><b>' + data.totalBreweriesTried + '</b> brasseries testées');
			$("#content-stats-friends").html('Vous suivez <b>' + data.totalFriends + '</b> personnes' + '<br /><b>' + data.totalFollowers + '</b> vous suivent.<br />');
			
			
			var chart;
			var grades = new Array();
			for (var i = 1; i < 6; i++)
			{
				grades[i-1] = [i + ' / 5', data.beersRate[i]];
			}
			
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
					name: 'Repartition des notes',
					data: grades
				}]
			});
		
			$("#spinner-user-content").css("display", "none");
			$("#user-loaded-content").css("display", "initial");
		}
	);
	
	$(document).on('click', ".likebutton", function() 
	{
		var eventId = $(this).parents('li').prop('id').substring(6);
		$.getJSON( "likeEvent/" + eventId + "/1", 
			function(data)
			{
				if(data['userLike'] == 1)
					$('#like-' + eventId).html('<i class="step fi-like size-18" style="color: #B8D30B;"></i> ' + data['1']);
				else
					$('#like-' + eventId).html('<i class="step fi-like size-18"></i> ' + data['1']);
						
				if(data['userLike'] == 0)
					$('#dislike-' + eventId).html('<i class="step fi-dislike size-18" style="color: #FF0000;"></i> ' + data['0']);
				else
					$('#dislike-' + eventId).html('<i class="step fi-dislike size-18"></i> ' + data['0']);
			}
		);
	});

	$(document).on('click', ".dislikebutton", function() 
	{
		var eventId = $(this).parents('li').prop('id').substring(6);
		$.getJSON( "likeEvent/" + eventId + "/0", 
			function(data)
			{
				if(data['userLike'] == 1)
					$('#like-' + eventId).html('<i class="step fi-like size-18" style="color: #B8D30B;"></i> ' + data['1']);
				else
					$('#like-' + eventId).html('<i class="step fi-like size-18"></i> ' + data['1']);
						
				if(data['userLike'] == 0)
					$('#dislike-' + eventId).html('<i class="step fi-dislike size-18" style="color: #FF0000;"></i> ' + data['0']);
				else
					$('#dislike-' + eventId).html('<i class="step fi-dislike size-18"></i> ' + data['0']);
			}
		);
	});
	
	$(document).on('click', ".btn-default", function() 
	{
		var eventId = $(this).prop('id').substring(21);
		var formToSubmit = $(this).parents('form');
		$.post(
			"commentEvent/" + eventId,
			{
				"_token": formToSubmit.find( 'input[name=_token]' ).val(),
				"comment": formToSubmit.find( '#comment-event' ).val()
			},
			function( data ) {
				var divComments = formToSubmit.parents('.streamItem').children('#commentsAboutThisEvent');
				var newCommentDiv = '<div style="margin-left : 50px; border : 1px #DDD solid; padding : 5px;"><img src="assets/img/avatars/{{ Auth::user()->getAvatarId() }}_40.jpg" alt="Your avatar" height="20" class="img-rounded">';
				newCommentDiv += '<b>{{ Auth::user()->username }}</b></a> ';
				newCommentDiv  += '<abbr class="timeago">' + jQuery.timeago(new Date()) + '</abbr>';
				newCommentDiv += '<p style="margin-left : 25px;">' + formToSubmit.find( '#comment-event' ).val() + '</p></div>';
				divComments.append(newCommentDiv);
				
				formToSubmit.find( '#comment-event' ).val("");
				
				jQuery("abbr.timeago").timeago();
			},
			'json'
		);
		
		return false;
	});
});
</script>	
@stop