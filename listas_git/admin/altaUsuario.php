<html><head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script type="text/javascript" src="../jquery.min.js"></script>
        <script type="text/javascript" src="../bootstrap.min.js"></script>
        <link href="../font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="../bootstrap.css" rel="stylesheet" type="text/css">
    </head><body>
        <div class="cover">
            <div class="navbar navbar-default">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-ex-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="usuarios.php"><span>Administración de usuarios</span><br></a>
                    </div>
                    <div class="collapse navbar-collapse" id="navbar-ex-collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li class="active">
                                <a href="#">Alta<br></a>
                            </li>
                            <li>
                                <a href="bajaUsuario.php">Baja</a>
                            </li>
                            <li>
                                <a href="listarUsuarios.php">Listar</a>
                            </li>
                            <li>
                                <a href="modificarUsuario.php">Modificación</a>
                            </li>
                            <li>
                                <a href="../logout.php">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="cover-image"></div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section">
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h1 class="text-center">Ingresar nuevo usuario</h1>
                                        <br>
                                        <br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-6">
                                        <form role="form" action="procesarAltaUsuario.php" method="POST">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input name="usu" type="text" class="form-control" placeholder="Usuario">
                                                    <input name="pss" type="password" class="form-control" placeholder="Contraseña">
                                                    <input name="omg" type="password" class="form-control" placeholder="Palabra clave">
                                                    <input name="admin" type="checkbox" class="form-inline">Admin<br>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-btn">
                                                        
                                                    </span>
                                                </div>
                                            </div>
                                            <button  class="btn btn-success" type="submit">Confirmar</buton>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    

</body></html>