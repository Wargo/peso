<!DOCTYPE html>
<html>

<head>

	<link type="text/css" rel="stylesheet" href="css.css">
	<link type="text/css" rel="stylesheet" href="bootstrap/docs/assets/css/bootstrap.css">
	<link type="text/css" rel="stylesheet" href="bootstrap/docs/assets/css/datepicker.css">
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
	<script src="bootstrap/docs/assets/js/bootstrap-datepicker.js"></script>
	<script src="bootstrap/docs/assets/js/bootstrap-modal.js"></script>

	<title>Peso</title>

	<script>

	<?php
	if (!empty($_POST['m'])) {
		echo '
		$(document).ready(function() {
			change(\'' . $_POST['m'] . '\');
		});
		';
	}
	?>
	
	function change(val) {

		if (val == 'eu') {

			$('#en').hide();
			$('.en').attr('disabled', true);

			$('#eu').show();
			$('.eu').attr('disabled', false);

		} else {

			$('#eu').hide();
			$('.eu').attr('disabled', true);

			$('#en').show();
			$('.en').attr('disabled', false);

		}

	}

	</script>

</head>

<body>

	<form method="POST">
		Modelo: <select name="m" onchange="change(this.value);">
			<option value="eu" <?php echo $_POST['m'] == 'eu' ? 'selected="selected"' : ''; ?>>Europeo</option>
			<option value="en" <?php echo $_POST['m'] == 'en' ? 'selected="selected"' : ''; ?>>Inglés</option>
		</select>
		<br />
		<div id="eu">
			Altura (cm): <input class="eu" name="h" value="<?php echo $_POST['h']; ?>" />
			<br />
			Peso antes del embarazo (kg): <input class="eu" name="wb" value="<?php echo $_POST['wb']; ?>" />
			<br />
			Peso actual (kg): <input class="eu" name="w" value="<?php echo $_POST['w']; ?>" />
			<br />
		</div>
		<div id="en" class="hidden">
			Altura (feet): <input disabled="disabled" class="en" name="h1" value="<?php echo $_POST['h1']; ?>" />
			<br />
			Altura (inches): <input disabled="disabled" class="en" name="h2" value="<?php echo $_POST['h2']; ?>" />
			<br />
			Peso antes del embarazo (lb): <input disabled="disabled" class="en" name="wb" value="<?php echo $_POST['wb']; ?>" />
			<br />
			Peso actual (lb): <input disabled="disabled" class="en" name="w" value="<?php echo $_POST['w']; ?>" />
			<br />
		</div>
		Semana: <input name="week" value="<?php echo $_POST['week']; ?>" />
		<br />
		<input type="submit" value="Dale caña" class="btn btn-primary" />
	</form>

	<?php
	if (!empty($_POST['h1'])) {
		$h1 = $_POST['h1'];
		$h2 = !empty($_POST['h2']) ? $_POST['h2'] : 0;

		$_POST['h'] = $h1 * 12 + $h2;
	}
	if (!empty($_POST['h']) && !empty($_POST['w'])) {

		$weight_before = $_POST['wb'];
		$weight = $_POST['w'];
		$height = $_POST['h'];
		$week = $_POST['week'];
		$method = $_POST['m'];

		if ($method == 'eu') {
			echo 'IMC: ' . $bmi = (($weight_before / $height) / $height) * 10000;
		} else {
			echo 'IMC: ' . $bmi = (($weight_before / $height) / $height) * 703;
		}

		if ($bmi < 18.5) {
			//$aux = 1.09629;
			$min_a = 2.2;
			$min_b = 6.6;
			$max_a = 28;
			$max_b = 40;
		} elseif ($bmi < 24.9) {
			//$aux = 0.94814;
			$min_a = 2.2;
			$min_b = 6.6;
			$max_a = 25;
			$max_b = 35;
		} elseif ($bmi < 29.9) {
			//$aux = 0.6;
			$min_a = 2.2;
			$min_b = 6.6;
			$max_a = 15;
			$max_b = 25;
		} else {
			//$aux = 0.5;
			$min_a = 1.1;
			$min_b = 4.4;
			$max_a = 11;
			$max_b = 20;
		}

		if ($method == 'eu') {
			$min_a *= 0.4536;
			$min_b *= 0.4536;
			$max_a *= 0.4536;
			$max_b *= 0.4536;
		}
		
		$aux_1 = ($min_a + ($min_b - $min_a ) / 2) / 11;
		$aux_2 = (($max_a + ($max_b - $max_a) / 2) - ($min_a + ($min_b - $min_a) / 2)) / 27;

		$aux_min_a = $min_a / 11;
		$aux_min_b = $min_b / 11;

		$aux_max_a = ($max_a - $min_a) / 27;
		$aux_max_b = ($max_b - $min_b) / 27;

		echo '
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
			google.load("visualization", "1", {packages:["corechart"]});
			google.setOnLoadCallback(drawChart);
			function drawChart() {
				var values = [];
				values.push([0, \'Max\', \'Min\', \'Tú\']);';
				$me = 0;
				for ($i = 0; $i <= 40; $i ++) {
					if ($i == $week) {
						$me = $weight - $weight_before;
					} else {
						$me = 0;
					}
					if ($i <= 2) {
						echo 'values.push([' . $i . ', 0, 0, ' . $me . ']);';
					} elseif ($i <= 13) {
						echo 'values.push([' . $i . ', ' . ($i - 2) * $aux_min_b . ', ' . ($i - 2) * $aux_min_a . ', ' . $me . ']);';
					} else {
						echo 'values.push([' . $i . ', ' . ($min_b + ($i - 13) * $aux_max_b) . ', ' . ($min_a + ($i - 13) * $aux_max_a) . ', ' . $me . ']);';
					}
				}
				echo '
				var data = google.visualization.arrayToDataTable(
					values
				);

				var options = {
					title: \'Peso (' . ($method == 'eu' ? 'Kg': 'Lb') . ')\'
				};

				var chart = new google.visualization.LineChart(document.getElementById(\'chart_div_line\'));
				chart.draw(data, options);
			}
		</script>

		<div id="chart_div_line" style="width: 800px; height: 900px;"></div>';

	}
	?>

	</body>

</html>
