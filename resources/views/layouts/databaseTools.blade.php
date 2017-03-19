<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Basic Page Needs
		================================================== -->
		<meta charset="utf-8" />
		<title>
			@section('title')
				Geeks of beers
			@show
		</title>
		<meta name="keywords" content="your, awesome, keywords, here" />
		<meta name="author" content="Jon Doe" />
		<meta name="description" content="Lorem ipsum dolor sit amet, nihil fabulas et sea, nam posse menandri scripserit no, mei." />

		<!-- Mobile Specific Metas
		================================================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- CSS
		================================================== -->
		<link href="{{{ asset('assets/css/bootstrap.css') }}}" rel="stylesheet">
		
		<link href="{{{ asset('assets/ico/foundation-icons/foundation-icons.css') }}}" rel="stylesheet">

		<link href="{{{ asset('assets/css/geekOfBeers.css') }}}" rel="stylesheet">


		<link href="{{{ asset('assets/css/bootstrap-responsive.css') }}}" rel="stylesheet">

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<!-- Favicons
		================================================== -->
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{{ asset('assets/ico/apple-touch-icon-144-precomposed.png') }}}">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{{ asset('assets/ico/apple-touch-icon-114-precomposed.png') }}}">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{{ asset('assets/ico/apple-touch-icon-72-precomposed.png') }}}">
		<link rel="apple-touch-icon-precomposed" href="{{{ asset('assets/ico/apple-touch-icon-57-precomposed.png') }}}">
		<link rel="shortcut icon" href="{{{ asset('assets/ico/favicon.png') }}}">
	</head>

	<body>
		<!-- Navbar -->
		<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="navbar-inner">
				<div class="container">
					<div class="navbar-header">
					  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					  </button>
					</div>

					<div class="navbar-collapse collapse">
						<ul class="nav navbar-nav pull-left">
							<li {{{ (Request::is('/') ? 'class="active"' : '') }}}><a href="{{{ URL::to('') }}}">Geeks of beers</a></li>
						</ul>
						
						<ul class="nav navbar-nav">
							<li {{{ (Request::is('/') ? 'class="active"' : '') }}}><a href="{{{ URL::to('biere') }}}">Bi&egrave;res</a></li>
							<li {{{ (Request::is('/') ? 'class="active"' : '') }}}><a href="{{{ URL::to('brasseries') }}}">Brasseries</a></li>
							<li {{{ (Request::is('/') ? 'class="active"' : '') }}}><a href="{{{ URL::to('add') }}}">Ajouter</a></li>
							@if (Auth::check())
							<li {{{ (Request::is('/') ? 'class="active"' : '') }}}><a href="{{{ URL::to('') }}}">Suggestions</a></li>
							<li {{{ (Request::is('/') ? 'class="active"' : '') }}}><a href="{{{ URL::to('') }}}">Amis</a></li>
							@endif
						</ul>

						<ul class="nav navbar-nav pull-right">
							@if (Auth::check())
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ Auth::user()->fullname() }} <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li {{{ (Request::is('account') ? 'class="active"' : '') }}}><a href="{{{ URL::to('account') }}}">Account</a></li>

									@if (Auth::user()->userRole()->role_id > 1)
										<li><a href="{{{ URL::to('database') }}}">Database</a></li>

										@if (Auth::user()->userRole()->role_id > 3)
											<li><a href="{{{ URL::to('administration') }}}">Administration</a></li>
										@endif
									@endif

									<li><a href="{{{ URL::to('account/logout') }}}">Logout</a></li>
								</ul>
							</li>
							@else
							<li {{{ (Request::is('account/login') ? 'class="active"' : '') }}}><a href="{{{ URL::to('account/login') }}}">Login</a></li>
							<li {{{ (Request::is('account/register') ? 'class="active"' : '') }}}><a href="{{{ URL::to('account/register') }}}">Register</a></li>
							@endif
							
							<div class="input-group" id="adv-search">
								<div id="scrollable-dropdown-menu">
									<input type="text" class="typeahead" placeholder="Bières, brasseries..." id="mainSearchBox" style="border-top-left-radius: 4px; border-bottom-left-radius: 4px; border-bottom-right-radius: 0px; border-top-right-radius: 0px; width: 250px; margin: 0px; height:34px;" />
								</div>
								<div class="input-group-btn">
									<div class="btn-group" role="group">
										<div class="dropdown dropdown-lg">
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></button>
											<div class="dropdown-menu dropdown-menu-right" role="menu">
												<form class="form-horizontal" role="form">
												  <div class="form-group">
													<label for="filter">Filter by</label>
													<select class="form-control">
														<option value="0" selected>All Snippets</option>
														<option value="1">Featured</option>
														<option value="2">Most popular</option>
														<option value="3">Top rated</option>
														<option value="4">Most commented</option>
													</select>
												  </div>
												  <div class="form-group">
													<label for="contain">Brasserie</label>
													<input class="form-control" type="text" />
												  </div>
												  <div class="form-group">
													<label for="contain">Ta recherche</label>
													<input class="form-control" type="text" />
												  </div>
												  <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
												</form>
											</div>
										</div>
										<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
									</div>
								</div>
							</div>
						</ul>
					</div>
					<!-- ./ nav-collapse -->
				</div>
			</div>
		</div>
		<!-- ./ navbar -->

		<!-- Container -->
		<div class="container">
			<div id="content">
                <ul class="nav nav-tabs">
					<li role="presentation" id="tabDatabaseInfo"><a href="#">Infos</a></li>
                    <li role="presentation" id="tabDatabaseBeers"><a href="database/beers">Bières</a></li>
                    <li role="presentation" id="tabDatabaseBreweries"><a href="database/breweries">Brasseries</a></li>
                </ul>
				@yield('content')
			</div>
			<!-- ./ content -->
		</div>
		<!-- ./ container -->

		<!-- Javascripts
		================================================== -->
		<script src="{{{ asset('assets/js/jquery.v1.8.3.min.js') }}}"></script>
		<script src="{{{ asset('assets/js/bootstrap/bootstrap.min.js') }}}"></script>
		<script src="{{{ asset('assets/js/typeahead.js/typeahead.bundle.min.js') }}}"></script>
		<script src="{{{ asset('assets/js/handlebars-v2.0.0.js') }}}"></script>
		
		@yield('footer-scripts')
		
		<script>
			$(document).ready(function() {
				var relativeUrl = "{{{ URL::to('') }}}/";
				var foundResults = new Bloodhound({
				  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
				  queryTokenizer: Bloodhound.tokenizers.whitespace,
				  limit: 10,
				  remote: {
						url: '{{{ asset('searchQuery') }}}',
						// the json file contains an array of strings, but the Bloodhound
						// suggestion engine expects JavaScript objects so this converts all of
						// those strings
						replace: function(url, query) {
							return url + "/" + query;
						},
						ajax : {
							beforeSend: function(jqXhr, settings){
							   settings.data = { searchQuery: $('input.typeahead.tt-input').val(), rootUrl: relativeUrl}
							},
							type: "GET",
						}
					}
				});
				 
				// kicks off the loading/processing of `local` and `prefetch`
				foundResults.initialize();
				 
				// passing in `null` for the `options` arguments will result in the default
				// options being used
				@include('partials/handlebars-template/found_results')
			});
		
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-58650788-1', 'auto');
		  ga('send', 'pageview');
		</script>
	</body>
</html>
