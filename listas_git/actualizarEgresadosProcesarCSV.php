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


function buscarPosLugarResidencia($linea) {
   $arrayLinea = string2array($linea);
   foreach ($arrayLinea as $k => $v)  {
      if ($v == "lugarResidencia") {
         return $k;
      }
   }
   return -1;
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
                case 'aula':
                    $sql = "INSERT IGNORE INTO aula(nombre) VALUES($valorElemento)";
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    
                    $sql = "INSERT IGNORE INTO docenteDictaAula(ciDocenteAula, nombreAula) VALUES($ci, $valorElemento)"; 
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    break;
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
                case 'dptoAcademico':
                    $sql = "INSERT IGNORE INTO dptoAcademico(nombre) VALUES($valorElemento)";
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    
                    $sql = "INSERT IGNORE INTO dptoAcademico_Docente(ciDocenteDptoAcademico,nombreDptoAcademico) VALUES($ci, $valorElemento)"; 
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
                    
                    $posNroCargo = array_search('nroCargo', $cabeceraArray);
                    $nroCargoVal=updateValue($lineaArray[$posNroCargo]);
                    $sql = "INSERT IGNORE INTO InstitucionalDSC_Persona(nombre,ciPersona,nroCargo) VALUES($valorElemento,$ci,$nroCargoVal)"; 
                    echo $sql;   
                    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    break;
                break;
            }
        }
    }
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
        $value = quitarComillas($value);
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

function insertValue($string) {
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
                    $sql = "DELETE IGNORE FROM regionPersona ciRegionPersona=$ci"; 
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

function actualizarOpcionales($cabecera, $ci, $opcionales, $dbh, $lineaArray) {
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
                    $sql = "INSERT IGNORE INTO aula(nombre) VALUES($valorElemento)";
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  

                    $sql = "INSERT IGNORE INTO docenteDictaAula(nombreAulam,ciDocenteAula) VALUES ($valorElemento,$ci)"; 
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    break;
                case 'region':
                    $sql = "INSERT IGNORE INTO region(nombre) VALUES($valorElemento)";
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    
                    $sql = "INSERT IGNORE regionPersona (nombreRegion,ciRegionPersona) VALUES ($valorElemento, $ci)"; 
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    break;
                case 'dptoAcademico':
                    $sql = "INSERT IGNORE INTO dptoAcademico(nombre) VALUES($valorElemento)";
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    
                    $sql = "INSERT IGNORE INTO dptoAcademico_Docente(ciDocenteDptoAcademico,nombreDptoAcademico) VALUES($ci, $valorElemento)"; 
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    break;
                case 'nroCargo':
                    $sql = "INSERT IGNORE into nroCargo(nroCargo,ciNroCargo) VALUES ($valorElemento,$ci)";
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    break;
                case 'institucionalDSC':
                    $sql = "INSERT IGNORE INTO InstitucionalDSC(nombre) VALUES($valorElemento)";
                    echo $sql;    
                    $colField = $dbh->prepare($sql);
                    $colField->execute();  
                    $posNroCargo = array_search('nroCargo', $cabeceraArray);
                    $nroCargoVal=updateValue($lineaArray[$posNroCargo]);
                    $sql = "INSERT IGNORE INTO InstitucionalDSC_Persona(nombre,ciPersona,nroCargo) VALUES($valorElemento,$ci,$nroCargoVal)"; 
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

//Considerar lo siguiente:
//Los cargos que empiezan con 6 son no docente
//stringLinea nunca puede ser vacio cuando se invoca a esta funcion

function guardarLineaEnBD($stringLinea, $cabecera, $dbh) {

    $lineaArray = array_map('trim', str_getcsv($stringLinea,',', '"'));

    $posCI = buscarPosCI($cabecera);
    if ($posCI==-1) {
      die("Debe haber un campo CI en la cabecera del archivo");
    }
    $ci = $lineaArray[$posCI];

    $stringLinea = fixNullValues($stringLinea);
    $arrayCabecera = string2array($cabecera,',');
        
/*  Para los update usaremos dos arrays en principio y son :

 * $lineaArray: Contiene los valores de los campos que vamos a actualizar
 * $arrayCabecera: Contiene los nombres de los campos que vamos a actualizar
 * Ademas usaremos la variable $ci para filtrar los datos de manera unica
 *  */ 
    $elementos = ['nroCargo','institucionalDSC','lugarResidencia']; //Elementos que no se guardan en la tabla personas pero si en otras
    $resultadoAux = quitarElementos($stringLinea, $cabecera, $elementos); // Devuelve una tupla con la linea modificada
  
    $arrayLineaPersona = $resultadoAux[0]; // valores de los campos para la tabla persona (para el update)
    $arrayCabeceraPersona = $resultadoAux[1]; // nombres de los campos para la tabla persona (para el update)
    
    $stringCondicion = "ci=" . $ci; //condicion del update
  
    $stringColVal = armarColVal($arrayCabeceraPersona, $arrayLineaPersona);
    
    $resultadoAux = quitarElementosDevuelveStrings($stringLinea, $cabecera, $elementos); // Devuelve una tupla con la linea modificada
    $stringLinea = $resultadoAux[0];
    $stringCabecera = $resultadoAux[1];
    
    //Insertamos en la tabla persona por si la persona no se encontraba ingresada en el sistema
    $sql = "INSERT IGNORE INTO persona($stringCabecera) VALUES($stringLinea)";
    
    echo $sql;
    echo "<br>";

    $colField = $dbh->prepare($sql);
    $colField->execute();
    
    // Actualizamos tabla persona en el caso de que si se encuentre en el sistema
    $sql = "UPDATE IGNORE persona SET " . $stringColVal . " WHERE " . $stringCondicion . ";" ;
    echo $sql;
    echo "<br>";
    $colField = $dbh->prepare($sql);
    $colField->execute();
    
    // Insertamos en tabla egresado
    $posLugarResidencia = buscarPosLugarResidencia($cabecera);
    if ($posLugarResidencia==-1) {
        $sql1 = "INSERT IGNORE INTO egresado(ciEgresado) VALUES($ci)";  //FIXME
        
        echo $sql1;    
        $colField = $dbh->prepare($sql1);
        $colField->execute();  
    
    }
    else {
        $lugarResidencia = $lineaArray[$posLugarResidencia];
        $stringResidencia = updateValue($lugarResidencia);
        $sql1 = "INSERT IGNORE INTO egresado(ciEgresado,lugarResidencia) VALUES($ci,$stringResidencia)"; 
        $sql2 = "UPDATE IGNORE egresado SET lugarResidencia=$stringResidencia WHERE ciEgresado=$ci"; 
        
        echo $sql1;    
        $colField = $dbh->prepare($sql1);
        $colField->execute();  
        
        echo $sql2;    
        $colField = $dbh->prepare($sql2);
        $colField->execute();  
    }
    
    // Consideramos que hay elementos opcionales que requieren insertarse en otras tablas
    $opcionales = ['nroCargo','institucionalDSC'];
    eliminarOpcionales($cabecera, $ci, $opcionales, $dbh, $lineaArray);
    actualizarOpcionales($cabecera, $ci, $opcionales, $dbh, $lineaArray);
   
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
    $opcionales = ['lugarResidencia','nroCargo','institucionalDSC'];
    insertarOpcionales($cabecera, $ci, $opcionales, $dbh, $lineaArray);      
       
}

//Considerar lo siguiente:
//Los cargos que empiezan con 6 son no docente
//stringLinea nunca puede ser vacio cuando se invoca a esta funcion

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
  $ciInsertadas = array(); 
 // $arrayPlanillaCopia = clonarArray($arrayPlanilla);

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
  print_r($resultadoRepetidos);
  

 /*
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
  
  */
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
  #    echo $_FILES["file"]["name"] . " el archivo ya existe. Borrarlo de upload/";
      unlink("upload/" . $_FILES["file"]["name"]);
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "upload/" . $_FILES["file"]["name"]);
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
