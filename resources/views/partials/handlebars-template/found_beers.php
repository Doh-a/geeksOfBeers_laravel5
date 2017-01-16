$('#scrollable-dropdown-menu-beers .typeahead').typeahead({highlight: true}, {
	name: 'foundBeers',
	displayKey: 'name',
	source: foundBeers.ttAdapter(),
	templates: {
		empty: [
		  '<div class="empty-message">',
		  'Aucune biere ne correspond à ce nom...',
		  '</div>'
		].join('\n'),
		suggestion: Handlebars.compile('<div style="display: block; height: 50px;"><p><a id="typeahead" href="biere/{{id_biere}}">{{nom_biere}}<img src="assets/img/bieres/{{etiquette}}" align="right" style="max-height : 50px; max-width : 50px;" /></a></p></div>')
	}
});