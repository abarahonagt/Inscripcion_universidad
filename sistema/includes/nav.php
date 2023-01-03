<nav>
			<ul>
				<li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
				<!--solo si el rol es igual a 1 se mostrara la opcion USUARIOS en navegacion -->
				<?php
					if($_SESSION['rol'] == 1){
				?>		
				<li class="principal">
					<a href="#"><i class="fas fa-users"></i> Usuarios</a>
					<ul>
						<li><a href="registro_usuario.php"><i class="fas fa-plus"></i> Nuevo Usuario</a></li>
						<li><a href="lista_usuario.php"><i class="fas fa-list"></i> Lista de Usuarios</a></li>
					</ul>
				</li>
				<?php } ?>
				
				<?php
				if($_SESSION['rol']==1 or $_SESSION['rol']==3){
				?>			
				<li class="principal">
					<a href="#"><i class="fas fa-book-open-reader"></i> Estudiantes</a>
					<ul>
					<?php
					if($_SESSION['rol'] == 1){
					?>
						<li><a href="registro_alumno.php"><i class="fas fa-plus"></i> Nuevo Estudiante</a></li>
					<?php } ?>
						<li><a href="lista_alumno.php"><i class="fas fa-list"></i> Lista de Estudiantes</a></li>
					</ul>
				</li>
				<?php } ?>
			
				<li class="principal">
					<a href="#"><i class="fas fa-chalkboard-user"></i> Catedraticos</a>
					<ul>
				<?php
					if($_SESSION['rol'] == 1){
				?>
						<li><a href="registro_catedratico.php"><i class="fas fa-plus"></i> Nuevo Catedratico</a></li>
				<?php } ?>
						<li><a href="lista_catedratico.php"><i class="fas fa-list"></i> Lista de Catedraticos</a></li>
					</ul>
				</li>
				<li class="principal">
					<a href="#"><i class="fas fa-book"></i> Cursos</a>
					<ul>
				<?php
					if($_SESSION['rol'] == 1){
				?>
						<li><a href="registro_curso.php"><i class="fas fa-plus"></i> Nuevo Curso</a></li>
				<?php } ?>
						<li><a href="lista_curso.php"><i class="fas fa-list"></i> Lista de Cursos</a></li>
					</ul>
				</li>
				<li class="principal">
					<a href="#"><i class="fas fa-school"></i> Matriculas</a>
					<ul>
				<?php
					if($_SESSION['rol'] == 1){
				?>
						<li><a href="nueva_inscripcion.php"><i class="fas fa-plus"></i> Nueva Matricula</a></li>
						
						<li><a href="lista_matricula.php"><i class="fas fa-list"></i> Ver Matriculas</a></li>
				<?php } ?>
				<?php
					if($_SESSION['rol'] == 2){
				?>
						<li><a href="mimatricula.php"><i class="fas fa-eye"></i> Ver mi matricula</a></li>
				<?php } ?>
			</ul>
			<li class="principal">
					<a href="#"><i class="fas fa-file-pdf"></i> Reportes</a>
					<ul>
				<?php
					if($_SESSION['rol'] == 1){
				?>
						<li><a href="reportes/reporte_alumno.php"><i class="fas fa-file-pdf"></i> Reporte de alumnos activos</a></li>
						
						<li><a href="reportes/reporte_catedraticos.php"><i class="fas fa-file-pdf"></i> Reporte de catedraticos activos</a></li>

						<li><a href="reportes/reporte_cursos.php"><i class="fas fa-file-pdf"></i> Reporte de cursos activos</a></li>

						<li><a href="reportes/reporte_facultades.php"><i class="fas fa-file-pdf"></i> Reporte de todas las facultades</a></li>

						<li><a href="reportes/reporte_matriculas.php"><i class="fas fa-file-pdf"></i> Reporte de todas las matriculas</a></li>
				<?php } ?>
					</ul>
				</li>
				<li class="principal">
					<a href="#"><i class="fas fa-chart-simple"></i> Estadisticas</a>
					<ul>
				<?php
					if($_SESSION['rol'] == 1){
				?>
						<li><a href="graficoNotasAlumno.php"><i class="fas fa-chart-simple"></i> Estadisticas de la Universidad</a></li>
				<?php } ?>
					</ul>
				</li>
		</nav>