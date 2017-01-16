$('#scrollable-dropdown-menu-breweries .typeahead').typeahead({highlight: true}, {
	name: 'foundBreweries',
	displayKey: 'name',
	source: foundBreweries.ttAdapter(),
	templates: {
		empty: [
		  '<div class="empty-message">',
		  'unable to find any Best Picture winners that match the current query',
		  '</div>'
		].join('\n'),
		suggestion: Handlebars.compile('<div style="display: block; height: 50px;"><p><a id="typeahead" href="brasserie/{{id_brasserie}}">{{nom_brasserie}}<img src="assets/img/brasseries/{{img}}" align="right" style="max-height : 50px; max-width : 50px;" /></a></p></div>')
	}
});