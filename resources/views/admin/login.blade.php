<!DOCTYPE html>
<html lang="@yield('htmlLang', 'en')" class="@yield('htmlClass')">

<head>
	@section('head')
	<meta charset="@yield('charset', 'utf-8')">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	@yield('meta')

	<title>@yield('title')</title>
	
	<link rel="stylesheet" href="{{ URL::asset('css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ URL::asset('css/style.css') }}">
	@yield('css')
	
	<!--[if lt IE 9]>
	<script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	@show
</head>

<body class="@yield('bodyClass')">

    <div class="login-wrapper">
        <div class="login-welcome-wrapper">
            <p class="login-welcome">
                Please enter your administrator account!
            </p>
        </div>
    	<div class="login-form-wrapper">
    		<form method="POST" action="/admin/login">
                <div class="lform-input-group">
                    <label for="username">Username</label>
                    <input class="lform-input" id="username" type="username" name="username" value="{{ session('username') ? session('username') : old('username')  }}">
                </div>

                @if ($errors->has('username'))
                    <span class="help-block">
                        <strong>{{ $errors->first('username') }}</strong>
                    </span>
                @endif

                <div class="lform-input-group">
                    <label for="password">Password</label>
                    <input class="lform-input" id="password" type="password" name="password" id="password" value="{{ session('password') ? session('password') : old('password')  }}">
                </div>
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif

                <div  class="lform-input-group lform-button-wrapper">
                    <input class="lform-checkbox" type="checkbox" name="remember" checked> Remember Me
                    <button class="lform-button" type="submit">Log In</button>
                </div>

                @if (session('error_message'))
                    <div class="alert alert-danger">
                        {{ session('error_message') }}
                    </div>
                @endif
            </form>
    	</div>
        <div class="login-lost-password">
            <p class="lost-password-link">
                Lost your password?
            </p>
        </div>
    </div>

<script type="text/javascript" src="{{ URL::asset('js/jquery-1.11.3.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".lost-password-link").click(function(event){
            alert("Please contact Technical Administrator for new Password!");
        });
    });
</script>
</body>
</html>