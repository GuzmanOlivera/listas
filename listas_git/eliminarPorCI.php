<html><head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script type="text/javascript" src="jquery.min.js"></script>
        <script type="text/javascript" src="bootstrap.min.js"></script>
        <link href="font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="bootstrap.css" rel="stylesheet" type="text/css">
    </head><body>

        <div class="section">
            <div class="container"> 
                <div class="row"> 
                    <div class="col-md-12"> 
                        <h1 class="text-center">Eliminar por CI<br></h1> 
                    </div>
                </div>
                <div class="row"> 
                    <div class="col-md-offset-3 col-md-6"> 
                        <form name="formulario" role="form" action="eliminarPorCIProcesar.php" enctype="multipart/form-data" method="post"> 
                            <div class="form-group"> 
                                <div class="input-group"> 
                                    <input name="ci" type="text" class="form-control" placeholder="Ingresa documento"> 
                                    <span class="input-group-btn">
                                        <input class="btn btn-default" type="submit" value="Confirmar"/>
                                        <br>
                                    </span>
                                </div>
                            </div>
                        </form> 
                    </div>
                </div>
            </div>
        </div>

        <link rel="stylesheet" href="css/style.css">  
         <br>
         <br>
         <br>
         <br>
         <br>
         <div align="center">
            <a class="boton_gestion" href="bajas.php">Volver</a>
            <br>
            <br>
            <a class="boton_gestion" href="logout.php">Logout</a>
         </div>
         
         </body></html>