<?php

	try{
//	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$pdo_options[PDO::ATTR_ERRMODE]=PDO::ERRMODE_EXCEPTION;
	$DataBase = new PDO('mysql:host=localhost;dbname=BDSYGES','root','',$pdo_options);
	$DataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	
	}
catch ( Exception $e ) 
	{
		  echo "Connection à MySQL impossible : ", $e->getMessage();  die();
	}


?>