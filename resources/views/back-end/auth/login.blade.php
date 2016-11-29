@extends('back-end.auth')
@section('title')
    Login
@stop
@section('content')
	<form action="/auth/login" method="POST" class="box login" novalidate>
		{!! csrf_field() !!}
		<fieldset class="boxBody">
		  <label>Tên người dùng</label>
		  <input type="text" name="email" tabindex="1" placeholder="Email" required>
		  <label><a href="#" class="rLink" tabindex="5">Quên mật khẩu?</a>Mật khẩu</label>
		  <input type="password" name="password" tabindex="2" placeholder="Mật khẩu" required>
		</fieldset>
		<footer>
		  	<label><input type="checkbox" name="remember" tabindex="3">Giữ tôi luôn đăng nhập</label>
		  	<input type="submit" class="btnLogin" value="Login" tabindex="4">
		  	<div class="clearfix"></div>
		  	<div style="padding-top:20px;">
		  		@if (count($errors) > 0)
				    <div class="alert alert-danger">
				      	<ul style="list-style: none">
				        	@foreach ($errors->all() as $error)
				           	   	<li>
									{{ $error }}
				           	   	</li>
				          	@endforeach
				      	</ul>
				    </div>
				@endif
		  	</div>
		</footer>
	</form>
	<footer id="main"> <a href="#">Simple Login Form (HTML5/CSS3 Coded) by Tuan Nguyen Thanh</a> </footer>
@endsection

