<?php

?>

<html>
   <head>
	<meta charset="utf-8" />
	<title>Cargar lista de personas</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
   </head>
   <body class="blurBg-false" style="background-color:#EBEBEB">

   <script>
      window.pressed = function() {
         var a = document.getElementById('filen');
         if(a.value == "")
         {
             fileLabel.innerHTML = "No se ha elegido el archivo";
          }
          else
          {
              var theSplit = a.value.split('\\');
              fileLabel.innerHTML = theSplit[theSplit.length-1];
          }
      };
   </script>

      <link rel="stylesheet" href="css/formoid-solid-blue.css" type="text/css" />
         <form action="procesarCSV.php" enctype="multipart/form-data" class="formoid-solid-blue" style="background-color:#FFFFFF;font-size:14px;font-family:Helvetica,Arial,'Roboto',sans-serif;color:#34495E;max-width:480px;min-width:150px" method="post">
            <div class="title">
               <h2>Subir archivo CSV</h2>
            </div>
	       <div class="element-file">
                  <label class="title">
                  </label>
               <div class="item-cont">
                  <label class="large" >
               <div class="button">Elegir archivo</div>
                  <input type="file" class="file_input" name="file"/>
               <div class="file_text">Cargar listado completo...
               </div>
                  <span class="icon-place"></span>
                  </label>
               </div>
               </div>
               <div class="submit">
                  <input type="submit" value="Continuar"/>
               </div>
         </form>
         <link rel="stylesheet" href="css/style.css">  
         <br>
         <br>
         <br>
         <br>
         <br>
         <div align="center">
            <a class="boton_gestion" href="altas.php">Volver</a>
            <br>
            <br>
            <a class="boton_gestion" href="logout.php">Logout</a>
         </div>
</body>
</html>