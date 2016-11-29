@extends('back-end.admin.master')
@section('title')
    MIMI ADMIN - USER BEHAVIORS
@endsection
<link rel="stylesheet" href="/css/style.css">

@section('content')
    <div id="page-wrapper">
        <div class="container-fluid hidden">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">User behaviors</h3>
                </div>
                <div class="col-md-12 view-content">
					<!-- Engaged -->
					<div class="view-block beha-engaged-wrapper">
						<!-- Tittle -->
						<p class="view-tittle">
							* HOW ENGAGED DO USER SEND MESSAGES?
						</p>
						<!-- Engaged content -->
						<div class="beha-engaged-content">
							<table class="beha-table">
								<td class="beha-td">
									<p class="beha-engaged-number">{{ $engaged_send['more_than_10000'] }}</p>
									<p class="beha-engaged-title">>10,000 MES</p>
								</td>
								<td class="beha-td">
									<p class="beha-engaged-number">{{ $engaged_send['between_1000_10000'] }}</p>
									<p class="beha-engaged-title">1000 - 10,000 MES</p>
								</td>
								<td class="beha-td">
									<p class="beha-engaged-number">{{ $engaged_send['between_100_1000'] }}</p>
									<p class="beha-engaged-title">100 - 1000 MES</p>
								</td>
								<td class="beha-td">
									<p class="beha-engaged-number">{{ $engaged_send['less_than_100'] }}</p>
									<p class="beha-engaged-title">0 - 100 MES</p>
								</td>
							</table>
						</div>
					</div>

					<!-- When use -->
					<div class="view-block beha-when-wrapper">
						<!-- Tittle -->
						<p class="view-tittle">
							* WHEN DO THEY USE THE APP?
						</p>
						<!-- When use content -->
						<div class="beha-when-content">
							<table class="beha-table">
								<td class="beha-td">
									<p class="beha-engaged-number">{{ $when_use['in_last_hour'] }}</p>
									<p class="beha-engaged-title">Open in last 1h</p>
								</td>
								<td class="beha-td">
									<p class="beha-engaged-number">{{ $when_use['in_last_day'] }}</p>
									<p class="beha-engaged-title">Open in last day</p>
								</td>
								<td class="beha-td">
									<p class="beha-engaged-number">{{ $when_use['in_last_week'] }}</p>
									<p class="beha-engaged-title">Open in last week</p>
								</td>
								<td class="beha-td">
									<p class="beha-engaged-number">{{ $when_use['in_last_month'] }}</p>
									<p class="beha-engaged-title">Open in last month</p>
								</td>
							</table>
						</div>
					</div>

					<!-- How active -->
					<div class="view-block beha-active-wrapper">
						<!-- Tittle -->
						<p class="view-tittle">
							* HOW ACTIVE THEY ARE?
						</p>
						<!-- How active content -->
						<div class="beha-active-content">
							<table class="beha-table">
								<td class="beha-td">
									<p class="beha-engaged-number">{{ $how_active['hourly'] }}</p>
									<p class="beha-engaged-title">Hourly Active</p>
								</td>
								<td class="beha-td">
									<p class="beha-engaged-number">{{ $how_active['daily'] }}</p>
									<p class="beha-engaged-title">Daily Active</p>
								</td>
								<td class="beha-td">
									<p class="beha-engaged-number">{{ $how_active['weekly'] }}</p>
									<p class="beha-engaged-title">Weekly Active</p>
								</td>
								<td class="beha-td">
									<p class="beha-engaged-number">{{ $how_active['monthly'] }}</p>
									<p class="beha-engaged-title">Monthly Active</p>
								</td>
							</table>
						</div>
					</div>
				</div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
		$(document).ready(function() {
			$('#page-loading').css('display', 'none');
			$('.container-fluid').removeClass('hidden');
		});	
	</script>
@endsection