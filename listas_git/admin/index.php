<?php

include_once "../conexion.php";

$conexion = new Connection;
$conn = $conexion->getConnection();

// PHP SESSION //
session_start();

//If the POST var "login_submit" exists (our submit button), then we can
//assume that the user has submitted the login form.
if(isset($_POST['login_submit'])){
    if(empty($_POST['username']) || empty($_POST['password'])){
        die('Debe ingresar usuario y clave'); //FIXME: Add button to refresh page so that the user can fill the form again
    }
    $errores = FALSE;
    $sql = "SELECT * FROM usuarios WHERE username = :name";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $_POST['username']);
    $stmt->execute();

    if($stmt->rowCount() > 0){
        $resultado = $stmt->fetchAll();
        $clave = $resultado[0]['password'];
        if (password_verify($_POST['password'], $clave)) {
          // echo "Correcto!";
           $_SESSION['usuario'] = $_POST['username'];
           //include "control_tiempo.php";
           // if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
           //    // last request was more than 30 minutes ago
           //    session_unset();     // unset $_SESSION variable for the run-time 
           //    session_destroy();   // destroy session data in storage
           // }
           // if (!isset($_SESSION['CREATED'])) {
           //    $_SESSION['CREATED'] = time();
           // } else if (time() - $_SESSION['CREATED'] > 1800) {
           //    // session started more than 30 minutes ago
           //    session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
           //    $_SESSION['CREATED'] = time();  // update creation time
           // }
           // $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
           header("Location: usuarios.php");
        }
        else {
           $errores = TRUE;
        }
    } else {
        if ($errores) {
            echo "Usuario o clave incorrectas";
        }
        else {
            echo "Usuario o clave incorrectas";
        }
    }
    //Retrieve the field values from our login form.
//    $username = !empty($_POST['username']) ? trim($_POST['username']) : null;
  //  $passwordAttempt = !empty($_POST['password']) ? trim($_POST['password']) : null;
//      $sql = "SELECT * FROM usuarios WHERE username =?";
  //    $stmt = $conn->prepare($sql);
    //  $result = $stmt->execute([$_POST['username']]);
     // $users = $result->fetchAll();
//      if (isset($users[0])) {
  //        if (password_verify($_POST['password'], $users[0]->password) {
        // valid login
      //       echo "Correcto";
    //      } else {
          // invalid password 
     //       die('Error 1');
     //     }
    //  } else {
     //       die('Error 2');
         // invalid username
    //  }
}

?>


<html >
<head>
  <meta charset="UTF-8">
  <title>Sistema de Gestión de Personal</title>
  <link rel='stylesheet prefetch' href='../css/bootstrap.min.css'>
      <link rel="stylesheet" href="../css/style.css">  
</head>

<body>
    <div class="wrapper">
    <form class="form-signin" action="" method="post"> 
      <h2 class="form-signin-heading">ADMIN</h2>
      <input type="text" class="form-control" name="username" placeholder="Usuario" required="" autofocus="" />
      <input type="password" class="form-control" name="password" placeholder="Clave" required=""/>      
      <button name="login_submit" class="btn btn-lg btn-primary btn-block" type="submit">Confirmar</button>   
    </form>
  </div>  
</body>
</html>
