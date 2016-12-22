<?php
header('Content-type: text/html; charset=utf-8');

include_once "conexion.php";

error_reporting(E_ALL);
ini_set('display_errors', 'on');

// $needle: substring con el cual se verificara si param1 empieza con el mismo 
// $haystack: string a analizar
function startsWith($haystack, $needle) {
//          search backwards starting from haystack length characters from the end
   return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

// $needle: substring con el cual se verificara si param1 termina con el mismo 
// $haystack: string a analizar
function endsWith($haystack, $needle) {
// search forward starting from end minus needle length characters
   return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

// Elimina elementos con valor $val del array $arr
function array_del($arr, $val)
{
    unset($arr[array_search($val, $arr)]);
    return array_values($arr);
}

function csv2array($archivo){
   $array = array(); 
   $myfile = fopen($archivo, "r") or die("Imposible abrir el archivo");
   // Output one line until end-of-file
 #  fgets($myfile); //Ignore the first line La primer linea la necesitamos
   while(!feof($myfile)) {
      array_push($array,fgets($myfile)); 
   }
   fclose($myfile);
   return $array;
}

function clonarArray($arr) {
    $newArray = array();
    foreach($arr as $key => $value) {
        if(is_array($value)) $newArray[$key] = array_copy($value);
        else if(is_object($value)) $newArray[$key] = clone $value;
        else $newArray[$key] = $value;
    }
    return $newArray;
}

//String con campos separados por comas a array
function string2array($string) { 
  return explode(',', $string);
}

function array2string($array, $separador) {
  return implode($separador, $array);
}

function buscarPosCI($linea) {
   $arrayLinea = string2array($linea);
   foreach ($arrayLinea as $k => $v)  {
      if ($v == "ci") {
         return $k;
      } 
   }
   return -1;
}


function fixNullValues($stringLinea) {
     $stringLinea = rtrim($stringLinea);
     $lineaArray = array_map('trim', str_getcsv($stringLinea,',', '"'));
     for($i=0; $i < count($lineaArray);$i++) {
             if ($lineaArray[$i]=="") {
                  $lineaArray[$i]="NULL";
             }
      }
      $aux = "'" . implode("','", $lineaArray) . "'";
      return str_replace("'NULL'",'NULL', $aux);
}

//Considerar lo siguiente:
//Los cargos que empiezan con 6 son no docente
//stringLinea nunca puede ser vacio cuando se invoca a esta funcion
function borrarEnBD($ci,$dbh) {
    
    $sql = "DELETE FROM docente where ciDocente=$ci";
    $colField = $dbh->prepare($sql);
    $colField->execute();
    
    $sql = "DELETE FROM egresado where ciEgresado=$ci";
    $colField = $dbh->prepare($sql);
    $colField->execute();
    
    $sql = "DELETE FROM estudiante where ciEstudiante=$ci";
    $colField = $dbh->prepare($sql);
    $colField->execute();
    
    $sql = "DELETE FROM tas where ciTAS=$ci";
    $colField = $dbh->prepare($sql);
    $colField->execute();
    
    $sql = "DELETE FROM personaSinMail where ci=$ci";
    $colField = $dbh->prepare($sql);
    $colField->execute();
  
    $sql = "DELETE FROM persona where ci=$ci";
    $colField = $dbh->prepare($sql);
    $colField->execute();
      
}

$ci = $_POST['ci'];
$conexion = new Connection;
$conn = $conexion->getConnection();
borrarEnBD($ci, $conn);

?>