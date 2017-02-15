<?php
header('Content-type: text/html; charset=utf-8');

include_once "conexion.php";

error_reporting(E_ALL);
ini_set('display_errors', 'on');

/*********************************************/
/*********************************************/
/****** Funciones que no se estan usando *****/
/*********************************************/
/*********************************************/

// $needle: substring con el cual se verificara si param1 termina con el mismo 
// $haystack: string a analizar
function endsWith($haystack, $needle) {
// search forward starting from end minus needle length characters
   return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

function quitarElementos2($stringLinea, $cabecera, $elementos) { // FIXME: Hacer mas dinamico 
    
    $cabecera_array = string2array($cabecera);
    print_r($cabecera_array);
/*
    $posElementos = array();
    for ($i=0; $i<count($elementos);$i++) {
        $pos = array_search($elementos[$i], $cabecera_array);
        array_push($posElementos, $pos);
    }
  */  
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
    //$posInstitucionalDSC = array_search($stringInstitucionalDSC, $cabecera_array);
    //unset($lineaArray[$posInstitucionalDSC]);
    foreach($lineaArray  as $key=>$value)
    {
        if(!is_numeric($value))
        {
            $insert_array[] = "'$value'";
        }
        else
        {
            $insert_array[] = "$value";
        }
    }
    $aux = implode(',', $insert_array);
    $resultado = array(str_replace("'NULL'",'NULL', $aux),$cabeceraNueva);
    return $resultado;
    //return str_replace("'NULL'",'NULL', $aux);
}

function inicializarMarcas($arr) { 
    $resultado = array();
    for ($i = 0; $i < count($arr); $i++) {
        $resultado[$i][0] = $arr[$i]; 
        $resultado[$i][1] = FALSE;
    }
    return $resultado;
}

function quitarElemento($stringLinea, $cabecera, $stringNroCargo, $stringInstitucionalDSC) { // FIXME: Hacer mas dinamico 
  
    $cabecera_array = string2array($cabecera);
    print_r($cabecera_array);
    
    $posNroCargo = array_search($stringNroCargo, $cabecera_array);
    
    $lineaArray = array_map('trim', str_getcsv($stringLinea,',', '"'));
    unset($lineaArray[$posNroCargo]);
    $cabeceraNueva = quitarElementoHeader($cabecera,'nroCargo');   
    $posInstitucionalDSC = array_search($stringInstitucionalDSC, $cabecera_array);
    unset($lineaArray[$posInstitucionalDSC]);
    foreach($lineaArray  as $key=>$value)
    {
        if(!is_numeric($value))
        {
            $insert_array[] = "'$value'";
        }
        else
        {
            $insert_array[] = "$value";
        }
    }
    $aux = implode(',', $insert_array);
    return str_replace("'NULL'",'NULL', $aux);
}

function IndexOf($searchValue,$array)
{
    $i = 0;
    for( $i = 0; $i < count($array); $i++ ) 
        if( $array[$i] == $searchValue )
            return $i;
}

function quitarElementoHeader($cabecera,$stringElemento) {
    $cabeceraInsertar = string2array($cabecera);    
    $cabeceraInsertar= array_del($cabeceraInsertar, $stringElemento);
    return array2string($cabeceraInsertar, ",");
}

function quitarElemento2($stringLinea, $cabecera, $stringElemento) {
    
    $cabecera_array = string2array($cabecera);
    print_r($cabecera_array);
    
    $posElemento = array_search($stringElemento, $cabecera_array);
    
    echo  "<br>";
    echo  "<br>";
    echo  "<br>";
    
    echo $stringLinea;
    echo  "<br>";
    echo $posElemento;
    
        if ($stringElemento=="institucionalDSC") {
        echo "hola" . $posElemento . "ES";
        
    }
    
     $insert_array= array();
     $stringLinea = rtrim($stringLinea);
     $lineaArray = array_map('trim', str_getcsv($stringLinea,',', '"'));
     
     print_r($lineaArray);
     
     unset($lineaArray[$posElemento]);
     foreach($lineaArray as $key=>$value)
     {
        if($value[0]=="'" && $value[strlen($value)-1]=="'"){
            $value = substr($value,1,strlen($value)-2);;
        } 
        if($value[0]=='"' && $value[strlen($value)-1]=='"'){
            $value = substr($value,1,strlen($value)-2);;
        } 
        if(!is_numeric($value))
        {
            if ($value=="") {
                  $insert_array[] = "NULL";
             }
             else {
                if($value[0]=="'" && $value[strlen($value)-1]=="'"){
                  $insert_array[] = "$value";   
                }
                else {
                  $insert_array[] = "'$value'";
                }
             }
        }
        else
        {
            if($value[0]=="'" && $value[strlen($value)-1]=="'"){
                $insert_array[] = substr($value,1,strlen($value)-2);;
            }else {
                $insert_array[] = "$value";
            }
        }
    }
    $aux = implode(',', $insert_array);
     //$aux = "'" . implode("','", $lineaArray) . "'";
    return str_replace("'NULL'",'NULL', $aux);
     //str_replace("'NULL'",'NULL', $aux);    
} // Basada en http://stackoverflow.com/questions/21529383/php-how-to-quote-string-array-values

// Si la tabla personas esta vacia entonces se devuelve un mensaje de error
function chequearTablaPersona($dbh){
    $sth = $dbh->prepare("SELECT * FROM persona");
    if ($sth->rowCount() > 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**************************************************/
/**************************************************/
/****** Fin. Funciones que no se estan usando *****/
/**************************************************/
/**************************************************/

// $needle: substring con el cual se verificara si param1 empieza con el mismo 
// $haystack: string a analizar
function startsWith($haystack, $needle) {
//          search backwards starting from haystack length characters from the end
   return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
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

function quitarElementos($stringLinea, $cabecera, $elementos) { // FIXME: Hacer mas dinamico 
    
    $cabecera_array = string2array($cabecera);
    
    $lineaArray = array_map('trim', str_getcsv($stringLinea,',', '"'));
    
    $largo = count($elementos);
    
    $copiaElementos = clonarArray($elementos);
    for ($i=0; $i<$largo;$i++) {
        $pos = array_search($elementos[$i], $cabecera_array);
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
}

//Considerar lo siguiente:
//Los cargos que empiezan con 6 son no docente
//stringLinea nunca puede ser vacio cuando se invoca a esta funcion
function guardarLineaEnBD($stringLinea,$cabecera,$dbh) {

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
    $posMail = buscarPosMail($cabecera);
    if ($posMail==-1) {
      die("Debe haber un campo mail en la cabecera del archivo");
    }
   
    $institucionalDSC = $lineaArray[$posInstitucionalDSC];
    $ci = $lineaArray[$posCI];
    $mail = $lineaArray[$posMail];
              
    $elementos = ['nroCargo','institucionalDSC']; //Elementos que no se guardan en la tabla personas
    $resultadoAux = quitarElementos($stringLinea, $cabecera, $elementos); // Devuelve una tupla con la linea modificada
  
    $stringLinea = $resultadoAux[0];
    $stringCabecera = $resultadoAux[1];
    
    // Los cargos que empiezan con 6 son no docente
    if (startsWith($nroCargo,"6")) {

       // Insertar en tabla persona        
       $sql = "INSERT INTO persona($stringCabecera) VALUES($stringLinea)";

       $colField = $dbh->prepare($sql);
       $colField->execute();
       
       // Insertar en tabla TAS
       $sql = "INSERT INTO tas(ciTAS) VALUES($ci)";

       $colField = $dbh->prepare($sql);
       $colField->execute();
       
       // Insertamos en tabla InstitucionalDSC
       $sql = "INSERT INTO InstitucionalDSC(Nombre) SELECT * FROM (SELECT '$institucionalDSC') AS tmp WHERE NOT EXISTS (SELECT Nombre from InstitucionalDSC WHERE Nombre='$institucionalDSC')";

       $colField = $dbh->prepare($sql);
       $colField->execute();
       
       // Insertamos en tabla nroCargo
       $sql = "INSERT INTO nroCargo(nroCargo,ciNroCargo) VALUES($nroCargo,$ci)";

       $colField = $dbh->prepare($sql);
       $colField->execute();
       
       // Insertamos en tabla InstitucionalDSC_Persona
       $sql = "INSERT INTO InstitucionalDSC_Persona(nombre,ciPersona,nroCargo) VALUES('$institucionalDSC',$ci,$nroCargo)";

       $colField = $dbh->prepare($sql);
       $colField->execute();
       
    }
    else {
        //  Ejemplo de stringLinea
        //  28741991,Acosta,Larrosa,Martin,Ignacio,tincho977@hotmail.com,555306,T/C Área Técnico Profesional 
        //  Ejemplo de cabecera
        //  ci,apellido,segundoApellido,nombre,segundoNombre,mail,nroCargo,institucionalDSC 
    
        // Insertamos en InstitucionalDSC
        $sql = "INSERT INTO InstitucionalDSC(Nombre) SELECT * FROM (SELECT '$institucionalDSC') AS tmp WHERE NOT EXISTS (SELECT Nombre from InstitucionalDSC WHERE Nombre='$institucionalDSC')";

        $colField = $dbh->prepare($sql);
        $colField->execute();

        // Insertamos en Persona
        $sql = "INSERT INTO persona($stringCabecera) VALUES($stringLinea)";

        $colField = $dbh->prepare($sql);
        $colField->execute();

        // Insertamos en docente    
        $sql = "INSERT INTO docente(ciDocente) VALUES($ci)";
        $colField = $dbh->prepare($sql);
        $colField->execute();

        // Insertamos en tabla nroCargo
        $sql = "INSERT INTO nroCargo(nroCargo,ciNroCargo) VALUES($nroCargo,$ci)";
        
        $colField = $dbh->prepare($sql);
        $colField->execute();
        
        // Insertamos en tabla InstitucionalDSC_Persona
        $sql = "INSERT INTO InstitucionalDSC_Persona(nombre,ciPersona,nroCargo) VALUES('$institucionalDSC',$ci,$nroCargo)";

        $colField = $dbh->prepare($sql);
        $colField->execute();
    }
}

function guardarLineaEnBDMismaPersona($stringLinea,$cabecera,$dbh) {
   // echo $stringLinea;
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
    
    unset($lineaArray[$posNroCargo]);
    
    $stringLinea = fixNullValues($stringLinea);

    // Los cargos que empiezan con 6 son no docente
    if (startsWith($nroCargo,"6")) {

       // Insertamos en tabla InstitucionalDSC  
       $sql = "INSERT INTO InstitucionalDSC(Nombre) SELECT * FROM (SELECT '$institucionalDSC') AS tmp WHERE NOT EXISTS (SELECT Nombre from InstitucionalDSC WHERE Nombre='$institucionalDSC')";

       $colField = $dbh->prepare($sql);
       $colField->execute();
       
       // Insertamos en tabla nroCargo
       $sql = "INSERT IGNORE INTO nroCargo(nroCargo,ciNroCargo) VALUES('$nroCargo',$ci)";

       $colField = $dbh->prepare($sql);
       $colField->execute();
       
    }
    else {
       // Insertar en tabla docente
       //  Ejemplo de stringLinea
       //  28741991,Acosta,Larrosa,Martin,Ignacio,tincho977@hotmail.com,555306,T/C Área Técnico Profesional 
       //  Ejemplo de cabecera
       //  ci,apellido,segundoApellido,nombre,segundoNombre,mail,nroCargo,institucionalDSC 
       
       // Insertamos en InstitucionalDSC
       $sql = "INSERT INTO InstitucionalDSC(Nombre) SELECT * FROM (SELECT '$institucionalDSC') AS tmp WHERE NOT EXISTS (SELECT Nombre from InstitucionalDSC WHERE Nombre='$institucionalDSC')";

       $colField = $dbh->prepare($sql);
       $colField->execute();
       
       // Insertamos en tabla nroCargo
       $sql = "INSERT IGNORE INTO nroCargo(nroCargo,ciNroCargo) VALUES($nroCargo,$ci)";
       //echo $sql;
       $colField = $dbh->prepare($sql);
       $colField->execute();   
    }
}

function guardarLineaEnBDSinMail($stringLinea,$cabecera,$dbh) { //FIXME: repetimos a los sin mail.

    $stringLinea = fixNullValues($stringLinea);
   
    $sql = "INSERT IGNORE INTO personaSinMail($cabecera) VALUES($stringLinea)";

    $colField = $dbh->prepare($sql);
    $colField->execute();

}

// Esta funcion chequea que las celdas de la cabecera coincidan con los nombres de atributos de la base de datos.
function chequearCabecera($cabecera, $dbh) {
  $counter = 0;
  $sql="";
  if (is_array($cabecera) || is_object($cabecera)){
     foreach ($cabecera as $k => $field) {
        $field = str_replace('"', '', $field);
        $sql = "SHOW COLUMNS FROM `persona` LIKE '$field'";
        $colField = $dbh->prepare($sql);
        $colField->execute();
        /* Devolver el numero de filas resultantes */
        $cuenta = $colField->rowCount();
        $exists = ($cuenta == 1) ? TRUE : FALSE;
        if($exists) {
          $counter++;
       }
    }
  }
  // Si es verdadero entonces la totalidad de elementos de la cabecera estan en la base de datos //FIXME: Quitamos a numero de cargo e institucionalDSC de la tabla persona, por eso el -2
  return ($counter == count($cabecera)-2) ? TRUE : FALSE;
}

function chequearPersona($dbh, $ci) {
    $sth = $dbh->prepare("SELECT * FROM persona WHERE ci='$ci'");
    $sth->execute();
    return $sth->rowCount() > 0;
}

function planillaFiltrada($arrayPlanilla,$dbh) {
/*  if (!(chequearTablaPersona($dbh))) {
      die("La tabla persona no esta vacia, para estos casos utilizar la funcionalidad de actualizar"); // FIXME: Lo correcto seria guardar en la bd a la misma persona con sus nuevos datos
  } 
 */
  $ciInsertadas = array(); 
  
  $resultadoRepetidos = array();
  $resultadoSinMails = array();
  $cabecera = $arrayPlanilla[0];
  
  $cabeceraArraySinEspacios = array_map('trim', string2array($cabecera));
 
  if (chequearCabecera($cabeceraArraySinEspacios,$dbh)) {
   //  echo "OK";
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
    
    $cedula = $arrayLinea[$posCedula];
    $mail = $arrayLinea[$posMail];
    
    $encontrado = array_search($cedula, $ciInsertadas); 
    if (!($encontrado!==FALSE)) {
        array_push($ciInsertadas,$cedula);
        if ($mail=="") {
            guardarLineaEnBDSinMail($lineaStringPlanilla, $cabecera, $dbh);
        }
        else { 
            if ((chequearPersona($dbh, $cedula))){
               guardarLineaEnBDMismaPersona($lineaStringPlanilla, $cabecera, $dbh);
            } else {
               guardarLineaEnBD($lineaStringPlanilla, $cabecera, $dbh);
           }
        }
    }
    else {
        if ($mail=="") {
            guardarLineaEnBDSinMail($lineaStringPlanilla, $cabecera, $dbh);            
        }
        else {
            guardarLineaEnBDMismaPersona($lineaStringPlanilla, $cabecera, $dbh);
        }
        $resultadoRepetidos[] = $lineaStringPlanilla; // Los repetidos los podriamos guardar en un CSV
    }
  }
}

$allowedExts = array("csv");
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);

$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
if ((in_array($_FILES['file']['type'],$mimes)) && (in_array($extension, $allowedExts))) {
  if ($_FILES["file"]["error"] > 0) {
    echo "ERROR. Codigo del error: " . $_FILES["file"]["error"] . "<br>";
    echo "<script type= 'text/javascript'>alert('ERROR. Codigo del error: " . $_FILES["file"]["error"] . "<br>');</script>";
  } else {
    if (file_exists("upload/" . $_FILES["file"]["name"])) {
      echo $_FILES["file"]["name"] . " el archivo ya existe. Se borra de upload/" + " <br>";
      unlink("upload/" . $_FILES["file"]["name"]);
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "upload/" . $_FILES["file"]["name"]);
    } else {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "upload/" . $_FILES["file"]["name"]);
    }
    $arrayPlanilla = csv2array("upload/" . $_FILES["file"]["name"]);
    $conexion = new Connection;
    $conn = $conexion->getConnection();
    planillaFiltrada($arrayPlanilla, $conn);
    unlink("upload/" . $_FILES["file"]["name"]);
    
    echo "<script type= 'text/javascript'>alert('Datos insertados');</script>";
  }
} else {
  echo "Por favor suba un archivo en formato CSV, lo puede conseguir exportandolo desde LibreOffice";
  echo "<script type= 'text/javascript'>alert('Por favor suba un archivo en formato CSV, lo puede conseguir exportandolo desde LibreOffice');</script>";
}
echo "<SCRIPT type='text/javascript'>
window.location.replace(\"altas.php\");
</SCRIPT>";


?> 
