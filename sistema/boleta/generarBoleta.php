<?php

session_start();
if (empty($_SESSION['active'])) {
	header('location: ../');
}

include "../../conexion.php";
require_once '../pdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$imagen = "logo_color.jpg";
$imagenB64 = "data:image/jpg;base64," . base64_encode(file_get_contents($imagen));

if (empty($_REQUEST['al']) || empty($_REQUEST['b'])) {
	echo "No es posible generar la boleta.";
} else {
	$carnet = $_REQUEST['al'];
	$noboleta = $_REQUEST['b'];
	$anulada = '';

	$query_config   = mysqli_query($conection, "SELECT * FROM configuracion");
	$result_config  = mysqli_num_rows($query_config);
	if ($result_config > 0) {
		$configuracion = mysqli_fetch_assoc($query_config);
	}


	$query = mysqli_query($conection, "SELECT b.noboleta, DATE_FORMAT(b.fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(b.fecha,'%H:%i:%s') as  hora, b.carnet, b.estado,
												 u.nombre as vendedor,
												 al.carnet, al.nombre, al.apellido, al.telefono,al.direccion
											FROM boleta b
											INNER JOIN usuario u
											ON b.usuario = u.idusuario
											INNER JOIN alumno al
											ON b.carnet = al.carnet
											WHERE b.noboleta = $noboleta AND b.carnet = $carnet  AND b.estado != 0");

	$result = mysqli_num_rows($query);
	if ($result > 0) {

		$boleta = mysqli_fetch_assoc($query);
		$no_boleta = $boleta['noboleta'];

		if ($boleta['estado'] == 0) {

			//$anulada = '<h1 class="label_anulada"> - ANULADA - </h1>';

		}

		$query_cursos = mysqli_query($conection, "SELECT c.codigo_cur, c.descripcion,h.horario,d.colegiatura,(d.colegiatura) as precio_total
														FROM boleta b
														INNER JOIN detalle d
														ON b.noboleta = d.noboleta
														INNER JOIN curso c
														ON d.codigo_cur = c.codigo_cur
														INNER JOIN horario h
														ON d.horario = h.idhorario
														WHERE b.noboleta = $no_boleta ");
		$result_detalle = mysqli_num_rows($query_cursos);

		//preparacion de bufer en memoria, accediendo a la ruta del archivo para escribir la informacion
		ob_start();
		//include(dirname('__FILE__') . '/boleta.php');
		//include 'boleta.php';

		$total 		= 0;
		//print_r($configuracion); 
?>

		<!DOCTYPE html>
		<html lang="es">

		<head>
			<meta charset="UTF-8">
			<title>Boleta de inscripcion</title>
			<!--<link rel="stylesheet" type="text/css" href="style.css">-->

			<style>
				* {
					margin: 0;
					padding: 0;
					box-sizing: border-box;
				}

				p,
				label,
				span,
				table {
					font-family: Arial, Helvetica, sans-serif;
					font-size: 9pt;
				}

				.h2 {
					font-family: Arial, Helvetica, sans-serif;
					font-size: 16pt;
				}

				.h3 {
					font-family: Arial, Helvetica, sans-serif;
					font-size: 12pt;
					display: block;
					background: #0a4661;
					color: #FFF;
					text-align: center;
					padding: 3px;
					margin-bottom: 5px;
				}

				#page_pdf {
					width: 95%;
					margin: 15px auto 10px auto;
				}

				#boleta_head,
				#boleta_alumno,
				#boleta_detalle {
					width: 100%;
					margin-bottom: 10px;
				}

				.logo_boleta {
					width: 25%;
				}

				.info_u {
					width: 50%;
					text-align: center;
				}

				.info_boleta {
					width: 25%;
				}

				.info_alumno {
					width: 100%;
				}

				.datos_alumno {
					width: 100%;
				}

				.datos_alumno tr td {
					width: 50%;
				}

				.datos_alumno {
					padding: 10px 10px 0 10px;
				}

				.datos_alumno label {
					width: 75px;
					display: inline-block;
				}

				.datos_alumno p {
					display: inline-block;
				}

				.textright {
					text-align: right;
				}

				.textleft {
					text-align: left;
				}

				.textcenter {
					text-align: center;
				}

				.round {
					border-radius: 10px;
					border: 1px solid #0a4661;
					overflow: hidden;
					padding-bottom: 15px;
				}

				.round p {
					padding: 0 15px;
				}

				#boleta_detalle {
					border-collapse: collapse;
				}

				#boleta_detalle thead th {
					background: #058167;
					color: #FFF;
					padding: 5px;
				}

				#detalle_cursos tr:nth-child(even) {
					background: #ededed;
				}

				#detalle_totales span {
					font-family: Arial, Helvetica, sans-serif;
					;
				}

				.nota {
					font-size: 8pt;
				}

				.label_gracias {
					font-family: corbel;
					font-weight: bold;
					font-style: italic;
					text-align: center;
					margin-top: 20px;
				}

				.label_anualada {
					font-family: corbel;
					font-size: xx-large;
					font-weight: bold;
					font-style: normal;
					text-align: center;
					margin-top: 20px;
				}

				.anulada {
					position: absolute;
					left: 50%;
					top: 50%;
					transform: translateX(-50%) translateY(-50%);
				}
			</style>

		</head>

		<body>



			<div id="page_pdf">
				<table id="boleta_head">
					<tr>
						<td class="logo_boleta">
							<div>
								<img src="<?php echo $imagenB64; ?>">

							</div>
						</td>
						<td class="info_u">

							<?php
							if ($result_config > 0) {

							?>

								<div>
									<span class="h2"><?php echo strtoupper($configuracion['nombre']); ?></span>
									<p><?php echo $configuracion['razon_social']; ?></p>
									<p><?php echo $configuracion['direccion']; ?></p>
									<p>Teléfono: <?php echo $configuracion['telefono']; ?></p>
									<p>Correo: <?php echo $configuracion['correo']; ?></p>
								</div>

							<?php
							}
							?>

						</td>
						<td class="info_boleta">
							<div class="round">
								<span class="h3">Boleta</span>
								<p>No. Boleta: <strong><?php echo $boleta['noboleta']; ?></strong></p>
								<p>Fecha: <?php echo $boleta['fecha']; ?></p>
								<p>Hora: <?php echo $boleta['hora']; ?></p>
								<p>Atendio: <?php echo $boleta['vendedor']; ?></p>
							</div>
						</td>
					</tr>
				</table>
				<table id="boleta_alumno">
					<tr>
						<td class="info_alumno">
							<div class="round">
								<span class="h3">Alumno</span>
								<table class="datos_alumno">
									<tr>
										<td><label>Carnet:</label>
											<p><?php echo $boleta['carnet']; ?></p>
										</td>
										<td><label>Teléfono:</label>
											<p><?php echo $boleta['telefono']; ?></p>
										</td>
									</tr>
									<tr>
										<td><label>Nombre:</label>
											<p><?php echo $boleta['nombre']; ?></p>
											<p> <?php echo $boleta['apellido']; ?></p>
										</td>
										<td><label>Dirección:</label>
											<p><?php echo $boleta['direccion']; ?></p>
										</td>
									</tr>
								</table>
							</div>
						</td>

					</tr>
				</table>

				<table id="boleta_detalle">
					<thead>
						<tr>
							<th width="50px">Codigo</th>
							<th class="textleft" width="100px">Descripción</th>
							<th width="75px">Horario</th>
							<th class="textcenter" width="75px"> Colegiatura</th>
						</tr>
					</thead>
					<tbody id="detalle_cursos">

						<?php

						if ($result_detalle > 0) {

							while ($row = mysqli_fetch_assoc($query_cursos)) {
						?>

								<tr>
									<td class="textcenter"><?php echo $row['codigo_cur']; ?></td>
									<td class="textleft"><?php echo $row['descripcion']; ?></td>
									<td class="textcenter"><?php echo $row['horario']; ?></td>
									<td class="textcenter"><?php echo $row['colegiatura']; ?></td>
								</tr>
						<?php
								$precio_total = $row['colegiatura'];
								$total 		= round($total + $precio_total, 2);
							}
						}


						?>
					</tbody>
					<tfoot id="detalle_totales">

						<tr>
							<td colspan="3" class="textright"><span>TOTAL Q.</span></td>
							<td class="textcenter"><span><?php echo $total; ?></span></td>
						</tr>
					</tfoot>
				</table>
				<div>
					<p class="nota" aling="center">NOTA: Si usted tiene preguntas sobre esta boleta, <br>pongase en contacto con nombre, teléfono y correo</p>

					<p><? echo $anulada; ?></p>

					<h4 class="label_gracias">¡Conocereis el trabajo y sereis millonarios!</h4>
				</div>

			</div>

		</body>

		</html>
<?php
		$options = new Options();
		$options->set('isRemoteEnable', TRUE);

		// instantiate and use the dompdf class
		$dompdf = new Dompdf($options);

		$html = ob_get_clean();

		$dompdf->loadHtml($html);
		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('letter', 'portrait');

		// Render the HTML as PDF
		//ob_get_clean();
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream('Boleta_' . $noboleta . '.pdf', array('Attachment' => 0));
		exit;
	}
}
