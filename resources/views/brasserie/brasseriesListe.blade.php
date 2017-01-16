@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
@parent
:: Account Login
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
	<h1>Brasseries list&eacute;es sur GeekOfBeers :</h1>
</div>
<div class="container">
	 @foreach($brasseries as $brasserie)
        <p><a href="brasserie/{{ $brasserie->id_brasserie }}">{{ $brasserie->nom_brasserie }}</a></p>
    @endforeach
</div>

<?php echo $brasseries->links(); ?>
@stop