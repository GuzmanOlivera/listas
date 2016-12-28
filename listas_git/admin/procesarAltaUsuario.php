<html>
    <head>
       
    </head>
    <body>

<?php

include_once "../conexion.php";

$conexion = new Connection;
$conn = $conexion->getConnection();

if(empty($_POST['usu']) || empty($_POST['pss'])){
        die('Debe ingresar usuario y clave');
}
else {
     
$servername = "localhost";
$username = "root";
$password = $_POST['omg'];
$dbname = "iseflistas";

if (!defined('PDO::ATTR_DRIVER_NAME')) {
   echo 'PDO unavailable';
}

try {
       $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
       // set the PDO error mode to exception
       $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

       // prepare sql and bind parameters

       $usue = $_POST['usu']; //Aca ponemos el username del usuario a agregar
       $psse = $_POST['pss']; //Aca ponemos el password del usuario a agregar
       $hash = password_hash($psse, PASSWORD_DEFAULT);

       $stmt = $conn->prepare("insert into usuarios set username=?, password=?");
              
       if ($stmt->execute([$usue, $hash])) {
        echo "<script type= 'text/javascript'>alert('New Record Inserted Successfully');</script>";
      echo "<SCRIPT type='text/javascript'>
        window.location.replace(\"altaUsuario.php\");
        </SCRIPT>";
       }
        else {
          echo "<script type= 'text/javascript'>alert('Data not successfully Inserted.');</script>";
      echo "<SCRIPT type='text/javascript'>
        window.location.replace(\"altaUsuario.php\");
        </SCRIPT>";
       }
    }
    catch(PDOException $e)
    {
       $mensaje = $e->getMessage();
       echo $mensaje;
       echo "<script type= 'text/javascript'>alert('Data not successfully Inserted.');</script>";
       echo "<script type= 'text/javascript'>alert('Error: '. $mensaje .' ');</script>";
       
       echo "<SCRIPT type='text/javascript'>
        window.location.replace(\"altaUsuario.php\");
        </SCRIPT>";
    }
    $conn = null;
}
?>

</body>
<html>
    

