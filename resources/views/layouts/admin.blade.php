<!DOCTYPE html>
<html lang="@yield('htmlLang', 'en')" class="@yield('htmlClass')" data-ng-app="mimi">

	<head>
		@section('head')
		<meta charset="@yield('charset', 'utf-8')">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>@yield('title')</title>

	    <!-- Fonts -->
	    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">
		
		<link rel="stylesheet" href="{{ URL::asset('css/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ URL::asset('css/bootstrap-theme.min.css') }}">
		<link rel="stylesheet" href="{{ URL::asset('css/style.css') }}">


		<link rel="stylesheet" href="{{ URL::asset('bower_components/fontawesome/css/font-awesome.min.css') }}">
		<link rel="stylesheet" href="{{ URL::asset('bower_components/ng-table/dist/ng-table.min.css') }}">
		<link rel="stylesheet" href="{{ URL::asset('bower_components/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}">

		@yield('css')
		
		<!--[if lt IE 9]>
		<script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		@show
	</head>

	<body class="@yield('bodyClass')">
		<div class="container">
			<div class="row">
				<!-- Header -->
				<div class="col-xs-12">
					<!-- Logo -->
					<div class="layout-logo">
						<h1>MIMI Logo</h1>
					</div>
				</div>
				<!-- Navigation bar -->
				<div class="col-md-2 col-xs-3 layout-nav">
					<div class="row">
						<div class="col-xs-12">
							<p class="layout-nav-welcome">
								Welcome back Administrator!
							</p>
						</div>
						<div class="col-xs-12">
							<p class="layout-nav-topic">
								Admin Panel
							</p>
							<ul class="layout-nav-ul">
								<li class="layout-nav-li">
									<a class="layout-nav-link link-demo" href="{{ url('/admin/user-demo') }}">
										<p class="layout-nav-link-content">USER DEMO</p>
									</a>
								</li>
								<li class="layout-nav-li">
									<a class="layout-nav-link link-beha" href="{{ url('/admin/user-behaviors') }}">
										<p class="layout-nav-link-content">USER BEHA</p>
									</a>
								</li>
								<li class="layout-nav-li">
									<a class="layout-nav-link link-setting" href="{{ url('/admin/setting') }}">
										<p class="layout-nav-link-content">SETTING</p>
									</a>
								</li>
							</ul>
						</div>	
					</div>
				</div>
				<!-- Right section -->
				<div class="col-md-10 col-xs-9 layout-right">
					<!-- Top Content -->
					<div class="row">
						<div class="col-xs-12 layout-topic">
							@yield('topic')
						</div>
					</div>
					<!-- Main Content -->
					<div class="row">
						@yield('content')
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript" src="/bower_components/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="/bower_components/jquery-ui/jquery-ui.min.js"></script>
		<script type="text/javascript" src="/bower_components/angular/angular.min.js"></script>
		<script type="text/javascript" src="/bower_components/angular-resource/angular-resource.js"></script>
		<script type="text/javascript" src="/bower_components/angular-bootstrap/ui-bootstrap.min.js"></script>
		<script type="text/javascript" src="/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
		<script type="text/javascript" src="/bower_components/ng-table/dist/ng-table.min.js"></script>

		<!-- My Angular js File -->
		<script type="text/javascript" src="/app/components/back-end/app.js"></script>
		<script type="text/javascript" src="/app/components/back-end/config.js"></script>

	{{-- 	<script type="text/javascript" src="{{ URL::asset('js/jquery-1.11.3.min.js') }}"></script>
		<script type="text/javascript" src="{{ URL::asset('js/bootstrap.min.js') }}"></script> --}}
		<script type="text/javascript">
			$(document).ready(function() {
				if($(".layout-nav").height() < $(".layout-right").height()){
					$(".layout-nav").height($(".layout-right").height());
				}
			});	
		</script>
		@yield('js')
	</body>
</html>