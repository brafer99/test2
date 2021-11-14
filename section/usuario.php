<?php include("./section_template/section_header.php");?>


<?php 

//variables obteniendo varlor POST de formulario:

//datos de tabla
$var_usuario_id = (isset($_POST['usuario_id']))?$_POST['usuario_id']:"";
$var_usuario_email = (isset($_POST['usuario_email']))?$_POST['usuario_email']:"";
$var_usuario_pass = (isset($_POST['usuario_pass']))?$_POST['usuario_pass']:"";
$var_usuario_rol_id = (isset($_POST['usuario_rol_id']))?$_POST['usuario_rol_id']:"";


//opciones de tabla
$var_accion = (isset($_POST['accion']))?$_POST['accion']:"";

include("../config/db.php");

switch($var_accion){
    case "Agregar":
        
        //Preparamos la sentencia sql con INSERT INTO y datos de la base de datos:

        $sentencia_sql= $conexion->prepare("SELECT * FROM usuario");
        $sentencia_sql->execute();
        $lista_validaciones=$sentencia_sql->fetchAll(PDO::FETCH_ASSOC);
        $validacion=true;
        foreach($lista_validaciones as $vali){
            if($var_usuario_email==$vali['sql_usuario_email']){
                $validacion=false;
            }

        }

        if($validacion==true){

        $sentencia_sql= $conexion->prepare("INSERT INTO usuario (
            sql_usuario_email,
            sql_usuario_pass,
            sql_usuario_rol_id) 
            VALUES (
            :param_usuario_email,
            :param_usuario_pass,
            :param_usuario_rol_id );");

        //Mediante bindParam relacionamos los parametros y las variables con contenido POST:
        $sentencia_sql->bindParam(':param_usuario_email',$var_usuario_email);
        $sentencia_sql->bindParam(':param_usuario_pass',$var_usuario_pass);
        $sentencia_sql->bindParam(':param_usuario_rol_id',$var_usuario_rol_id);
        $sentencia_sql->execute();
        header("Location:usuario.php");
        }

        break;

    case "Modificar":

        //validacion en la modificacion de email:
        if(isset($var_usuario_email)){
            $sentencia_sql_3= $conexion->prepare("SELECT * FROM usuario
            WHERE sql_usuario_id NOT IN ( 
            SELECT sql_usuario_id FROM usuario
            WHERE sql_usuario_id=:param_usuario_id)");
            $sentencia_sql_3->bindParam(':param_usuario_id',$var_usuario_id);
            $sentencia_sql_3->execute();
            $lista_vali_modi=$sentencia_sql_3->fetchAll(PDO::FETCH_ASSOC);
            $validacion_modi=true;
            foreach($lista_vali_modi as $modi){
                if($var_usuario_email==$modi['sql_usuario_email']){
                    $validacion_modi=false;
                }
            }
        }

        if($validacion_modi==true){
        //Actualizacion mediante UPDATE y datos de la base de datos:
        $sentencia_sql= $conexion->prepare("UPDATE usuario SET

            sql_usuario_email=:param_usuario_email,
            sql_usuario_pass=:param_usuario_pass,
            sql_usuario_rol_id=:param_usuario_rol_id

            WHERE 
            sql_usuario_id=:param_usuario_id;"); 

        $sentencia_sql->bindParam(':param_usuario_id',$var_usuario_id);
        $sentencia_sql->bindParam(':param_usuario_email',$var_usuario_email);
        $sentencia_sql->bindParam(':param_usuario_pass',$var_usuario_pass);
        $sentencia_sql->bindParam(':param_usuario_rol_id',$var_usuario_rol_id);

    
        $sentencia_sql->execute();

        //fin modificacion imagen

        header("Location:usuario.php");
        }else{
            
            $sentencia_sql= $conexion->prepare("SELECT 
            usuario.sql_usuario_id, 
            usuario.sql_usuario_email, 
            usuario.sql_usuario_pass, 
            usuario.sql_usuario_rol_id,
            rol.sql_rol_nombre
            FROM usuario
            JOIN rol ON usuario.sql_usuario_rol_id=rol.sql_rol_id 
            WHERE sql_usuario_id=:param_usuario_id;");
            $sentencia_sql->bindParam(':param_usuario_id',$var_usuario_id);
            $sentencia_sql->execute();
            //FETCH_LAZY CARGA LOS DATOS UNO A UNO:
            $usuario = $sentencia_sql->fetch(PDO::FETCH_LAZY);            
            $var_usuario_rol_id_2=$usuario['sql_usuario_rol_id'];
            $var_rol_nombre=$usuario['sql_rol_nombre'];   
        }    
        break;


    case "Borrar":

        //que pasa si un usuario borra todos los usuarios...
        $sentencia_sql= $conexion->prepare("SELECT * FROM usuario");
        $sentencia_sql->execute();
        $lista_usuarios=$sentencia_sql->fetchAll(PDO::FETCH_ASSOC); 
        $cantidad_usuario=count($lista_usuarios);

        $validacion_borrar=true;
        
        if($cantidad_usuario==1){
            $validacion_borrar=false;
        }

        if($validacion_borrar==true){
        //Borrado de datos en BD mediante DELETE y id:
        $sentencia_sql = $conexion->prepare("DELETE FROM usuario WHERE sql_usuario_id=:param_usuario_id;");
        $sentencia_sql->bindParam(':param_usuario_id',$var_usuario_id);
        $sentencia_sql->execute();
        //echo "Presionado Boton Borrar";
        //header("Location:productos.php");
        header("Location:usuario.php");
        } 
        break;

    case "Seleccionar":



        //Seleccionamos informacion mediante INNER JOIN:
        $sentencia_sql= $conexion->prepare("SELECT 
        usuario.sql_usuario_id, 
        usuario.sql_usuario_email, 
        usuario.sql_usuario_pass, 
        usuario.sql_usuario_rol_id,
        rol.sql_rol_nombre

        FROM usuario
        JOIN rol ON usuario.sql_usuario_rol_id=rol.sql_rol_id 

        WHERE sql_usuario_id=:param_usuario_id;");
        
        $sentencia_sql->bindParam(':param_usuario_id',$var_usuario_id);
        $sentencia_sql->execute();
        //FETCH_LAZY CARGA LOS DATOS UNO A UNO:
        $usuario = $sentencia_sql->fetch(PDO::FETCH_LAZY);

        //rellenamos los imputs
        $var_usuario_email=$usuario['sql_usuario_email'];
        $var_usuario_pass=$usuario['sql_usuario_pass'];

        //boton select de rol:
        $var_usuario_rol_id_2=$usuario['sql_usuario_rol_id'];
        $var_rol_nombre=$usuario['sql_rol_nombre'];
        
        break;
    case "Cancelar":
         header("Location:usuario.php");


}

$sentencia_sql= $conexion->prepare("SELECT 
    usuario.sql_usuario_id, 
    usuario.sql_usuario_email, 
    usuario.sql_usuario_pass,
    usuario.sql_usuario_rol_id,
    rol.sql_rol_nombre

    FROM usuario 
    JOIN rol ON usuario.sql_usuario_rol_id=rol.sql_rol_id 
    ORDER BY usuario.sql_usuario_id ASC;");

$sentencia_sql->execute();
$lista_usuarios=$sentencia_sql->fetchAll(PDO::FETCH_ASSOC);

if(isset($var_usuario_rol_id_2)){

    $sentencia_sql_2= $conexion->prepare("SELECT * FROM rol
    WHERE sql_rol_id NOT IN ( 
    SELECT sql_rol_id FROM rol
    WHERE sql_rol_id=:param_rol_id)");
    $sentencia_sql_2->bindParam(':param_rol_id',$var_usuario_rol_id_2);
    $sentencia_sql_2->execute();
    $lista_roles=$sentencia_sql_2->fetchAll(PDO::FETCH_ASSOC);

}else{
    $sentencia_sql_2= $conexion->prepare("SELECT * FROM rol");
    $sentencia_sql_2->execute();
    $lista_roles=$sentencia_sql_2->fetchAll(PDO::FETCH_ASSOC);    
}

?>


    <div class="col-md-5">

        <div class="card">
            <div class="card-header">
                AGREGAR NUEVO USUARIO
            </div>

            <div class="card-body">
                <form method="POST" enctype="multipart/form-data"> <!-- propiedad enctype para recibir archivos en el formulario-->

                    <!-- datos generales del formulario -->
                    <div class = "form-group">
                        <input type="hidden" required readonly class="form-control"  value="<?php echo $var_usuario_id; ?>" name="usuario_id" id="usuario_id"  placeholder="ID">
                    </div>

                    <?php if(isset($validacion)){
                        if($validacion==false){
                         ?>
                        <div class="alert alert-danger">
                        <strong>Email existente, digite un nuevo Email</strong>
                        </div>

                   <?php }} ?>
                   <?php if(isset($validacion_modi)){
                        if($validacion_modi==false){
                         ?>
                        <div class="alert alert-danger">
                        <strong>Otro usuario tiene ese email, digite un nuevo email</strong>
                        </div>

                   <?php }} ?>

                    <div class = "form-group">
                        <label for="usuario_email">Émail:</label>
                        <input type="email" required class="form-control" value="<?php echo $var_usuario_email; ?>" name="usuario_email" id="usuario_email"  placeholder="Email">
                    </div>



                    <div class = "form-group">
                        <label>Contraseña:</label>
                        <div class="form-row">
                            <div class="col">
                                <input required class="form-control" value="<?php echo $var_usuario_pass; ?>" type="password" name="usuario_pass" id="contraseña" placeholder="Contraseña">
                            </div>

                            <div class="col">
                                <button class="btn btn-primary" type="button" id="boton">Mostrar Contraseña</button>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        var boton = document.getElementById('boton');
                        var input = document.getElementById('contraseña');
                        boton.addEventListener('click',mostrarContraseña);
                        function mostrarContraseña(){
                            if(input.type == "password"){
                                input.type = "text";
                            }else{
                                input.type = "password";
                            }
                        }
                        </script>

                     <!-- Lista con areas: -->
                    <div class = "form-group">
                        <label for="roles">Asignar Rol:</label>
                        <select name="usuario_rol_id" id="usuario_rol_id" required>
                            <?php if(isset($var_usuario_rol_id_2)) { ?>
                                <option selected="" value="<?php echo $var_usuario_rol_id_2; ?>" ><?php echo $var_rol_nombre; ?></option> 
                            <?php } else{?>
                                <option value="" selected disabled hidden>Selecciona una opción</option> 
                            <?php }?>
                            <?php foreach($lista_roles as $rol){ ?>
                                <option value="<?php echo $rol['sql_rol_id']; ?>"> <?php echo $rol['sql_rol_nombre']; ?></option> 
                            <?php } ?>
                        </select>
                    </div>


                    <div class="btn-group" role="group" aria-label="">


                        <button type="submit" name="accion" 

                        <?php if(isset($validacion_modi)){
                            if($validacion_modi==false){
                                echo "disabled";
                            }
                        }else{
                            if($var_accion=="Seleccionar"){
                                echo "disabled";
                            } 
                        }
                        ?>
                        value= "Agregar" class="btn btn-success">Agregar</button>
                        
                        <button type="submit" name="accion" 
                        <?php if(isset($validacion_modi)){
                            if($validacion_modi==false){
                                echo "";
                            }
                        }else{
                            if($var_accion!="Seleccionar"){
                                echo "disabled";
                            }
                        } 
                        ?> value= "Modificar" class="btn btn-warning">Modificar</button>

                         <a href="usuario.php"><button type="button" class="btn btn-info" ">Cancelar</button></a>
                    </div>
                    <!-- cambiar tipo de boton, sacarlo del form -->  
                </form> 
                <br/>
               
                   
            </div>
            
        </div>
    </div>  



    <div class="col-md-7">
        
        <table class="table table-bordered">
            
            <?php if(isset($validacion_borrar)){
                if($validacion_borrar==false){ ?>
                <div class="alert alert-danger">
                        <strong>No se puede borrar mas usuarios</strong>
                        </div>    

                
            <?php }} ?>

            <thead>
                <tr>
                    
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>

                </tr>
            </thead>
            <tbody>

            <?php foreach($lista_usuarios as $usu) { ?>
                <tr>

                    <td><?php echo $usu['sql_usuario_email'] ?> </td>
                    <td><?php echo $usu['sql_rol_nombre'] ?></td>
                   

                    <td>
                    <form method="post">
                        <input type="hidden" name="usuario_id" id="usuario_id" value="<?php echo $usu['sql_usuario_id'] ?>"/>   
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