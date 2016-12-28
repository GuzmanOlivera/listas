<?php
header('Content-type: text/html; charset=utf-8');  

include_once "conexion.php";

error_reporting(E_ALL);
ini_set('display_errors', 'on');

$conexion = new Connection;
$dbh = $conexion->getConnection();

    $sql = "SELECT ci,mail,COALESCE(nombre,'') as nombre,COALESCE(segundoNombre,'') as segundoNombre,COALESCE(apellido,'') as apellido,COALESCE(segundoApellido,'') as segundoApellido,COALESCE(region,'') as region,COALESCE(nroCargo,'') as nroCargo,COALESCE(institucionalDSC,'') as institucionalDSC FROM personaSinMail;";

    $header = "ci,mail,nombre,segundoNombre,apellido,segundoApellido,region,nroCargo,institucionalDSC" . "\n";

    $sth = $dbh->prepare($sql);

    $sth->execute();

    $filename = "sinMail".'-'.date('d.m.Y_H.i.s').'.csv';

    $data = fopen("upload/" . $filename, 'w');
    fwrite($data, $header);

    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($data, $row);
    }

    fclose($data);

    header("Location: upload/" . $filename);
?> 
