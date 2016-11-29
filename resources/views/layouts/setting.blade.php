@extends('layouts.admin')

@section('title')
	MIMI ADMIN - SETTING
@endsection

@section('topic')
	<div class="col-md-12 view-topic">SETTING</div>
@endsection

@section('content')
	<div class="col-md-12 view-content">
		<!-- Report -->
		<div class="view-block seeting-report-wrapper">
			<!-- Tittle -->
			<p class="view-tittle">
				* EXPORT REPORT
			</p>
			<!-- Report -->
			<div class="seeting-report-content">
				<div class="row">
					@yield('content')
				</div>
			</div>
		</div>
	</div>
@endsection

@section('js')
<script type="text/javascript">
	$(document).ready(function() {
		$(".link-setting").addClass("active");
	});	
</script>
@endsection