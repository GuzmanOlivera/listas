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

function csv2array($archivo) {
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

function buscarPosMail($linea) {
   $arrayLinea = string2array($linea);
   foreach ($arrayLinea as $k => $v)  {
      if ($v == "mail") {
         return $k;
      }
   }
   return -1;
}

function buscarPosNroCargo($linea) {
   $arrayLinea = string2array($linea);
   foreach ($arrayLinea as $k => $v)  {
      if ($v == "nroCargo") {
         return $k;
      }
   }
   return -1;
}

function buscarPosInstitucionalDSC($linea) {
   $arrayLinea = string2array($linea);
   foreach ($arrayLinea as $k => $v)  {
      if ($v == "institucionalDSC") {
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
function guardarLineaEnBD($stringLinea, $cabecera, $dbh) {

    /* DELETES DE LA BD, usado para las pruebas */
/*
    $sql = "DELETE FROM docente";
    $colField = $dbh->prepare($sql);
    $colField->execute();

    $sql = "DELETE FROM persona";
    $colField = $dbh->prepare($sql);
    $colField->execute();

    $sql = "DELETE FROM InstitucionalDSC";
    $colField = $dbh->prepare($sql);
    $colField->execute();

*/

    /* Borrarlo cuando este para produccion */

    $lineaArray = array_map('trim', str_getcsv($stringLinea,',', '"'));

    $posNroCargo = buscarPosNroCargo($cabecera);
    if ($posNroCargo==-1) {
      die("Debe haber un campo nroCargo en la cabecera del archivo");
    }

    $posCI = buscarPosCI($cabecera);
    if ($posCI==-1) {
      die("Debe haber un campo CI en la cabecera del archivo");
    }

    $nroCargo = $lineaArray[$posNroCargo];

    $posInstitucionalDSC = buscarPosInstitucionalDSC($cabecera);
    if ($posInstitucionalDSC==-1) {
      die("Debe haber un campo institucionalDSC en la cabecera del archivo");
    }
    $institucionalDSC = $lineaArray[$posInstitucionalDSC];
    $ci = $lineaArray[$posCI];

    $stringLinea = fixNullValues($stringLinea);
    
    $arrayCabecera = string2array($cabecera,',');
        
/*  Para los update usaremos dos arrays en principio y son :

 * $lineaArray: Contiene los valores de los campos que vamos a actualizar
 * $arrayCabecera: Contiene los nombres de los campos que vamos a actualizar
 * Ademas usaremos la variable $ci para filtrar los datos de manera unica
 *  */ 
    
    $stringColVal = "";
    for($i = 0; $i < count($arrayCabecera); $i++) {
        if ($i == count($arrayCabecera)-1) {
            if (is_numeric($lineaArray[$i])){
            $stringColVal = $stringColVal . $arrayCabecera[$i] . "=" . "$lineaArray[$i]";  
                
            }
            else {
                $stringColVal = $stringColVal . $arrayCabecera[$i] . "=" . "'" .  "$lineaArray[$i]" . "'";  
            }
        }
        else {
                if (is_numeric($lineaArray[$i])){
                   $stringColVal = $stringColVal  . $arrayCabecera[$i] . "=" . "$lineaArray[$i]" . ",";
                }
                else
                {
                   $stringColVal = $stringColVal . $arrayCabecera[$i] . "=" . "'" . "$lineaArray[$i]" . "'" . ",";
                }
        }
    }
    $stringCondicion = "ci=" . $ci;
  
    // Insertamos en tabla InstitucionalDSC
    // Si no existe lo intertamos
    $sql = "INSERT IGNORE INTO InstitucionalDSC(Nombre) SELECT * FROM (SELECT '$institucionalDSC') AS tmp WHERE NOT EXISTS (SELECT Nombre from InstitucionalDSC WHERE Nombre='$institucionalDSC')";
    echo $sql;
    echo "<br>";
    $colField = $dbh->prepare($sql);
    $colField->execute();

    // Insertar en tabla persona
    $sql = "UPDATE persona SET " . $stringColVal . " WHERE " . $stringCondicion . ";" ;
    echo $sql;
    echo "<br>";
    $colField = $dbh->prepare($sql);
    $colField->execute();
  
    //  Ejemplo de stringLinea
    //  28741991,Acosta,Larrosa,Martin,Ignacio,tincho977@hotmail.com,555306,T/C Área Técnico Profesional 
        //  Ejemplo de cabecera
        //  ci,apellido,segundoApellido,nombre,segundoNombre,mail,nroCargo,institucionalDSC 
        
        
}

function guardarLineaEnBDSinMail($stringLinea,$cabecera,$dbh) {
    # echo "Se guarda en BD Sin mail";
    echo "<br>";
    echo "<br>";
    echo "<br>";
    echo "Linea que no nos deja insertar";
    echo "<br>";
    echo $stringLinea;
    echo "<br>";
    echo "<br>";
    echo "<br>";
    echo "<br>";

    $stringLinea = fixNullValues($stringLinea);

    echo "<br>";
    echo "Linea despues de fixNullvalues";
    echo "<br>";
    echo $stringLinea;
    echo "<br>";
    
    $sql = "INSERT IGNORE INTO personaSinMail($cabecera) VALUES($stringLinea)"; //FIXME: ver que forma hay de actualizar a los sinMail.
    echo $sql;
    echo "<br>";

    $colField = $dbh->prepare($sql);
    $colField->execute();
}

// Esta funcion chequea que las celdas de la cabecera coincidan con los nombres de atributos de la base de datos.
function chequearCabecera($cabecera, $dbh) {

  $counter = 0;
  $sql="";
  echo count($cabecera);
  if (is_array($cabecera) || is_object($cabecera)){
     foreach ($cabecera as $k => $field) {
        $field = str_replace('"', '', $field);
        $sql = "SHOW COLUMNS FROM `persona` LIKE '$field'";
        $colField = $dbh->prepare($sql);
        $colField->execute();
        echo $sql;
        echo "<br>";
        /* Devolver el numero de filas resultantes */
        $cuenta = $colField->rowCount();
        $exists = ($cuenta == 1) ? TRUE : FALSE;
        if($exists) {
          $counter++;
       }
    }
  }
  echo $counter;
  // Si es verdadero entonces la totalidad de elementos de la cabecera estan en la base de datos
  return ($counter == count($cabecera)) ? TRUE : FALSE;
}

function planillaFiltrada($arrayPlanilla,$dbh) {
  
  $arrayPlanillaCopia = clonarArray($arrayPlanilla);

  $resultadoRepetidos = array();
  $resultadoSinMails = array();
  $cabecera = $arrayPlanilla[0];

  $cabeceraArraySinEspacios = array_map('trim', string2array($cabecera));
 
  if (chequearCabecera($cabeceraArraySinEspacios,$dbh)) {
     echo "OK";
  }
  else {
     die("Debe subir una planilla con los campos correctos, leer la documentacion apropiada");
  }

  $cabecera = array2string(",",$cabeceraArraySinEspacios); //String cabecera sin espacios
  $cabecera = str_replace('"', '', $cabecera);


  $posCedula = buscarPosCI($cabecera);
  if ($posCedula==-1) {
    die("Debe haber un campo ci en la cabecera del archivo");
  }

  $posMail = buscarPosMail($cabecera);
  if ($posMail==-1) {
    die("Debe haber un campo mail en la cabecera del archivo");
  }

  for($i = 1; $i < count($arrayPlanilla); ++$i) {
    $lineaStringPlanilla = $arrayPlanilla[$i];
    if ($lineaStringPlanilla=="") {
       continue;
    }

    $arrayLinea = str_getcsv($lineaStringPlanilla, ',', '"');

#string2array($lineaStringPlanilla);

    $cedula = $arrayLinea[$posCedula];
    $mail = $arrayLinea[$posMail];
    if ($mail=="") {
          $resultadoSinMails[] = $lineaStringPlanilla;
    }
    $ocurrencias = 0;
    if ($cedula=="") {
      die("CI vacia en $lineaStringPlanilla");

    }
 
    for($j = 1; $j < count($arrayPlanillaCopia); $j++){ 
        $lineaPlanillaCopia = $arrayPlanillaCopia[$j];
        if ($lineaPlanillaCopia=="") {
          
          continue;
        }
        $arrayLineaCopia = str_getcsv($lineaPlanillaCopia,',','"');
        $cedulaCopia = $arrayLineaCopia[$posCedula];
        if ($cedulaCopia==$cedula) {
           $ocurrencias++;
        }
        // Siempre va a entrar en este if, al menos una vez. Esta linea sera la que consideremos para guardar en la BD.
        // Si luego hay otras lineas con la misma CI entonces no las guardamos. Esto se controla mas abajo con el condicional de ocurrencias>1
        if ($ocurrencias==1) {
           $lineaCIUnica = $lineaStringPlanilla;
        }
    }  
    // En resumen, si es linea repetida la agregamos al array de lineas repetidas
    if (($ocurrencias>1) && ($lineaStringPlanilla!=$lineaCIUnica)) {
       $resultadoRepetidos[] = $lineaStringPlanilla; // Los repetidos los guardamos en un CSV
    }
    elseif(($ocurrencias==1) && ($mail!="")){
       guardarLineaEnBD($lineaStringPlanilla, $cabecera, $dbh);
    }
    elseif(($ocurrencias==1) && ($mail=="")){
       guardarLineaEnBDSinMail($lineaStringPlanilla, $cabecera, $dbh); //Hay que crear tabla para los sin mail, vamos a hacera facil :)
    }
  } 
}

#mkdir("/upload", 0775);
$allowedExts = array("csv");
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);

#unlink("upload/" . $_FILES["file"]["name"]);

if (($_FILES["file"]["type"] == "text/csv") && (in_array($extension, $allowedExts))) {
  if ($_FILES["file"]["error"] > 0) {
    echo "ERROR! Codigo del error: " . $_FILES["file"]["error"] . "<br>";
  } else {
 #   echo "Upload: " . $_FILES["file"]["name"] . "<br>";
  #  echo "Type: " . $_FILES["file"]["type"] . "<br>";
  #  echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
  #  echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
    if (file_exists("upload/" . $_FILES["file"]["name"])) {
      echo $_FILES["file"]["name"] . " el archivo ya existe. Borrarlo de upload/";
#      unlink("upload/" . $_FILES["file"]["name"]);
    } else {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "upload/" . $_FILES["file"]["name"]);
 #     echo "Se guarda el archivo en: " . "upload/" . $_FILES["file"]["name"];
    }
    $arrayPlanilla = csv2array("upload/" . $_FILES["file"]["name"]);
    $conexion = new Connection;
    $conn = $conexion->getConnection();
    planillaFiltrada($arrayPlanilla, $conn);
    unlink("upload/" . $_FILES["file"]["name"]);
  
  }
} else {
  echo "Por favor suba un archivo en formato CSV, lo puede conseguir exportandolo desde LibreOffice";
}
?> 
