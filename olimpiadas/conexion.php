<?php 



    $Server="localhost";
    $User="root";
    $Pass="";
    $Base="agencia_viaje";

    $conexion=mysqli_connect($Server, $User, $Pass, $Base)
     or die ("no se pudo conectar");
?>