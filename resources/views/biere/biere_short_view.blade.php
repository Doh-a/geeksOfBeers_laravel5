<div id="short_biere_view_{{ $biere->id_biere }}">
	<a href="../biere/{{{ $biere->id_biere }}}">{!! Html::image((asset("assets/img/bieres/$biere->id_biere/$biere->etiquette")), "Logo $biere->nom_biere", array("width" => "70;", "align" => "left")) !!}</a>
	<b>{!! link_to("biere/$biere->id_biere", $biere->nom_biere) !!}</b>
	<br />
	{!! link_to("couleur/", $biere->couleur()->nom_couleur) !!}, {!! $biere->typeAmericain()->nom_type !!}, 
	@if($biere->typeBelge() != null)
		{{{ $biere->typeBelge()->nom_type2 }}}
	@endif
</div>
