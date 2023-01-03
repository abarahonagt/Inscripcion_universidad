<?php

use LDAP\Result;

	session_start();
?>



<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Charver University</title>
	<link rel="icon" href="img/logo_color.jpg" type="image/png">
</head>
<body>
	<?php 
		include "includes/header.php"; 
		include "../conexion.php";

		//datos universidad
		$nombreU = '';
		$razonSocial='';
		$telU='';
		$correoU='';
		$dirU='';

		$query_u = mysqli_query($conection, "SELECT * FROM configuracion");
		$row_u = mysqli_num_rows($query_u);

		if($row_u > 0){
			
			while($arrayInfoU = mysqli_fetch_assoc($query_u)){
				$nombreU = $arrayInfoU['nombre'];
				$razonSocial = $arrayInfoU['razon_social'];
				$telU = $arrayInfoU['telefono'];
				$correoU = $arrayInfoU['correo'];
				$dirU = $arrayInfoU['direccion'];
			}
		}

		$query = mysqli_query($conection, "CALL dataDashboard();");
		$result = mysqli_num_rows($query);

		if($result > 0){
			$data = mysqli_fetch_assoc($query);
			mysqli_close($conection);
		}
		
		//print_r($data);
	?>
	<section id="container">
		<div class="divContainer">
			<div>
				<h1 class="titulo_panel"><i class="fas fa-gear"></i>  Panel de control</h1>
			</div>

			<div class="dashboard">
				<!-- validacion de tipo de rol para mostrar info -->
				<?php if($_SESSION['rol'] == 1){ ?>

				<a href="lista_usuario.php">
					<i class="fas fa-users"></i>
					<p>
						<strong>Usuarios</strong><br>
						<span><?=$data['usuarios']; ?></span>
					</p>
				</a>

				<a href="lista_alumno.php">
					<i class="fas fa-book-open-reader"></i>
					<p>
						<strong>Alumnos</strong><br>
						<span><?=$data['alumnos']; ?></span>
					</p>
				</a>

				<a href="lista_catedratico.php">
					<i class="fas fa-chalkboard-user"></i>
					<p>
						<strong>Catedraticos</strong><br>
						<span><?=$data['catedraticos']; ?></span>
					</p>
				</a>

				<a href="lista_curso.php">
					<i class="fas fa-book"></i>
					<p>
						<strong>Cursos</strong><br>
						<span><?=$data['cursos']; ?></span>
					</p>
				</a>

				<a href="lista_matricula.php">
					<i class="fas fa-school"></i>
					<p>
						<strong>Matriculas activas</strong><br>
						<span><?=$data['matriculas']; ?></span>
					</p>
				</a>
				
				<?php } ?>

			</div>
			
		</div>

		<div class="infoSistema">
			<div>
				<h1 class ="titulo_panel"> Configuracion </h1>
			</div>
			<div class="containerPerfil">
				<div class="infoUsuario">
					<div class="logoUser">
						<?php if($_SESSION['idusuario']== 1){?>
							<img src="img/abner.png">
						<?php }else if($_SESSION['idusuario']== 17){ ?>
							<img src="img/brandon.png">
						<?php }else if($_SESSION['idusuario']== 20){ ?>	
							<img src="img/gilberto.png">
							<?php }else{ ?>
							<img src="img/user.png">
						<?php } ?>
					</div>

					<div class="divInfoUsuario">
							<h4>Informacion Personal</h4>
							<div>
								<label>Nombre: </label> <span><?= $_SESSION['nombre']; ?></span>
							</div>
							<div>
								<label>Apellido: </label> <span><?= $_SESSION['apellido']; ?></span>
							</div>
							<div>
								<label>Correo: </label> <span><?= $_SESSION['correo']; ?></span>
							</div>

							<h4>Datos de Usuario</h4>
							<div>
								<label>Rol: </label> <span><?= $_SESSION['rol_nombre']; ?></span>
							</div>
							<div>
								<label>Usuario: </label> <span><?= $_SESSION['usuario']; ?></span>
							</div>

							<h4>Cambiar Clave</h4>
							<form action="" method="post" name="cambioClave" id="cambioClave">
								<div>
									<input type="password" name="clave" id="clave" placeholder="Clave actual" required>
								</div>
								<div>
									<input class="newPass" type="password" name="nuevaclave" id="nuevaclave" placeholder="Nueva clave" required>
								</div>
								<div>
									<input class="newPass" type="password" name="confirmarclave" id="confirmarclave" placeholder="Confirmar clave" required>
								</div>
								<div class="alertCambioClave" style="display:none;">

								</div>
								<div>
									<button type="submit" class="btn_save btn_cambioClave"><i class="fas fa-key"></i> Cambiar clave</button>
								</div>

							</form>

					</div> 
				</div>

				<?php if($_SESSION['rol'] == 1){ ?>

				<div class="infoUniversidad">
					<div class="logoUniversidad">
						<img src="img/logo_color.jpg">
					</div>
					<h4>Datos de la universidad</h4>

					<form action="" method="post" name="frmUniversidad" id="frmUniversidad">
						<input type="hidden" name="action" value="actualizarUniversidad">

						<div>
							<label>Nombre: </label><input type="text" name="txt_nombre" id="txt_nombre" placeholder="Nombre de la universidad" value="<?=$nombreU; ?>" required>
						</div>
						<div>
							<label>Telefono: </label><input type="text" name="txt_tel" id="txt_tel" placeholder="Numero de telefono" value="<?=$telU; ?>" required>
						</div>
						<div>
							<label>Correo: </label><input type="text" name="txt_correo" id="txt_correo" placeholder="Correo de la universidad" value="<?=$correoU; ?>" required>
						</div>
						<div>
							<label>Direccion: </label><input type="text" name="txt_direccion" id="txt_direccion" placeholder="Direccion" value="<?=$dirU; ?>" required>
						</div>
						<div class="alertUniversidad" style="display:none;"></div>
						<div>
							<button type="submit" class="btn_save btn_guardar"><i class="fas fa-save"></i> Guardar cambios</button>
						</div>
					</form>
				
				</div>

				<?php } ?>

			</div>
		</div>
	</section>

</body>
<?php include "includes/footer.php"; ?>
</html>