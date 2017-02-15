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

function buscarPosDptoAcademico($linea) {
   $arrayLinea = string2array($linea);
   foreach ($arrayLinea as $k => $v)  {
      if ($v == "DptoAcademico") {
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
    $resultado = array($insert_array,$cabecera_array);
    return $resultado;
    //return str_replace("'NULL'",'NULL', $aux);
}

function quitarElementosDevuelveStrings($stringLinea, $cabecera, $elementos) { // FIXME: Hacer mas dinamico 
    
    $cabecera_array = string2array($cabecera);
    $lineaArray = array_map('trim', str_getcsv($stringLinea,',', '"'));
    
    $largo = count($elementos);
    
    $copiaElementos = clonarArray($elementos);
    for ($i=0; $i<$largo;$i++) {
        //$pos = array_search($elementos[$i], $lineaArray);
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
    //return str_replace("'NULL'",'NULL', $aux);
}

function updateValue($string) {
    if ($string==""){
       return 'NULL';         
    }
    elseif(!is_numeric($string)) {
       return "'$string'";
    }
    return $string;
}
function eliminarOpcionales($cabecera, $ci, $opcionales, $dbh, $lineaArray) {
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
            $valorElemento = updateValue($lineaArray[$pos]);
            switch ($opcionales[$i]) {
                case 'aula':
                    $sql = "DELETE IGNORE FROM docenteDictaAula WHERE ciDocenteAula=$ci"; 
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    break;
                case 'region':
                    $sql = "DELETE IGNORE FROM regionPersona WHERE ciRegionPersona=$ci"; 
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    break;
                case 'dptoAcademico':
                    $sql = "DELETE IGNORE FROM dptoAcademico_Docente WHERE ciDocenteDptoAcademico=$ci"; 
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    break;
                case 'nroCargo':
                    $sql = "DELETE IGNORE FROM nroCargo WHERE ciNroCargo=$ci";
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    break;
                case 'institucionalDSC':  
                    $sql = "DELETE IGNORE FROM InstitucionalDSC_Persona WHERE ciPersona=$ci";
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    break;
                break;
            }
        }
    }
}


// Quita comillas al principio y al final
// Primero dobles y luego simples
function quitarComillas($string) {
    $largo = strlen($string);
    $condicion = ($string[$largo-1]=='"' && $string[0]=='"');
    while($condicion) {
        $string = substr($string, 1, $largo-2); //Quitar comillas dobles al principio y al final
        $largo = strlen($string);
        $condicion = ($string[$largo-1]=='"' && $string[0]=='"');
    }
    $largo = strlen($string);
    $condicion = ($string[$largo-1]=="'" && $string[0]=="'");
    while($condicion) {
        $string = substr($string, 1, $largo-2); //Quitar comillas simples al principio y al final
        $largo = strlen($string);
        $condicion = ($string[$largo-1]=="'" && $string[0]=="'");
    }
    return $string;
}

function armarColVal($arrayCabecera, $lineaArray) {
    
    $stringColVal = "";
    for($i = 0; $i < count($arrayCabecera); $i++) {
        $lineaArray[$i] = quitarComillas($lineaArray[$i]);
        if ($i == count($arrayCabecera)-1) {

            if (is_numeric($lineaArray[$i])) {
                $stringColVal = $stringColVal . $arrayCabecera[$i] . "=" . "$lineaArray[$i]";  
            }
            elseif ($lineaArray[$i]=="NULL") {
                $stringColVal = $stringColVal . $arrayCabecera[$i] . "=" . $lineaArray[$i];  
            }
            else {
                $stringColVal = $stringColVal . $arrayCabecera[$i] . "=" . "'" .  "$lineaArray[$i]" . "'";  
            }
        }
        else {
                if (is_numeric($lineaArray[$i])){
                   $stringColVal = $stringColVal  . $arrayCabecera[$i] . "=" . "$lineaArray[$i]" . ",";
                }
                elseif ($lineaArray[$i]=="NULL") {
                   $stringColVal = $stringColVal  . $arrayCabecera[$i] . "=" . $lineaArray[$i] . ",";
                }
                else
                {
                   $stringColVal = $stringColVal . $arrayCabecera[$i] . "=" . "'" . "$lineaArray[$i]" . "'" . ",";
                }
        }
    }
    return $stringColVal;
}

function buscarPersonaSinMail($dbh,$ci){ //Busca si la persona estaba ingresada como sinMail, en caso afirmativo devuelve true sino false
    $sth = $dbh->prepare("SELECT * FROM personaSinMail WHERE ci='$ci'");
    $sth->execute();
    return $sth->rowCount() > 0;
}

function borrarPersonaSinMail($dbh,$ci) { //Borra a la persona de la tabla sinMail
    $sql = "DELETE FROM personaSinMail where ci=$ci";
    $colField = $dbh->prepare($sql);
    $colField->execute();
}

//Considerar lo siguiente:
//Los cargos que empiezan con 6 son no docente
//stringLinea nunca puede ser vacio cuando se invoca a esta funcion
function guardarLineaEnBD($stringLinea, $cabecera, $dbh) {
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
    
    $arrayCabecera = string2array($cabecera,',');
        
/*  Para los update usaremos dos arrays en principio y son :

 * $lineaArray: Contiene los valores de los campos que vamos a actualizar
 * $arrayCabecera: Contiene los nombres de los campos que vamos a actualizar
 * Ademas usaremos la variable $ci para filtrar los datos de manera unica
 *  */ 
    $elementos = ['nroCargo','institucionalDSC']; //Elementos que no se guardan en la tabla personas pero si en otras
    $resultadoAux = quitarElementos($stringLinea, $cabecera, $elementos); // Devuelve una tupla con la linea modificada
  
    $arrayLineaPersona = $resultadoAux[0]; // valores de los campos para la tabla persona (para el update)
    $arrayCabeceraPersona = $resultadoAux[1]; // nombres de los campos para la tabla persona (para el update)
    
    $stringCondicion = "ci=" . $ci; //condicion del update
  
    $stringColVal = armarColVal($arrayCabeceraPersona, $arrayLineaPersona);
    // Los cargos que empiezan con 6 son no docente
    
    $opcionales = ['nroCargo','institucionalDSC'];
    eliminarOpcionales($cabecera, $ci, $opcionales, $dbh, $lineaArray);
    
    $elementos = ['nroCargo','institucionalDSC']; //Elementos que no se guardan en la tabla personas
    $resultadoAux = quitarElementosDevuelveStrings($stringLinea, $cabecera, $elementos); // Devuelve una tupla con la linea modificada
  
    $stringLinea = $resultadoAux[0];
    $stringCabecera = $resultadoAux[1];
    
    if (startsWith($nroCargo,"6")) {

       // Insertamos en tabla InstitucionalDSC
       // Si no existe lo intertamos
       $sql = "INSERT IGNORE INTO InstitucionalDSC(Nombre) SELECT * FROM (SELECT '$institucionalDSC') AS tmp WHERE NOT EXISTS (SELECT Nombre from InstitucionalDSC WHERE Nombre='$institucionalDSC')";
       $colField = $dbh->prepare($sql);
       $colField->execute();
       
       // Actualizamos tabla nroCargo
       $sql = "INSERT IGNORE nroCargo(nroCargo,ciNroCargo) VALUES($nroCargo,$ci);";
       $colField = $dbh->prepare($sql);
       $colField->execute();

       // Insertamos en la tabla persona
       $sql = "INSERT IGNORE INTO persona($stringCabecera) VALUES($stringLinea)";
       echo $sql;
       $colField = $dbh->prepare($sql);
       $colField->execute();
       
       // Actualizamos tabla persona
       $sql = "UPDATE IGNORE persona SET " . $stringColVal . " WHERE " . $stringCondicion . ";" ;
       $colField = $dbh->prepare($sql);
       $colField->execute();
       
       // Insertamos en tabla InstitucionalDSC_Persona
       $sql = "INSERT IGNORE INTO InstitucionalDSC_Persona(nombre,ciPersona,nroCargo) VALUES('$institucionalDSC',$ci,$nroCargo)";

       $colField = $dbh->prepare($sql);
       $colField->execute();
       
    }
    else {
  
       //  Ejemplo de stringLinea
       //  28741991,Acosta,Larrosa,Martin,Ignacio,tincho977@hotmail.com,555306,T/C Área Técnico Profesional 
       //  Ejemplo de cabecera
       //  ci,apellido,segundoApellido,nombre,segundoNombre,mail,nroCargo,institucionalDSC 
        
       // Insertamos en InstitucionalDSC por si se agrega uno nuevo
       $sql = "INSERT IGNORE INTO InstitucionalDSC(Nombre) SELECT * FROM (SELECT '$institucionalDSC') AS tmp WHERE NOT EXISTS (SELECT Nombre from InstitucionalDSC WHERE Nombre='$institucionalDSC')";
       $colField = $dbh->prepare($sql);
       $colField->execute();

       // Actualizamos tabla nroCargo
       $sql = "INSERT IGNORE nroCargo(nroCargo,ciNroCargo) VALUES ($nroCargo,$ci);";
      
       $colField = $dbh->prepare($sql);
       $colField->execute();
      
        
       $sql = "INSERT IGNORE INTO persona($stringCabecera) VALUES($stringLinea)";
       $colField = $dbh->prepare($sql);
       $colField->execute();

       // Actualizamos tabla persona
       $sql = "UPDATE IGNORE persona SET " . $stringColVal . " WHERE " . $stringCondicion . ";" ;
       $colField = $dbh->prepare($sql);
       $colField->execute();
                 
       // Actualizamos la tabla docente
       $sql = "INSERT IGNORE docente(ciDocente) VALUES($ci);";
       $colField = $dbh->prepare($sql);
       $colField->execute();
        
       // Insertamos en tabla InstitucionalDSC_Persona
       $sql = "INSERT IGNORE INTO InstitucionalDSC_Persona(nombre,ciPersona,nroCargo) VALUES('$institucionalDSC',$ci,$nroCargo)";

       $colField = $dbh->prepare($sql);
       $colField->execute();

       
    }
    // Si la persona sin mail esta en la base de datos entonces la borramos
    if (buscarPersonaSinMail($dbh, $ci)) {
        borrarPersonaSinMail($dbh, $ci);
    }
}

function guardarLineaEnBDMismaPersona($stringLinea,$cabecera,$dbh) {

    // En esta funcion hacemos lo mismo que en guardarLineaEnBD($stringLinea, $cabecera, $dbh) exceptuando que no se hacen operaciones sobre la tabla persona
    
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
       // Si no existe lo intertamos
       $sql = "INSERT IGNORE INTO InstitucionalDSC(Nombre) SELECT * FROM (SELECT '$institucionalDSC') AS tmp WHERE NOT EXISTS (SELECT Nombre from InstitucionalDSC WHERE Nombre='$institucionalDSC')";
       echo $sql;
       echo "<br>";
       $colField = $dbh->prepare($sql);
       $colField->execute();
       
       // Insertamos en tabla InstitucionalDSC_Persona
       $sql = "INSERT IGNORE INTO InstitucionalDSC_Persona(nombre,ciPersona,nroCargo) VALUES('$institucionalDSC',$ci,$nroCargo)";

       $colField = $dbh->prepare($sql);
       $colField->execute();
       
       // Actualizamos en tabla InstitucionalDSC_Persona
       $sql = "UPDATE IGNORE InstitucionalDSC_Persona SET nombre='$institucionalDSC' where ciPersona=$ci";
       echo $sql;
       echo "<br>";

       $colField = $dbh->prepare($sql);
       $colField->execute();
       
       // Actualizamos tabla nroCargo
       $sql = "UPDATE IGNORE nroCargo SET nroCargo=$nroCargo where ciNroCargo=$ci;";
       echo $sql;
       echo "<br>";

       $colField = $dbh->prepare($sql);
       $colField->execute();
       
    }
    else {
       //  Ejemplo de stringLinea
        //  28741991,Acosta,Larrosa,Martin,Ignacio,tincho977@hotmail.com,555306,T/C Área Técnico Profesional 
        //  Ejemplo de cabecera
        //  ci,apellido,segundoApellido,nombre,segundoNombre,mail,nroCargo,institucionalDSC 
        
        // Insertamos en InstitucionalDSC por si se agrega uno nuevo
        $sql = "INSERT IGNORE INTO InstitucionalDSC(Nombre) SELECT * FROM (SELECT '$institucionalDSC') AS tmp WHERE NOT EXISTS (SELECT Nombre from InstitucionalDSC WHERE Nombre='$institucionalDSC')";
        echo $sql;
        echo "<br>";
        $colField = $dbh->prepare($sql);
        $colField->execute();

        // Insertamos en tabla InstitucionalDSC_Persona
        $sql = "INSERT IGNORE INTO InstitucionalDSC_Persona(nombre,ciPersona,nroCargo) VALUES('$institucionalDSC',$ci,$nroCargo)";

        $colField = $dbh->prepare($sql);
        $colField->execute();

        
        
        // Actualizamos en tabla InstitucionalDSC_Persona
        $sql = "UPDATE IGNORE InstitucionalDSC_Persona SET nombre='$institucionalDSC' where ciPersona=$ci";
        
        // Actualizamos tabla nroCargo
        $sql = "UPDATE IGNORE nroCargo SET nroCargo=$nroCargo where ciNroCargo=$ci;";

        $colField = $dbh->prepare($sql);
        $colField->execute();
        
        // Actualizar tabla persona                
        // Actualizamos la tabla docente
        // El update en la tabla docente no seria necesario ya que el update de la constraint de la fk de la tabla persona funciona con oncascade
        
       
       }
}

function guardarLineaEnBDSinMail($stringLinea,$cabecera,$dbh) {
    $stringLinea = fixNullValues($stringLinea);
    $sql = "INSERT IGNORE INTO personaSinMail($cabecera) VALUES($stringLinea)"; //FIXME: ver que forma hay de actualizar a los sinMail.
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
  return ($counter == count($cabecera)-2) ? TRUE : FALSE; //FIX: hacer dinamico. Le restamos dos porque sacamos nroCargo y institucionalDSC
}

function planillaFiltrada($arrayPlanilla,$dbh) {
  $ciInsertadas = array(); 

  $resultadoRepetidos = array();
  $resultadoSinMails = array();
  $cabecera = $arrayPlanilla[0];

  $cabeceraArraySinEspacios = array_map('trim', string2array($cabecera));
 /*
  if (chequearCabecera($cabeceraArraySinEspacios,$dbh)) { //FIXME: Chequear cabecera
     echo "OK";
  }
  else {
     die("Debe subir una planilla con los campos correctos, leer la documentacion apropiada");
  }
*/
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
    
    echo "<script type= 'text/javascript'>alert('Datos actualizados');</script>";
  }
} else {
  echo "Por favor suba un archivo en formato CSV, lo puede conseguir exportandolo desde LibreOffice";
  echo "<script type= 'text/javascript'>alert('Por favor suba un archivo en formato CSV, lo puede conseguir exportandolo desde LibreOffice');</script>";
}
/*echo "<SCRIPT type='text/javascript'>
window.location.replace(\"modificaciones.php\");
</SCRIPT>"; */
?> 
