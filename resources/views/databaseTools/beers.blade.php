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
        <p>La base de données contient {{ $beersCount }} bières, dont {{ $notApprovedBeers }} non approuvées (soit {{ $rateNotApproved }} %).</p>
    </div>

    <!-- Table -->
    <table class="table">
        <thead> <tr> <th>#</th> <th>Nom</th> <th>Brasserie</th> <th>Couleur</th> <th>Fermentation</th> <th>Maltage</th> <th>Type</th> <th>Type 2</th> <th>Ajouté par</th> <th>Le</th> <th>Validée</th> </tr> </thead> 
        <tbody> 
            @foreach($beers as $beer)
                <tr> <th scope=row>{{ $beer->id_biere }}</th> <td><a href="database/editBeer/{{ $beer->id_biere }}">{{ $beer->nom_biere}}</a></td> <td>{{ $beer->brasserie()->nom_brasserie }}</td> <td>{{ $beer->couleur()->nom_couleur }}</td> <td>{{ $beer->fermentation()->nom_fermentation }}</td> <td>{{ $beer->maltage()->nom_maltage }}</td> <td>{{ $beer->typeAmericain()->nom_type }}</td> <td>{{ $beer->typeBelge()->nom_type2 }}</td> <td>{{ $beer->created_by }}</td> <td>{{ $beer->created_at }}</td> <td>@if ($beer->approved == 0) Non @else Oui @endif</td> </tr>
            @endforeach
        </tbody>
    </table>
</div>
@stop

@section('footer-scripts')
<script type="text/javascript">
    $('#tabDatabaseBeers').addClass('active');
</script>
@stop