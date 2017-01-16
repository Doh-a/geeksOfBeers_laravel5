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
	
@stop

@section('more-content')

@stop

{{-- Content --}}
@section('content')

<div class="panel panel-default">
	<div class="panel-body">
		{!! Form::open(array('url' => 'add', 'method' => 'post', 'files' => true)) !!}
		
		<div id="formBiere" style="float:left">
			<div class="control-group">
				{!! Form::label('beer_name', 'Nom de la biere') !!}
				<div class="controls">
					{!! Form::text('beer_name') !!}
				</div>
			</div>
			
			<div class="control-group">
				{!! Form::label('beer_degres', 'Degres') !!} 
				<div class="controls">
					{!! Form::text('beer_degres') !!}
				</div>
			</div>
			
			<div class="control-group">
				{!! Form::label('brasserie', 'Brasserie') !!} 
				<div class="controls">
					{!! Form::select('brasserie', $brasseriesList , Input::old('brasseriesList')) !!}
				</div>
			</div>
			
			<div class="control-group">
				{!! Form::label('couleur', 'Couleur') !!} 
				<div class="controls">
					{!! Form::select('couleur', $couleursList , Input::old('couleursList')) !!}
				</div>
			</div>
			
			<div class="control-group">
				{!! Form::label('fermentation', 'Fermentation') !!} 
				<div class="controls">
					{!! Form::select('fermentation', $fermentationsList , Input::old('fermtationsList')) !!}
				</div>
			</div>
			
			<div class="control-group">
				{!! Form::label('maltage', 'Maltage') !!} 
				<div class="controls">
					{!! Form::select('maltage', $maltagesList , Input::old('maltagesList')) !!}
				</div>
			</div>
			
			<div class="control-group">
				{!! Form::label('typeAmericain', 'Classification (americaine)') !!} 
				<div class="controls">
					{!! Form::select('typeAmericain', $typesAmericainList , Input::old('typesAmericainList')) !!}
				</div>
			</div>
			
			<div class="control-group">
				{!! Form::label('typeBelge', 'Classification (belge)') !!} 
				<div class="controls">
					{!! Form::select('typeBelge', $typesBelgeList , Input::old('typesBelgeList')) !!}
				</div>
			</div>
			
			<div class="control-group">
				{!! Form::label('etiquette', 'Etiquette') !!} 
				<input id="fileupload" type="file" name="files[]" data-url="{!!route('upload')!!}" multiple>
				<div id="etiquetteDiv"></div>
				<input type="hidden" id="etiquetteFile" name="etiquetteFile" value="false" />
				<div id="progress">
					<div class="bar" style="width: 0%; height: 18px; background: green;"></div>
				</div>
				<br />
			</div>
		</div>
		
		<div id="formBrasserie" style="float:right">
			<div class="control-group">
				{!! Form::label('brewerie_name', 'Nom de la brasserie') !!}
				<div class="controls">
					{!! Form::text('brewerie_name') !!}
				</div>
			</div>
			
			<div class="control-group">
				{!! Form::label('country', 'Pays') !!} 
				<div class="controls">
					{!! Form::select('country', $countriesList , Input::old('countriesList')) !!}
				</div>
			</div>
			
			<div class="control-group">
				{!! Form::label('brewerie_description', 'Description') !!} 
				<div class="controls">
					{!! Form::textarea('brewerie_description') !!}
				</div>
			</div>
		</div>
		
		<br clear="both" />
		
		<input type="hidden" id="addBrasserie" name="addBrasserie" value="false" />
		
		<div class="control-group">
			<div class="controls">{!! Form::submit('Ajouter la biere !') !!}</div>
		</div>
		{!! Form::close() !!}
	</div>
</div>

@stop

@section('footer-scripts')

	<script type="text/javascript">
		
		$( "#formBrasserie" ).hide();
	
		$( "#brasserie" ).change(function() {
			if($("#brasserie").val() == -1)
			{
				$( "#formBrasserie" ).show();
				$("#addBrasserie").val("true");
			}
			else
			{
				$( "#formBrasserie" ).hide();
				$("#addBrasserie").val("false");
			}
		});
	</script>

	<script src="../public/assets/js/jqueryFileUpload/js/vendor/jquery.ui.widget.js"></script>
	<script src="../public/assets/js/jqueryFileUpload/js/jquery.iframe-transport.js"></script>
	<script src="../public/assets/js/jqueryFileUpload/js/jquery.fileupload.js"></script>
	<script>
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
					$('#etiquetteFile').val(file.name);
					$('#etiquetteDiv').html('<p><img src="files/thumbnail/' + file.name + '" /></p>');
					$('#fileupload').hide();
					$('#progress').hide();
				});
			}
		});
	});
	</script>

@stop