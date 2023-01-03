<?php


      if(empty($_SESSION['active'])){
          //cuando el inicio de sesion es valido lo redirige a esta pagina
          header('location: ../');
      }

?>

<header>
		<div class="header">
		
			<img class="logo" src ="img/logo_gris75.png" alt="logo">
			<h1>Universidad de Charver</h1>
			<div class="optionsBar">
				<p>Guatemala, <?php echo fechaC(); ?> </p>
				<span>|</span>
				<span class="user"><?php echo $_SESSION['usuario'];?> <span></span> <?php echo $_SESSION['nombre']; ?></span>
				<span><?php echo $_SESSION['apellido']; ?></span>
				<span><?php echo $_SESSION['rol']; ?></span>
				<img class="photouser" src="img/user.png" alt="Usuario">
				<a href="salir.php"><img class="close" src="img/salir.png" alt="Salir del sistema" title="Salir"></a>
			</div>
		</div>
        <?php include "nav.php"; ?>
	</header>
	<div class="modal">
		<div class="body">
		<form action="" method="post" name="form_anular" id="form_anular" onsubmit = "event.preventDefault(); anular_boleta();">
        <h1><i class="fas fa-book" style="font-size: 45pt;" align="center"></i> <br><br>Anular Matricula </h1>
        <p>¿Esta seguro de anular el siguiente registro?</p>'+

        <p><strong>No. '+info.noboleta+'</strong></p>'+
        <p><strong>Colegiatura: Q. '+info.total+'</strong></p>'+
        <p><strong>Fecha: '+info.fecha+'</strong></p>'+
        <input type="hidden" name="action" value="anularBoleta">
        <input type="hidden" id="no_boleta" value="'+info.noboleta+'" required>

		<div class="alert alert_matricula"></div>'+
        <a href="#" class="btn_cancel" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar </a>
        <button type="submit" class="btn_ok"><i class="fas fa-trash-alt"></i> Anular </button>
        </form>
		</div>
	</div>