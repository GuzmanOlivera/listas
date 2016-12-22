<?php
header('Content-type: text/html; charset=utf-8');  

include_once "conexion.php";

error_reporting(E_ALL);
ini_set('display_errors', 'on');

$conexion = new Connection;
$dbh = $conexion->getConnection();

    $sql = "SELECT p.ci, p.mail, p.nombre, p.segundoNombre, p.apellido, p.segundoApellido, p.region, n.nroCargo, i.nombre as institucionalDSC, d.DptoAcademico from persona as p JOIN docente as d on p.ci=d.ciDocente JOIN nroCargo as n on n.ciNroCargo=p.ci LEFT JOIN InstitucionalDSC_Persona as i on i.ciPersona=p.ci;";

    $header = "ci,mail,nombre,segundoNombre,apellido,segundoApellido,region,nroCargo,institucionalDSC,DptoAcademico" . "\n";

    $sth = $dbh->prepare($sql);

    $sth->execute();

    $filename = "docentes".'-'.date('d.m.Y_H.i.s').'.csv';

    $data = fopen("upload/" . $filename, 'w');
    fwrite($data, $header);

    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($data, $row);
    }

    fclose($data);

    header("Location: upload/" . $filename);
?> 
