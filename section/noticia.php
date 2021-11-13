<?php include("./section_template/section_header.php");?>


<?php 

//variables obteniendo varlor POST de formulario:

//datos de tabla
$var_noticia_id = (isset($_POST['noticia_id']))?$_POST['noticia_id']:"";
$var_noticia_titulo = (isset($_POST['noticia_titulo']))?$_POST['noticia_titulo']:"";
$var_noticia_imagen = (isset($_FILES['noticia_imagen']['name'])) ? $_FILES['noticia_imagen']['name'] :"";
$var_noticia_fecha = (isset($_POST['noticia_fecha']))?$_POST['noticia_fecha']:"";
$var_noticia_hora = (isset($_POST['noticia_hora']))?$_POST['noticia_hora']:"";
$var_noticia_enlace = (isset($_POST['noticia_enlace']))?$_POST['noticia_enlace']:"";
$var_noticia_autor_id = (isset($_POST['noticia_area_id']))?$_POST['noticia_area_id']:"";
$var_noticia_estado_id = (isset($_POST['noticia_estado_id']))?$_POST['noticia_estado_id']:"";

//opciones de tabla
$var_accion = (isset($_POST['accion']))?$_POST['accion']:"";

include("../config/db.php");

switch($var_accion){
    case "Agregar":
        
        //Preparamos la sentencia sql con INSERT INTO y datos de la base de datos:
        $sentencia_sql= $conexion->prepare("INSERT INTO noticia (
            sql_noticia_titulo,
            sql_noticia_imagen,
            sql_noticia_fecha,
            sql_noticia_hora,
            sql_noticia_enlace,
            sql_noticia_area_id,
            sql_noticia_estado_id) 
            VALUES (
            :param_noticia_titulo,
            :param_noticia_imagen,
            :param_noticia_fecha,
            :param_noticia_hora,
            :param_noticia_enlace,
            :param_noticia_area_id,
            :param_noticia_estado_id );");

        //Mediante bindParam relacionamos los parametros y las variables con contenido POST:
        $sentencia_sql->bindParam(':param_noticia_titulo',$var_noticia_titulo);
        
        //TRATAMIENTO DE IMAGENES//
        $fecha=new DateTime();
        $nombre_archivo=($var_noticia_imagen!="") ? $fecha->getTimestamp()."_".$_FILES["noticia_imagen"]['name'] :"imagen.jpg";
        $temporal_imagen = $_FILES["noticia_imagen"]["tmp_name"];
        if($temporal_imagen!=""){move_uploaded_file($temporal_imagen,"../img/".$nombre_archivo);}

        //Demas parametros:
        $sentencia_sql->bindParam(':param_noticia_imagen',$nombre_archivo);
        $sentencia_sql->bindParam(':param_noticia_fecha',$var_noticia_fecha);
        $sentencia_sql->bindParam(':param_noticia_hora',$var_noticia_hora);
        $sentencia_sql->bindParam(':param_noticia_enlace',$var_noticia_enlace);
        $sentencia_sql->bindParam(':param_noticia_area_id',$var_noticia_autor_id);
        $sentencia_sql->bindParam(':param_noticia_estado_id',$var_noticia_estado_id);

        //Ejecutamos:
        $sentencia_sql->execute();


        header("Location:noticia.php");
        break;

    case "Modificar":

        //Actualizacion mediante UPDATE y datos de la base de datos:
        $sentencia_sql= $conexion->prepare("UPDATE noticia SET
            sql_noticia_titulo=:param_noticia_titulo,
           
            sql_noticia_fecha=:param_noticia_fecha,
            sql_noticia_hora=:param_noticia_hora,
            sql_noticia_enlace=:param_noticia_enlace,
            sql_noticia_area_id=:param_noticia_area_id,
            sql_noticia_estado_id=:param_noticia_estado_id
            WHERE 
            sql_noticia_id=:param_noticia_id;"); 

        $sentencia_sql->bindParam(':param_noticia_id',$var_noticia_id);
        $sentencia_sql->bindParam(':param_noticia_titulo',$var_noticia_titulo);
        
        $sentencia_sql->bindParam(':param_noticia_fecha',$var_noticia_fecha);
        $sentencia_sql->bindParam(':param_noticia_hora',$var_noticia_hora);
        $sentencia_sql->bindParam(':param_noticia_enlace',$var_noticia_enlace);
        $sentencia_sql->bindParam(':param_noticia_area_id',$var_noticia_autor_id);
        $sentencia_sql->bindParam(':param_noticia_estado_id',$var_noticia_estado_id);    
        $sentencia_sql->execute();

        //Modificacion imagen
        if ($var_noticia_imagen!=""){

            //AÑADIMOS EL NUEVO ARCHIVO CON (similar a agregar)
            $fecha=new DateTime();
            $nombre_archivo=($var_noticia_imagen!="") ? $fecha->getTimestamp()."_".$_FILES["noticia_imagen"]['name'] :"imagen.jpg";           
            $temporal_imagen = $_FILES["noticia_imagen"]["tmp_name"];
            move_uploaded_file($temporal_imagen,"../img/".$nombre_archivo); 
            
            //ahora eliminamos el FILE (similar a DELETE)
            $sentencia_sql = $conexion->prepare("SELECT sql_noticia_imagen FROM noticia WHERE sql_noticia_id=:param_noticia_id;");
            $sentencia_sql->bindParam(':param_noticia_id',$var_noticia_id);
            $sentencia_sql->execute();
            $noticia = $sentencia_sql->fetch(PDO::FETCH_LAZY);

            if(isset($noticia["sql_noticia_imagen"]) && ($noticia["sql_noticia_imagen"]!="imagen.jpg")){
                if(file_exists("../img/".$noticia["sql_noticia_imagen"])){
                    unlink("../img/".$noticia["sql_noticia_imagen"]);
                }
            }        

            //ACTUALIZAMOS LOS NUEVOS PARAMETROS
            $sentencia_sql = $conexion->prepare("UPDATE noticia SET sql_noticia_imagen=:param_noticia_imagen  WHERE sql_noticia_id=:param_noticia_id;");
            //IGUAL QUE EN agregar, utilizamos la varibale modificada $nombre_archivo...
            $sentencia_sql->bindParam(':param_noticia_imagen',$nombre_archivo);
            $sentencia_sql->bindParam(':param_noticia_id',$var_noticia_id);
            $sentencia_sql->execute();
        }
        //fin modificacion imagen

        header("Location:noticia.php");    
        break;


    case "Borrar":

        //Borrado de imagenes de /img...
        $sentencia_sql = $conexion->prepare("SELECT sql_noticia_imagen FROM noticia WHERE sql_noticia_id=:param_noticia_id;");
        $sentencia_sql->bindParam(':param_noticia_id',$var_noticia_id);
        $sentencia_sql->execute();
        $noticia = $sentencia_sql->fetch(PDO::FETCH_LAZY);

        if(isset($noticia["sql_noticia_imagen"]) && ($noticia["sql_noticia_imagen"]!="imagen.jpg")){
            if(file_exists("../img/".$noticia["sql_noticia_imagen"])){
                unlink("../img/".$noticia["sql_noticia_imagen"]);
            }

        }
        //FIN borrado de imagen...

        //Borrado de datos en BD mediante DELETE y id:
        $sentencia_sql = $conexion->prepare("DELETE FROM noticia WHERE sql_noticia_id=:param_noticia_id;");
        $sentencia_sql->bindParam(':param_noticia_id',$var_noticia_id);
        $sentencia_sql->execute();
        //echo "Presionado Boton Borrar";
        //header("Location:productos.php");
        header("Location:noticia.php");
        break;

    case "Seleccionar":

        //Seleccionamos informacion mediante INNER JOIN:
        $sentencia_sql= $conexion->prepare("SELECT 
        noticia.sql_noticia_id, 
        noticia.sql_noticia_titulo, 
        noticia.sql_noticia_imagen, 
        noticia.sql_noticia_fecha, 
        noticia.sql_noticia_hora, 
        noticia.sql_noticia_enlace, 
        noticia.sql_noticia_area_id,
        area.sql_area_sigla,  
        noticia.sql_noticia_estado_id, 
        estado.sql_estado_nombre 
        FROM noticia 
        JOIN area ON noticia.sql_noticia_area_id=area.sql_area_id 
        JOIN estado ON noticia.sql_noticia_estado_id=estado.sql_estado_id 
        WHERE sql_noticia_id=:param_noticia_id;");
        
        $sentencia_sql->bindParam(':param_noticia_id',$var_noticia_id);
        $sentencia_sql->execute();


        //FETCH_LAZY CARGA LOS DATOS UNO A UNO:
        $noticia = $sentencia_sql->fetch(PDO::FETCH_LAZY);

        //rellenamos los imputs
        $var_noticia_titulo=$noticia['sql_noticia_titulo'];
        $var_noticia_imagen=$noticia['sql_noticia_imagen'];
        $var_noticia_fecha=$noticia['sql_noticia_fecha'];
        $var_noticia_hora=$noticia['sql_noticia_hora'];
        $var_noticia_enlace=$noticia['sql_noticia_enlace'];
    
        //boton select de area:
        $var_noticia_area_id_2=$noticia['sql_noticia_area_id'];
        $var_area_sigla=$noticia['sql_area_sigla'];
        
        //boton select de estado
        $var_noticia_estado_id_2=$noticia['sql_noticia_estado_id'];
        $var_estado_nombre=$noticia['sql_estado_nombre'];

        break;
    case "Cancelar":
         header("Location:noticia.php");


}



$sentencia_sql= $conexion->prepare("SELECT 
    noticia.sql_noticia_id, 
    noticia.sql_noticia_titulo, 
    noticia.sql_noticia_imagen, 
    noticia.sql_noticia_fecha, 
    noticia.sql_noticia_hora, 
    noticia.sql_noticia_enlace, 
    noticia.sql_noticia_area_id,
    area.sql_area_sigla,  
    noticia.sql_noticia_estado_id, 
    estado.sql_estado_nombre 
    FROM noticia 
    JOIN area ON noticia.sql_noticia_area_id=area.sql_area_id 
    JOIN estado ON noticia.sql_noticia_estado_id=estado.sql_estado_id 
    ORDER BY noticia.sql_noticia_id ASC;");

$sentencia_sql->execute();
$lista_noticias=$sentencia_sql->fetchAll(PDO::FETCH_ASSOC);

if(isset($var_noticia_area_id_2)){

    $sentencia_sql_2= $conexion->prepare("SELECT * FROM area
    WHERE sql_area_id NOT IN ( 
    SELECT sql_area_id FROM area
    WHERE sql_area_id=:param_area_id)");
    $sentencia_sql_2->bindParam(':param_area_id',$var_noticia_area_id_2);
    $sentencia_sql_2->execute();
    $lista_areas=$sentencia_sql_2->fetchAll(PDO::FETCH_ASSOC);

}else{
    $sentencia_sql_2= $conexion->prepare("SELECT * FROM area");
    $sentencia_sql_2->execute();
    $lista_areas=$sentencia_sql_2->fetchAll(PDO::FETCH_ASSOC);
    
}

if(isset($var_noticia_estado_id_2)){

    $sentencia_sql_3= $conexion->prepare("SELECT * FROM estado
    WHERE sql_estado_id NOT IN ( 
    SELECT sql_estado_id FROM estado
    WHERE sql_estado_id=:param_estado_id)");
    $sentencia_sql_3->bindParam(':param_estado_id',$var_noticia_estado_id_2);
    $sentencia_sql_3->execute();
    $lista_estados=$sentencia_sql_3->fetchAll(PDO::FETCH_ASSOC);

}else{
    $sentencia_sql_3= $conexion->prepare("SELECT * FROM estado");
    $sentencia_sql_3->execute();
    $lista_estados=$sentencia_sql_3->fetchAll(PDO::FETCH_ASSOC);  
}



?>


    <div class="col-md-5">

        <div class="card">
            <div class="card-header">
                AGREGAR NUEVA NOTICIA
            </div>

            <div class="card-body">
                <form method="POST" enctype="multipart/form-data"> <!-- propiedad enctype para recibir archivos en el formulario-->

                    <!-- datos generales del formulario -->
                    <div class = "form-group">
                        <input type="hidden" required readonly class="form-control"  value="<?php echo $var_noticia_id; ?>" name="noticia_id" id="noticia_id"  placeholder="ID">
                    </div>

                    <div class = "form-group">
                        <label for="noticia_titulo">Título:</label>
                        <input type="text" required class="form-control" value="<?php echo $var_noticia_titulo; ?>" name="noticia_titulo" id="noticia_titulo"  placeholder="Título">
                    </div>
                    <!-- Imagenes: -->
                    <div class = "form-group">
                        <label for="noticia_imagen">Imagen:</label><br/> 
                        <?php if($var_noticia_imagen!=""){ ?>
                            <img class="img-thumbnail rounded" src="../img/<?php echo $var_noticia_imagen;?>" width="50" alt="">    
                        <?php } ?>
                        <input type="file" class="form-control" name="noticia_imagen" id="noticia_imagen" placeholder="ID">
                    </div>

                     <div class = "form-group">
                        <label for="noticia_fecha">Fecha:</label>
                        <input type="text" required class="form-control" value="<?php echo $var_noticia_fecha; ?>" name="noticia_fecha" id="noticia_fecha"  placeholder="Fecha">
                    </div>

                     <div class = "form-group">
                        <label for="noticia_hora">Hora:</label>
                        <input type="text" required class="form-control" value="<?php echo $var_noticia_hora; ?>" name="noticia_hora" id="noticia_hora"  placeholder="Hora">
                    </div>

                    <div class = "form-group">
                        <label for="noticia_enlace">Enlace:</label>
                        <input type="text" required class="form-control" value="<?php echo $var_noticia_enlace; ?>" name="noticia_enlace" id="noticia_enlace"  placeholder="Hora">
                    </div>

                     <!-- Lista con areas: -->
                    <div class = "form-group">
                        <label for="areas">Área:</label>
                        <select name="noticia_area_id" id="noticia_area_id" required>
                            <?php if(isset($var_noticia_area_id_2)) { ?>
                                <option selected="" value="<?php echo $var_noticia_area_id_2; ?>" ><?php echo $var_area_sigla; ?></option> 
                            <?php } else{?>
                                <option value="" selected disabled hidden>Selecciona una opción</option> 
                            <?php }?>
                            <?php foreach($lista_areas as $area){ ?>
                                <option value="<?php echo $area['sql_area_id']; ?>"> <?php echo $area['sql_area_sigla']; ?></option> 
                            <?php } ?>
                        </select>
                    </div>

                   <!-- Lista con estado: -->
                    <div class = "form-group">
                        <label for="estado">Estado:</label>
                        <select name="noticia_estado_id" id="noticia_estado_id" required>
                            <?php if(isset($var_noticia_estado_id_2)) { ?>
                                <option selected="" value="<?php echo $var_noticia_estado_id_2; ?>" ><?php echo $var_estado_nombre; ?></option> 
                            <?php } else{?>
                                <option value="" selected disabled hidden>Selecciona una opción</option> 
                            <?php }?>
                            <?php foreach($lista_estados as $estado){ ?>
                                <option value="<?php echo $estado['sql_estado_id']; ?>"> <?php echo $estado['sql_estado_nombre']; ?></option> 
                            <?php } ?>
                        </select> 
                    </div><br/> 

                    <div class="btn-group" role="group" aria-label="">
                        <button type="submit" name="accion" <?php echo ($var_accion=="Seleccionar")? "disabled":""?> value= "Agregar" class="btn btn-success">Agregar</button>
                        <button type="submit" name="accion" <?php echo ($var_accion!="Seleccionar")? "disabled":""?> value= "Modificar" class="btn btn-warning">Modificar</button>
                        <button type="submit" name="accion" <?php echo ($var_accion!="Seleccionar")? "disabled":""?> value= "Cancelar" class="btn btn-info">Cancelar</button>
                    </div>
                </form>    
            </div>
        </div>
    </div>  



    <div class="col-md-7">
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    
                    <th>Título</th>
                    <th>Imagen</th>
                    <th>Área</th>
                    <th>Estado</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>

            <?php foreach($lista_noticias as $noti) { ?>
                <tr>

                    <td><?php echo $noti['sql_noticia_titulo'] ?> </td>
                    <td><img class="img-thumbnail rounded" src="../img/<?php echo $noti['sql_noticia_imagen'];?>" width="100" alt=""></td>
                    <td><?php echo $noti['sql_area_sigla'] ?></td>
                    <td><?php echo $noti['sql_estado_nombre'] ?></td>
                   

                    <td>

                    <form method="post">
                        <input type="hidden" name="noticia_id" id="noticia_id" value="<?php echo $noti['sql_noticia_id'] ?>"/>
                        
                        <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary"/>

                        <input type="submit" name="accion" value="Borrar" class="btn btn-danger"/>

                    </form>


                    </td>
                
                </tr>
            <?php  } ?>
            </tbody>
        </table>



    </div>


<?php include("./section_template/section_footer.php");?>