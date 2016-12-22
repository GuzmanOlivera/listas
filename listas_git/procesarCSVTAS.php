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

function buscarPosSeccion($linea) {
   $arrayLinea = string2array($linea);
   foreach ($arrayLinea as $k => $v)  {
      if ($v == "seccion") {
         return $k;
      }
   }
   return -1;
}

function fixNullValues($stringLinea) {
     $stringLinea = rtrim($stringLinea);
     $lineaArray = array_map('trim', str_getcsv($stringLinea,',', '"'));
     print_r($lineaArray);
     for($i=0; $i < count($lineaArray);$i++) {

             if ($lineaArray[$i]=="") {
                  $lineaArray[$i]="NULL";
             }
      }
      print_r($lineaArray);
      echo "<br>";
      $aux = "'" . implode("','", $lineaArray) . "'";
      return str_replace("'NULL'",'NULL', $aux);
}

// Toma un array con valores y devuelve un string con valores para insertar
function valoresLinea($lineaArray) {
    for($i=0; $i < count($lineaArray);$i++) {
             if ($lineaArray[$i]=="") {
                  $lineaArray[$i]="NULL";
             }
    }
    $aux = "'" . implode("','", $lineaArray) . "'";
    return str_replace("'NULL'",'NULL', $aux);
}

function valoresEstudiantes($arrayCabecera, $arrayCabeceraEstudiante, $arrayLinea) {
    $resultado = array();
    for($i = 0; $i < count($arrayCabecera); $i++) {
         $found = array_search($arrayCabecera[$i],$arrayCabeceraEstudiante);
         if ($found !== false) {
                          array_push($resultado,$arrayLinea[$i]);   
         } else {
             continue;
      //Si algo anda mal probar con $found en vez de $i
         }
    }
    return $resultado;
}

// Devuelve los campos de estudiante en el orden que fueron ingresados en el CSV
function camposTAS($arrayCabecera, $arrayCabeceraEstudiante){ 
    $resultado = array();
    for($i = 0; $i < count($arrayCabecera); $i++) {
         $found = array_search($arrayCabecera[$i],$arrayCabeceraEstudiante);
         if ($found !== false) {
             array_push($resultado,$arrayCabecera[$i]);         //Si algo anda mal probar con $found en vez de $i
         } else {
             continue;
             
         }
    }
    return $resultado;
}

function valoresPersona($arrayCabecera, $arrayCabeceraEstudiante, $arrayLinea) {
    $resultado = array();
    for($i = 0; $i < count($arrayCabeceraEstudiante); $i++) {
        $found = array_search($arrayCabeceraEstudiante[$i],$arrayCabecera);
        if ($found !== false) {
            array_push($resultado,$arrayLinea[$found]);          
        }
    }
    return $resultado;
    
}

//Considerar lo siguiente:
//Los cargos que empiezan con 6 son no docente
//stringLinea nunca puede ser vacio cuando se invoca a esta funcion
function guardarLineaEnBD2($stringLinea,$cabecera,$arrayCabeceraPersona,$arrayCabeceraTAS,$dbh) {

    $lineaArray = array_map('trim', str_getcsv($stringLinea,',', '"'));
    $arrayCabecera = string2array($cabecera);
    $stringLinea = fixNullValues($stringLinea);
    $cabeceraPersona = array2string($arrayCabeceraPersona, ",");

    $posNroCargo = buscarPosNroCargo($cabecera);
    if ($posNroCargo==-1) {
      die("Debe haber un campo nroCargo en la cabecera del archivo");
    }
    
    $posMail = buscarPosMail($cabecera);
    if ($posMail==-1) {
        die("Debe haber un campo mail en la cabecera del archivo");
    }
    $mail = $lineaArray[$posMail];

    $posCI = buscarPosCI($cabecera);
    if ($posCI==-1) {
      die("Debe haber un campo CI en la cabecera del archivo");
    }

    $nroCargo = $lineaArray[$posNroCargo];

    $posInstitucionalDSC = buscarPosInstitucionalDSC($cabecera);
    if ($posInstitucionalDSC==-1) {
      die("Debe haber un campo institucionalDSC en la cabecera del archivo");
    }
    
    $posSeccion = buscarPosSeccion($cabecera);
    
    $institucionalDSC = $lineaArray[$posInstitucionalDSC];
    $ci = $lineaArray[$posCI];

    $stringLinea = fixNullValues($stringLinea);

    $sql = "INSERT INTO InstitucionalDSC(Nombre) SELECT * FROM (SELECT '$institucionalDSC') AS tmp WHERE NOT EXISTS (SELECT Nombre from InstitucionalDSC WHERE Nombre='$institucionalDSC')";

    echo $sql;
    echo "<br>";
    $colField = $dbh->prepare($sql);
    $colField->execute();

    // Insertar en tabla persona
    $arrayValoresPersona = valoresPersona($arrayCabecera, $arrayCabeceraPersona, $lineaArray);
    $valoresPersona = valoresLinea($arrayValoresPersona); // String
    
    
    $sql = "INSERT IGNORE INTO persona($cabeceraPersona) VALUES($valoresPersona)";
    
    echo $sql;
    echo "<br>";

    $colField = $dbh->prepare($sql);
    $colField->execute();
        
    // Insertamos en tabla TAS    
    
       // Estudiante: ciEstudiante, generacion,carrera,regionEstudiante
    // Ninguno es nulo
    $arrayCamposTAS = camposTAS($arrayCabecera, $arrayCabeceraTAS);    
    $arrayValoresTAS = valoresEstudiantes($arrayCabecera, $arrayCabeceraTAS, $lineaArray);
    
    $camposTAS = array2string($arrayCamposTAS,',');
    
    $valoresTAS = valoresLinea($arrayValoresTAS); // String
    
    $camposTAS = $camposTAS . ",ciTAS";
    $valoresTAS = $valoresTAS . "," . $ci;
    
    $sql = "INSERT IGNORE INTO tas($camposTAS) VALUES($valoresTAS)";
    
    /* VIEJO 
    // Opcionalmente puede haber un campo seccion
    if ($posSeccion==-1) {
        $sql = "INSERT IGNORE INTO TAS(ciTAS) VALUES('$ci')";
    }
    else {
        $seccion = $lineaArray[$posSeccion];
        $sql = "INSERT IGNORE INTO TAS(ciTAS,seccion) VALUES('$ci','$seccion')";
    }
     * 
     * 
     */
    echo $sql;
    $colField = $dbh->prepare($sql);
    $colField->execute();  

}


function quitarElementos($stringLinea, $cabecera, $elementos) { // FIXME: Hacer mas dinamico 
    
    $cabecera_array = string2array($cabecera);
    print_r($cabecera_array);
    
    $lineaArray = array_map('trim', str_getcsv($stringLinea,',', '"'));
    
    $largo = count($elementos);
    echo "Lago es" . $largo;
    
    $copiaElementos = clonarArray($elementos);
    for ($i=0; $i<$largo;$i++) {
        //$pos = array_search($elementos[$i], $lineaArray);
        $pos = array_search($elementos[$i], $cabecera_array);
        echo '<br>';
        echo "La posicion es " . $pos;
        unset($lineaArray[$pos]);
        unset($cabecera_array[$pos]);
        array_del($copiaElementos,$elementos[$i]);
        //Quitar dicho elemento del header
    }
    
    $cabeceraNueva = array2string($cabecera_array, ',');
    
    foreach($lineaArray  as $key=>$value)
    {
        if(!is_numeric($value))
        {
            if ($value=="") {
                 $insert_array[] = "NULL";
            }
               else {
                 $insert_array[] = "'$value'";
            }
        }
        else
        {
            if ($value=="") {
                 $insert_array[] = "NULL";
            }
            else {
                $insert_array[] = "$value";
            }
        }
        
    }
    $aux = implode(',', $insert_array);
    $resultado = array(str_replace("'NULL'",'NULL', $aux),$cabeceraNueva);
    return $resultado;
    //return str_replace("'NULL'",'NULL', $aux);
}
function insertValue($string) {
    if ($string==""){
       return 'NULL';         
    }
    elseif(!is_numeric($string)) {
       return "'$string'";
    }
    return $string;
}

function insertarOpcionales($cabecera, $ci, $opcionales, $dbh, $lineaArray) {
    $cabeceraArray = string2array($cabecera);
    echo "<br>" . "<br>" . "<br>";
    print_r($cabeceraArray);
    echo "<br>" . "<br>" . "<br>";
    print_r($opcionales);
    echo "<br>" . "<br>" . "<br>";
    print_r($lineaArray);
    echo "<br>" . "<br>" . "<br>";
    
    for($i = 0; $i < count($opcionales); $i++){
        echo "<br>" . "<br>" . "<br>";
        echo $opcionales[$i];
        echo "<br>" . "<br>" . "<br>";
        $pos = array_search($opcionales[$i], $cabeceraArray);
        if ($pos==-1)
           continue; //Significa que no se especifico el campo opcional y por ende no hacemos nada
        else {
            $valorElemento = insertValue($lineaArray[$pos]);
            switch ($opcionales[$i]) {
                case 'region':
                    $sql = "INSERT IGNORE INTO region(nombre) VALUES($valorElemento)";
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    
                    $sql = "INSERT IGNORE INTO regionPersona(nombreRegion, ciRegionPersona) VALUES($valorElemento, $ci)"; 
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    break;
                case 'nroCargo':
                    $sql = "INSERT IGNORE INTO nroCargo(nroCargo,ciNroCargo) VALUES($valorElemento,$ci)";
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    break;
                case 'institucionalDSC':
                    $sql = "INSERT IGNORE INTO InstitucionalDSC(nombre) VALUES($valorElemento)";
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    
                    $sql = "INSERT IGNORE INTO InstitucionalDSC_Persona(nombre, ciPersona) VALUES($valorElemento, $ci)"; 
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    break;
                break;
            }
        }
    }
}

function guardarLineaEnBD($stringLinea,$cabecera,$dbh) {

    $lineaArray = array_map('trim', str_getcsv($stringLinea,',', '"'));
    
    $posMail = buscarPosMail($cabecera);
    $posCI = buscarPosCI($cabecera);
    $posSeccion = buscarPosSeccion($cabecera);
   
    if ($posMail==-1) {
        die("Debe haber un campo mail en la cabecera del archivo");
    }
    elseif ($posCI==-1) {
      die("Debe haber un campo CI en la cabecera del archivo");
    }
    else {
        if ($lineaArray[$posCI]=="") {
            die("La CI no puede ser vacia en " . $stringLinea);
        }
    }
    
    $mail = $lineaArray[$posMail];
    $ci = $lineaArray[$posCI];
     
    $elementos = ['nroCargo','institucionalDSC','region','seccion']; //Elementos que no se guardan en la tabla personas pero pueden aparecer en el encabezado
    $resultadoAux = quitarElementos($stringLinea, $cabecera, $elementos); // Devuelve una tupla con la linea y cabecera actualizadas
  
    $stringLinea = $resultadoAux[0];
    $stringCabecera = $resultadoAux[1];
    
    // Insertar en tabla persona
    $sql = "INSERT IGNORE INTO persona($stringCabecera) VALUES($stringLinea)";
    
    echo $sql;
    echo "<br>";

    $colField = $dbh->prepare($sql);
    $colField->execute();
        
    // Insertamos en tabla TAS
    
    if ($posSeccion==-1) {
        $sql = "INSERT INTO tas(ciTAS) VALUES($ci)"; 
    } 
    else {
        $seccion = $lineaArray[$posSeccion];
        $stringSeccion = insertValue($carrera);
        $sql = "INSERT INTO tas(ciTAS,seccion) VALUES($ci,$stringSeccion)"; 
    }
    
    echo $sql;    
    $colField = $dbh->prepare($sql);
    $colField->execute();  
    // Elementos obligatorios a insertar en otras tablas
    //$obligatorios = ['region'];
    //insertarObligatorios($cabecera, $ci, $obligatorios, $dbh, $lineaArray);
    
    // Consideramos que hay elementos opcionales que requieren insertarse en otras tablas
    $opcionales = ['nroCargo','institucionalDSC','region'];
    insertarOpcionales($cabecera, $ci, $opcionales, $dbh, $lineaArray);
    
}

function guardarLineaEnBDMismaPersona($stringLinea,$cabecera,$dbh) {

    $lineaArray = array_map('trim', str_getcsv($stringLinea,',', '"'));
    
    $posMail = buscarPosMail($cabecera);
    if ($posMail==-1) {
        die("Debe haber un campo mail en la cabecera del archivo");
    }
    $mail = $lineaArray[$posMail];

    $posCI = buscarPosCI($cabecera);
    if ($posCI==-1) {
      die("Debe haber un campo CI en la cabecera del archivo");
    }
    $ci = $lineaArray[$posCI];
            
    // Consideramos que hay elementos opcionales que requieren insertarse en otras tablas
    $opcionales = ['lugarResidencia','region','nroCargo','institucionalDSC'];
    insertarOpcionales($cabecera, $ci, $opcionales, $dbh, $lineaArray);      
       
}

function guardarLineaEnBDSinMail($stringLinea,$cabecera,$dbh) {
    # echo "Se guarda en BD Sin mail";
        
    $stringLinea = fixNullValues($stringLinea);
   
    $sql = "INSERT INTO personaSinMail($cabecera) VALUES($stringLinea)";
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
    $ciInsertadas = array(); 
  /*
         $encontrado = array_search($arrayCabecera[$i], $arrayCabeceraEstudiante); 
       if (!($encontrado!==FALSE)) {
  */
  
    $resultadoRepetidos = array();
    $resultadoSinMails = array();
    $cabecera = $arrayPlanilla[0];
  
    $cabeceraArraySinEspacios = array_map('trim', string2array($cabecera));
 /*
  if (chequearCabecera($cabeceraArraySinEspacios,$dbh)) {
     echo "OK";
  }
  else {
     die("Debe subir una planilla con los campos correctos, leer la documentacion apropiada");
  }*/

    $cabecera = array2string(",",$cabeceraArraySinEspacios); //String cabecera sin espacios
    $cabecera = str_replace('"', '', $cabecera);
  
    $posCedula = buscarPosCI($cabecera);
    if ($posCedula==-1) {
        die("Debe haber un campo ci en la cabecera del archivo");
    }
    $posMail = buscarPosMail($cabecera);
    $posCI = buscarPosCI($cabecera);
    
    if ($posMail==-1) {
        die("Debe haber un campo mail en la cabecera del archivo");
    }
    elseif ($posCI==-1) {
      die("Debe haber un campo CI en la cabecera del archivo");
    }
    
  for($i = 1; $i < count($arrayPlanilla); ++$i) {
    $lineaStringPlanilla = $arrayPlanilla[$i];
    if ($lineaStringPlanilla=="") {
       continue;
    }
    $arrayLinea = str_getcsv($lineaStringPlanilla, ',', '"');
    
    $cedula = $arrayLinea[$posCedula];
    $mail = $arrayLinea[$posMail];
    
    $encontrado = array_search($cedula, $ciInsertadas); 
    if (!($encontrado!==FALSE)) {
        array_push($ciInsertadas,$cedula);
        if ($mail=="") {
            guardarLineaEnBDSinMail($lineaStringPlanilla, $cabecera, $dbh);
        }
        else {
            guardarLineaEnBD($lineaStringPlanilla, $cabecera, $dbh);
        }
    }
    else {
        if ($mail=="") {
            guardarLineaEnBDSinMail($lineaStringPlanilla, $cabecera, $dbh);            
        }
        else {
            guardarLineaEnBDMismaPersona($lineaStringPlanilla, $cabecera, $dbh);
        }
        $resultadoRepetidos[] = $lineaStringPlanilla; // Los repetidos los guardamos en un CSV
    }
  }
  print_r($resultadoRepetidos);
     
}

function planillaFiltrada2($arrayPlanilla,$dbh) {
        
  $arrayPlanillaCopia = clonarArray($arrayPlanilla);

  $resultadoRepetidos = array();
  $resultadoSinMails = array();
  $cabecera = $arrayPlanilla[0];

  $cabeceraArraySinEspacios = array_map('trim', string2array($cabecera));
/* 
  if (chequearCabecera($cabeceraArraySinEspacios,$dbh)) {
     echo "OK";
  }
  else {
     die("Debe subir una planilla con los campos correctos, leer la documentacion apropiada");
  }

*/
  $cabecera = array2string(",",$cabeceraArraySinEspacios); //String cabecera sin espacios
  $cabecera = str_replace('"', '', $cabecera);

  $camposTAS = "ciTAS,seccion"; // FIXME: Hacer dinamico
  //$arrayCabecera = string2array($cabecera);
  $arrayCabeceraTAS = string2array($camposTAS);
  
  $arrayCabeceraTAS = array_map('trim', $arrayCabeceraTAS); 
  $cabeceraArraySinEspacios = array_map('trim', string2array($cabecera));

  /* LLAMAR A chequearCabecera aqui */ 
  
  $cabeceraPersona = array_diff($cabeceraArraySinEspacios, $arrayCabeceraTAS); // Array Cabecera para tabla persona
  
  $cabecera = array2string(",",$cabeceraArraySinEspacios); //String cabecera sin espacios
  $cabecera = str_replace('"', '', $cabecera); // String cabecera completo, con todos los campos inclusive los de TAS
  
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
       guardarLineaEnBD($lineaStringPlanilla, $cabecera, $cabeceraPersona, $arrayCabeceraTAS, $dbh);
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
