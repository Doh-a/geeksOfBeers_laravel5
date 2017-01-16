$('#scrollable-dropdown-menu .typeahead').typeahead({highlight: true}, {
	name: 'foundResults',
	displayKey: 'name',
	source: foundResults.ttAdapter(),
	templates: {
		empty: [
		  '<div class="empty-message">',
		  'C\'est dommage mais on ne trouve rien. <br />N\'hésite pas à l\'ajouter à notre liste.',
		  '</div>'
		].join('\n'),
		suggestion: Handlebars.compile('<div style="display: block; height: 50px;"><p><a id="typeahead" href="{{link_directory}}/{{id_item}}">{{name_item}}<img src="{{img_folder}}/{{img}}" align="right" style="max-height : 50px; max-width : 50px;" /></a></p></div>')
	}
});