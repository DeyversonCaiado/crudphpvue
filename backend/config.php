<?php
	
    // Version
    $VERSION='2.3.0.2';
    
    // DB
    $DB_DRIVER='mysqli';
    $DB_HOSTNAME='localhost';
    $DB_USERNAME='u182511529_clique';
    $DB_PASSWORD='Campos1@2ewq';
    $DB_DATABASE='u182511529_clique';
    $DB_PORT='3306';
    $DB_PREFIX='';

	$mysqli = new mysqli($DB_HOSTNAME,$DB_USERNAME,$DB_PASSWORD,$DB_DATABASE);
	
	date_default_timezone_set('America/Sao_Paulo');

?>