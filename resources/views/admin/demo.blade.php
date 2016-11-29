
@extends('layouts.admin')

@section('title')
	MIMI ADMIN - USER DEMOGRAPHICS
@endsection

@section('topic')
	<p class="view-topic">USER DEMOGRAPHICS</p>
@endsection

@section('content')
	<div class="col-md-12 view-content">
		<!-- Chart User Register -->
		<div class="view-block demo-regis-wrapper">
			<!-- Tittle -->
			<p class="view-tittle">
				* HOW MANY USER REGISTERED?
			</p>
			<!-- Chart -->
			<div class="demo-chart-wrapper">
				<canvas id="regis-chart" ></canvas>
			</div>
		</div>

		<!-- Chart who are they -->
		<div class="view-block demo-genage-wrapper">
			<!-- Tittle -->
			<p class="view-tittle">
				* WHO ARE THEY?
			</p>
			<div class="row">
				<!-- Gender -->
				<div class="demo-gender-wrapper col-md-5 col-md-offset-1">
					<p class="demo-age-title">
						Gender Distribution
					</p>
					<div class="gender-canvas-wrapper">
						<canvas id="gender-chart" ></canvas>	
					</div>
					<div id="gender-legend" class="chart-legend-gender"></div>
				</div>

				<!-- Age -->
				<div class="demo-age-wrapper col-md-5">
					<p class="demo-age-title">
						Age Distribution
					</p>
					<div class="age-canvas-wrapper">
						<canvas id="age-chart" ></canvas>	
					</div>
					<div id="age-legend" class="chart-legend-age"></div>
				</div>	
			</div>
		</div>
	</div>
@endsection

@section('js')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.6/Chart.bundle.min.js"></script>
<?php
	//get data registration
	$php_regis_labels = array_pluck($users_registered['last_30_days'], 'date');
	$php_regis_data = array_pluck($users_registered['last_30_days'], 'registereds');

	//get data gender
	$php_gender_gender = array_pluck($gender, 'gender');
	$php_gender_data = array_pluck($gender, 'quantity');	

	//get data age
	$php_age_group = array_pluck($age, 'group');
	$php_age_data = array_pluck($age, 'quantity');
?>
<script type="text/javascript">
	//get data for chart: registered - date
	var imp_regis_lables = '{{ implode(',', $php_regis_labels) }}';
	var js_regis_labels = imp_regis_lables.split(",");

	//get data for chart: registered - registereds
	var imp_regis_data = '{{ implode(',', $php_regis_data) }}';
	var js_regis_data = imp_regis_data.split(",");

	//get data for chart: age - group
	var imp_age_labels = '{{ implode(',', $php_age_group) }}';
	var js_age_labels = imp_age_labels.split(",");

	//get data for chart: age - quantity
	var imp_age_data = '{{ implode(',', $php_age_data) }}';
	var js_age_data = imp_age_data.split(",");

	//get data for chart: gender - gender
	var imp_gender_labels = '{{ implode(',', $php_gender_gender) }}';
	var js_gender_labels = imp_gender_labels.split(",");

	//get data for chart: gender - quantity
	var imp_gender_data = '{{ implode(',', $php_gender_data) }}';
	var js_gender_data = imp_gender_data.split(",");

	$(document).ready(function() {
		//add class active to menu demo
		$(".link-demo").addClass("active");

		/*
		 * Registereds chart
		 */
		//declare canvas element
		var ctx_regis = document.getElementById("regis-chart").getContext("2d");
		ctx_regis.canvas.width = $(".demo-chart-wrapper").width();
		ctx_regis.canvas.height = 500;

		//data to draw
		var data_regis = {
		    labels: js_regis_labels,
		    datasets: [
		        {
		            fill: true,
		            lineTension: 0.1,
		            backgroundColor: "rgba(75,192,192,0.4)",
		            borderColor: "rgba(75,192,192,1)",
		            borderCapStyle: 'butt',
		            borderJoinStyle: 'miter',
		            pointBorderColor: "rgba(75,192,192,1)",
		            pointBackgroundColor: "#fff",
		            pointHoverBackgroundColor: "rgba(75,192,192,1)",
		            pointHoverBorderColor: "rgba(220,220,220,1)",
		            data: js_regis_data,
		        }
		    ]
		};

		//use chartjs to draw
		var regis_Chart = new Chart(ctx_regis, {
		    type: 'line',
	        data: data_regis,
	        options: {
	        	legend: { display: false },
				tooltips: { enabled: false },
    			responsive: false,
		    }
		});

		/*
		 * Gender chart
		 */
		//declare canvas element
		var ctx_gender = document.getElementById("gender-chart").getContext("2d");
		ctx_gender.canvas.width = 200;
		ctx_gender.canvas.height =200;

		//set data to draw
		var data_gender = {
		    labels: js_gender_labels,
		    datasets: [
		        {
		            data: js_gender_data,
		            backgroundColor: ["#FF6384", "#36A2EB", "#e1e1e1"],
		            hoverBackgroundColor: ["#FF6384", "#36A2EB", "#e1e1e1"],
	            }]
		};

		//draw chart by chartjs
		var myGenderChart = new Chart(ctx_gender,{
		    type: 'pie',
		    data: data_gender,
		    options: {
	        	legend: { display: false },
    			responsive: false,
		    }
		});
		document.getElementById('gender-legend').innerHTML = myGenderChart.generateLegend();

		/*
		 * Age chart
		 */
		//declare canvas element
		var ctx_age = document.getElementById("age-chart").getContext("2d");
		ctx_age.canvas.width = 200;
		ctx_age.canvas.height =200;

		//set data to draw
		var data_age = {
		    labels: js_age_labels,
		    datasets: [
		        {
		            data: js_age_data,
		            backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#00ffff", "#e1e1e1"],
		            hoverBackgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#00ffff", "#e1e1e1"],
	            }]
		};

		//draw chart by chartjs
		var myAgeChart = new Chart(ctx_age,{
		    type: 'pie',
		    data: data_age,
		    options: {
	        	legend: { display: false },
    			responsive: false,
		    }
		});
		document.getElementById('age-legend').innerHTML = myAgeChart.generateLegend();

		//reset the green line
		$(".layout-nav").height($(".layout-right").height());
	});	
</script>
@endsection