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

    <div class="panel-body">
        <p>La base de données contient {{ $breweriesCount }} brasseries, dont {{ $notApprovedBreweries }} non approuvées (soit {{ $rateNotApproved }} %).</p>
    </div>

    <!-- Table -->
    <table class="table">
        <thead> <tr> <th>#</th> <th>Nom</th> <th>Pays</th> <th>Bières</th> <th>Ajouté par</th> <th>Le</th> <th>Validée</th> </tr> </thead> 
        <tbody> 
            @foreach($breweries as $brewery)
                <tr> <th scope=row>{{ $brewery->id_brasserie }}</th> <td><a href="database/editBrewery/{{ $brewery->id_brasserie }}">{{ $brewery->nom_brasserie}}</a></td> <td>@if ($brewery->country() == "null") null @else {{ $brewery->country()->country_name }}@endif</td> <td>{{ $brewery->getBeersCount() }}</td> <td>{{ $brewery->creator()->username }}</td> <td>{{ $brewery->created_at }}</td> <td>@if ($brewery->approved == 0) Non @else Oui @endif</td> </tr>
            @endforeach
        </tbody>
    </table>
</div>
@stop

@section('footer-scripts')
<script type="text/javascript">
    $('#tabDatabaseBreweries').addClass('active');
</script>
@stop