<?php 
	date_default_timezone_set('America/Guatemala'); 
	
	function fechaC(){
		$mes = array("","enero", 
					  "febrero", 
					  "marzo", 
					  "abril", 
					  "mayo", 
					  "junio", 
					  "julio", 
					  "agosto", 
					  "septiembre", 
					  "octubre", 
					  "noviembre", 
					  "diciembre");
		return date('d')." de ". $mes[date('n')] . " de " . date('Y');
	}


 ?>