@extends('layouts.databaseTools')

{{-- Web site Title --}}
@section('title')
@parent
:: Database
@stop

{{-- New Laravel 4 Feature in use --}}
@section('styles')
@parent
body {
	background: #f2f2f2;
}

@stop

{{-- Content --}}
@section('content')
<div class="panel panel-default">
        {!! Form::open(array('url' => 'database/editBrewery', 'method' => 'post', 'files' => true)) !!}
		<div id="formBrasserie">
			<div class="control-group">
				{!! Form::label('brewery_name', 'Nom de la brasserie') !!}
				<div class="controls">
					{!! Form::text('brewery_name', $brewery->nom_brasserie ) !!}
				</div>
			</div>
			
			<div class="control-group">
				{!! Form::label('country', 'Pays') !!} 
				<div class="controls">
					{!! Form::select('country', $countriesList , $brewery->country) !!}
				</div>
			</div>

			<div class="control-group">
				{!! Form::label('brewery_founded', 'Année de création') !!} 
				<div class="controls">
					{!! Form::text('brewery_founded', $brewery->founded) !!}
				</div>
			</div>
			
			<div class="control-group">
				{!! Form::label('brewery_description', 'Description') !!} 
				<div class="controls">
					{!! Form::textarea('brewery_description', $brewery->description_brasserie) !!}
				</div>
			</div>

            <div class="control-group">
				{!! Form::label('brewery_logo', 'Logo') !!} 
				<input id="fileupload" type="file" name="files[]" data-url="{!!route('upload')!!}" multiple>
				<div id="logoDiv">
				@if ($brewery->img != '')
					{!! Html::image((asset("assets/img/brasseries/$brewery->img")), 'Logo', array("width" => "200px;")) !!}
				@endif
				</div>
				<input type="hidden" id="logoFile" name="logoFile" value="false" />
				<div id="progress">
					<div class="bar" style="width: 0%; height: 18px; background: green;"></div>
				</div>
				<br />
			</div>

			<div class="control-group">
				{!! Form::label('brewery_location', 'Emplacement') !!} 
				<div class="controls">
					Latitude : {!! Form::text('brewery_lat', $brewery->lat_brewery) !!}
					Longitude : {!! Form::text('brewery_long', $brewery->long_brewery) !!}
				</div>
				<div id="map" style="height: 300px; width: 400px;"></div>
				<br />
			</div>

            <input type="hidden" id="breweryId" name="breweryId" value="{{ $brewery->id_brasserie }}" />

            <div class="control-group">
				{!! Form::label('brewery_approved', 'Validée') !!} 
				<div class="controls">
                    {!! Form::checkbox('brewery_approved', ($brewery->approved) ? true : false) !!}
				</div>
			</div>
		</div>
		
		<br clear="both" />
		
		<div class="control-group">
			<div class="controls">{!! Form::submit('Editer la brasserie !') !!}</div>
		</div>
		{!! Form::close() !!}
</div>
@stop

@section('footer-scripts')
<script src="../public/assets/js/jqueryFileUpload/js/vendor/jquery.ui.widget.js"></script>
	<script src="../public/assets/js/jqueryFileUpload/js/jquery.iframe-transport.js"></script>
	<script src="../public/assets/js/jqueryFileUpload/js/jquery.fileupload.js"></script>

<script type="text/javascript">
    $('#tabDatabaseBreweries').addClass('active');

	$(function () {
		$('#fileupload').fileupload({
			dataType: 'json',
			progressall: function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				console.log(progress);
				$('#progress .bar').css(
					'width',
					progress + '%'
				);
			},
			done: function (e, data) {
				$.each(data.result.files, function (index, file) {
					$('#logoFile').val(file.name);
					$('#logoDiv').html('<p><img src="files/thumbnail/' + file.name + '" /></p>');
					$('#fileupload').hide();
					$('#progress').hide();
				});
			}
		});
	});

@if ($brewery->lat_brewery != '' && $brewery->lat_brewery != '')
      function initMap() {
        var breweryPlace = {lat: {{ $brewery->lat_brewery }}, lng: {{ $brewery->long_brewery }}};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 10,
          center: breweryPlace
        });
        var marker = new google.maps.Marker({
          position: breweryPlace,
          map: map
        });
      }
@endif
    </script>
@if ($brewery->lat_brewery != '' && $brewery->lat_brewery != '')
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxw2izkMb1AgQY_9of8JSsgYTP5T12e6w&callback=initMap">
    </script>
@endif
@stop