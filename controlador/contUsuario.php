<?php 
require_once("../logica/clsUsuario.php");
require_once("../logica/clsAcceso.php");
require_once("../logica/clsSesion.php");
require_once("../logica/clsCase.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
	//session_start();
	$objUsu=new clsUsuario();
	$objSesion = new clsSesion();
	$objAcc = new clsAcceso();
    $objCase = new clsCase();
	switch ($accion){
		
		case "NUEVO_USUARIO": 
				try{
					$existe=$objUsu->verificarUsuario($_POST['txtIdPersona'], $_POST['cboRegistroPerfil'],$_POST['txtRegistroLogin']);
					if($existe->rowCount()<1){
					$objUsu->registrarUsuario($_POST['txtRegistroLogin'],$_POST['txtPrimerPassword'],$_POST['cboRegistroEstado'], $_POST['txtIdPersona'],$_POST['cboRegistroPerfil']);
						echo "Cuenta registrada satisfactoriamente";
					}else{
						$existe=$existe->fetch();	
							$rst="***** Cuenta de usuario NO registrada *****<br/>";
						if($existe['idpersona']==$_POST['txtIdPersona'] && $existe['idperfil']==$_POST['cboRegistroPerfil']){
							$rst.= "<br/> -> Cuenta con persona y rol ingresados ya existe ";
						}
						if(strtolower($existe['login'])==strtolower($_POST['txtRegistroLogin'])){
							$rst.= "<br/> -> Login ingresado ya existe";
						}
						
						echo $rst;
					}
				}catch(Exception $e){
					echo "Lo sentimos cuenta no ha podido ser registrada, intentelo nuevamente";
				}
				break;
				
		case "ACTUALIZAR_USUARIO": 
				try{
					$existe=$objUsu->verificarUsuarioActualizacion($_POST['txtIdPersona'],$_POST['cboRegistroPerfil'],$_POST['txtRegistroLogin'],$_POST['txtIdUsuario']);
					if($existe->rowCount()<1){
						
					$objUsu->actualizarUsuario($_POST['txtIdUsuario'],$_POST['txtIdPersona'],$_POST['cboRegistroPerfil'],$_POST['cboRegistroEstado'],$_POST['txtRegistroLogin'],$_POST['txtPrimerPassword']);
					echo "Cuenta actualizada satisfactoriamente";
						
					}else{
						$existe=$existe->fetch();	
							$rst="***** Cuenta de usuario NO registrada *****<br/>";
						if($existe['idpersona']==$_POST['txtIdPersona'] && $existe['idperfil']==$_POST['cboRegistroPerfil']){
							$rst.= "<br/> -> Cuenta con persona y rol ingresados ya existe ";
						}
						if(strtolower($existe['login'])==strtolower($_POST['txtRegistroLogin'])){
							$rst.= "<br/> -> Login ingresado ya existe";
						}
						
						echo $rst;
					}
				}catch(Exception $e){
					echo "Lo sentimos cuenta no ha podido ser actualizada, intentelo nuevamente";
				}
				break;
				
				
		case "ACTUALIZAR_CLAVE": 
				try{
					$existe=$objUsu->verificarUsuarioClave($_POST['txtIdUsuario'], $_POST['txtIdPersona'], $_POST['txtOldPassword']);
					if($existe->rowCount()==1){
					$objUsu->actualizarClaveUsuario($_POST['txtIdUsuario'], $_POST['txtIdPersona'], $_POST['txtPrimerPassword']);
					echo "Cuenta actualizada satisfactoriamente";
						
					}else{
					echo "Clave ingresada es incorrecta.";	
					}
				}catch(Exception $e){
					echo "Lo sentimos cuenta no ha podido ser actualizada, intentelo nuevamente";
				}
				break;
		
		case "NUEVO_PERFIL": 
				try{
					$existe=$objUsu->verificarPerfil($_POST['txtDescripcion']);
					if($existe->rowCount()<1){
					$objUsu->registrarPerfil($_POST['txtDescripcion'],$_POST['cboEstado']); 
						echo "Perfil registrado satisfactoriamente";
					}else{
						$existe=$existe->fetch();	
							$rst="***** Perfil NO registrado *****<br/>";
						if(strtolower($existe['descripcion'])==strtolower($_POST['txtDescripcion'])){
							$rst.= "<br/> -> Ya existe un perfil con la misma descripcion ";
						}
						
						echo $rst;
					}
				}catch(Exception $e){
					echo "Lo sentimos perfil no ha podido ser registrado, intentelo nuevamente";
				}
				break;
				
		case "ACTUALIZAR_PERFIL": 
				try{
					$existe=$objUsu->verificarPerfil($_POST['txtDescripcion'],$_POST['txtIdPerfil']);
					if($existe->rowCount()<1){
					$objUsu->actualizarPerfil($_POST['txtIdPerfil'],$_POST['txtDescripcion'],$_POST['cboEstado']); 
						echo "Cuenta registrada satisfactoriamente";
					}else{
						$existe=$existe->fetch();	
							$rst="***** Perfil NO registrado *****<br/>";
						if(strtolower($existe['descripcion'])==strtolower($_POST['txtDescripcion'])){
							$rst.= "<br/> -> Ya existe un perfil con la misma descripcion ";
						}
						
						echo $rst;
					}
				}catch(Exception $e){
					echo "Lo sentimos perfil no ha podido ser registrado, intentelo nuevamente";
				}
				break;
		
		case "NUEVO_OPCION": 
				try{
					$existe=$objUsu->verificarOpcion($_POST['txtDescripcion']);
					if($existe->rowCount()<1){
					if($_POST['id_opcionref']='0'){$_POST['id_opcionref']=NULL;}
					$objUsu->registrarOpcionMenu($_POST['txtDescripcion'],$_POST['txtLink'],$_POST['cboEstado'], $_POST['id_opcionref'],$_POST['txtOrden'],$_POST['txtTitle']); 
						echo "Opcion registrado satisfactoriamente";
					}else{
						$existe=$existe->fetch();	
							$rst="***** Opcion NO registrado *****<br/>";
						if(strtolower($existe['descripcion'])==strtolower($_POST['txtDescripcion'])){
							$rst.= "<br/> -> Ya existe una opcion con la misma descripcion ";
						}
						
						echo $rst;
					}
				}catch(Exception $e){
					echo "Lo sentimos opcion no ha podido ser registrado, intentelo nuevamente";
				}
				break;
				
		case "ACTUALIZAR_OPCION": 
				try{
					$existe=$objUsu->verificarOpcion($_POST['txtDescripcion'],$_POST['txtIdOpcion']);
					if($existe->rowCount()<1){
					if($_POST['id_opcionref']='0'){$_POST['id_opcionref']=NULL;}
					$objUsu->actualizarOpcionMenu($_POST['idopcion'],$_POST['txtDescripcion'],$_POST['txtLink'],$_POST['cboEstado'], $_POST['id_opcionref'],$_POST['txtOrden'],$_POST['txtTitle']); 
						echo "Opcion registrado satisfactoriamente";
					}else{
						$existe=$existe->fetch();	
							$rst="***** Opcion NO registrado *****<br/>";
						if(strtolower($existe['descripcion'])==strtolower($_POST['txtDescripcion'])){
							$rst.= "<br/> -> Ya existe una opcion con la misma descripcion ";
						}
						
						echo $rst;
					}
				}catch(Exception $e){
					echo "Lo sentimos opcion no ha podido ser registrado, intentelo nuevamente";
				}
				break;
		
		
		case "CAMBIAR_ESTADO_USUARIO": 
				try{
					if($_POST['bloque']=='S'){
						$ids=$_POST['iduser[]'];
						foreach($ids as $k=>$v){
							$objUsu->actualizarEstadoUsuario($v,$_POST['estado']);
						}
					}else{
					$objUsu->actualizarEstadoUsuario($_POST['iduser'],$_POST['estado']); 
					}
					echo "Datos actualizados satisfactoriamente";
				}catch(Exception $e){
					echo "Lo sentimos cuenta de usuario no ha sido anulada, intentelo nuevamente";
				}
				break;
				
		case "CAMBIAR_ESTADO_ACCESO": 
				try{
					if($_POST['bloque']=='S'){
						$ids=$_POST['idopcion[]'];
						foreach($ids as $k=>$v){
							//$objUsu->actualizarEstadoUsuario($v,$_POST['estado']);
						}
					}else{
						if($_POST['estado']=='N'){
							$objUsu->registrarAcceso($_POST['idperfil'],$_POST['idopcion'],'N'); 
						}else{
							$objUsu->quitarAcceso($_POST['idopcion']); 
							// en este caso se lanzara el idacceso en la variable idopcion
						}
					}
					echo "Datos actualizados satisfactoriamente";
				}catch(Exception $e){
					echo "Lo sentimos cuenta de usuario no ha sido anulada, intentelo nuevamente";
				}
				break;

			case "CAMBIAR_ESTADO_ACCESO2": 

			try{
				//cero tiene que eliminarlo.
				// check tiene que registrarlo
					$idperfil = $_POST['idperfil'];
					$idopcion = $_POST['idopcion'];
					$idacceso = $_POST['idacceso'];

					$permiso = $_POST['permiso'];
					
					
					if ($permiso == 1 ) {
						$objUsu->registrarAcceso($_POST['idperfil'],$_POST['idopcion'],'N');
						echo "Permiso registrado ";
					}else{
						$objUsu->quitarAcceso($_POST['idperfil'],$_POST['idopcion']); 
						echo "Permiso Eliminado ";
					}
				
					
				}catch(Exception $e){
					echo "*** Error al actualizar permiso.";
				}

				break;
		
		case "CAMBIAR_ESTADO_PERFIL": 
				try{
					if($_POST['bloque']=='S'){
						$ids=$_POST['idperfil[]'];
						foreach($ids as $k=>$v){
							$objUsu->cambiarEstadoPerfil($v,$_POST['estado']);
						}
					}else{
					$objUsu->cambiarEstadoPerfil($_POST['idperfil'],$_POST['estado']); 
					}
					echo "Datos actualizados satisfactoriamente";
				}catch(Exception $e){
					echo "Lo sentimos cuenta de usuario no ha sido anulada, intentelo nuevamente";
				}
				break;
				
		case "CAMBIAR_ESTADO_OPCION": 
				try{
					if($_POST['bloque']=='S'){
						$ids=$_POST['idopcion[]'];
						foreach($ids as $k=>$v){
							$objUsu->cambiarEstadoOpcionMenu($v,$_POST['estado']);
						}
					}else{
					$objUsu->cambiarEstadoOpcionMenu($_POST['idopcion'],$_POST['estado']); 
					}
					echo "Datos actualizados satisfactoriamente";
				}catch(Exception $e){
					echo "Lo sentimos cuenta de usuario no ha sido anulada, intentelo nuevamente";
				}
				break;

		case "CBO_PERFIL": 
				try{
					$data=$objUsu->consultarPerfil('','N',0,1000);
					echo "<option value='0'>- Todos -</option>";
					while($fila=$data->fetch(PDO::FETCH_NAMED)){
						echo "<option value='".$fila['idperfil']."' >".$fila['descripcion']."</option>";
					}
				}catch(Exception $e){
					echo "Los sentimos, datos no pudieron ser obtenidos";
				}
				break;

		case "CBO_PERFIL_2": 
				try{
					$data=$objUsu->consultarPerfil('','N',0,1000);
					while($fila=$data->fetch(PDO::FETCH_NAMED)){
						echo "<option value='".$fila['idperfil']."' >".$fila['descripcion']."</option>";
					}
				}catch(Exception $e){
					echo "Los sentimos, datos no pudieron ser obtenidos";
				}
				break;
				
		case "CBO_USUARIO_ALL": 
				try{
					$data=$objUsu->consultarUsuario("","0","N",0,1000000);
					while($fila=$data->fetch(PDO::FETCH_NAMED)){
						echo "<option value='".$fila['idusuario']."' >".$fila['persona'].' / '.$fila['perfil']."</option>";
					}
				}catch(Exception $e){
					echo "Los sentimos, datos no pudieron ser obtenidos";
				}
				break;
				
		case "CBO_PERFIL2": 
				try{
					$data=$objUsu->consultarPerfil('','N',0,1000);
					echo "<option value='0'>- Seleccione uno -</option>";
					while($fila=$data->fetch(PDO::FETCH_NAMED)){
						echo "<option value='".$fila['idperfil']."' >".$fila['descripcion']."</option>";
					}
				}catch(Exception $e){
					echo "Los sentimos, datos no pudieron ser obtenidos";
				}
		break;
		
		case "IN": 
				try{
					$rst=$objUsu->consultarAcceso($_POST['txtLogin'],$_POST['txtPassword']);
					if($rst->rowCount()>0){
						$user=$rst->fetch();					                    
						
						$horario_acceso=getConfig(77);
						$perfiles_restriccion_acceso = getConfig(78);
						if($horario_acceso!=''){
							$horario = explode("-",$horario_acceso);
							$horaActual = date("H:i");
							$estaDentroHorario =  estasDentroDelHorario($horaActual,$horario[0], $horario[1]);

							if(!$estaDentroHorario && $perfiles_restriccion_acceso!=''){
								$perfiles_restriccion_acceso = explode(",", $perfiles_restriccion_acceso);
								if(!in_array($user['idperfil'], $perfiles_restriccion_acceso)){
									throw new Exception("Lo sentimos, el acceso al sistema está restringido en el siguiente horario:<br><center><strong><h3>".$horario_acceso."</h3></strong></center>", 555);
								}
							}
						}

						$_SESSION['idusuario']=$user['idusuario'];
						$_SESSION['idpersona']=$user['idpersona'];
						$_SESSION['idperfil']=$user['idperfil'];
						$_SESSION['persona']=$user['persona'];
						$_SESSION['login']=$user['login'];
	                    $_SESSION['nrodoc']=$user['nro_documento'];
	                    $idempresas=array();
	                    $acceso = $objUsu->consultarAccesoInstitucionSucursal($user['idperfil']);

						$idsucursal=array();
	                    while($acc= $acceso->fetch(PDO::FETCH_NAMED)){
	                        $idempresas[]=$acc;
	                        $idsucursal[]=$acc["idsucursal"];
	                    }
	                    
	                    $_SESSION['acceso']=$idempresas;
	                                        
	                    $accesoUser = $objAcc->consultarAccesoByIdUsuario($user['idusuario']);
	                    if($accesoUser->rowCount()>0){
	                    	$_SESSION['user_acceso'] = $accesoUser->fetch(PDO::FETCH_NAMED);
	                    }


						$objUsu->actualizarClaveSimple($user['idusuario'],$_POST['txtPassword']);
						$objUsu->actualizarVisitas($user['idusuario']);
						if(!isset($user['idmatricula'])){
							$user['idmatricula']=NULL;
						}
						$objSesion->insertarSesion($user['idusuario'],$user['idperfil'],$user['idmatricula']);
						//Mejora de seguridad
						//echo "<META HTTP-EQUIV=Refresh CONTENT='0;URL= ../admin.php'>";		
						$configuraciones = $objCase->getListTableFiltroSimple("mgconfig","1",1);
						$config=array();
						while($fila = $configuraciones->fetch(PDO::FETCH_NAMED)){
							$config[intval($fila['idconfig'])][intval($fila['idinstitucion'])][intval($fila['idsucursal'])] = $fila['valor'];
						}
						$_SESSION['config'] = $config;
						
						echo 'admin.php';
					}else{
						//Mejora de seguridad
						//echo "<META HTTP-EQUIV=Refresh CONTENT='0;URL= ../index.php?error=1'>";
						echo '*** Usuario o contraseña no válido ***';
					}
				}catch(Exception $e){
					echo "*** ERROR AL ACCEDER<br/><br/>".$e->getMessage();
				}
				break;
		
		case "OUT": 
		
				echo "<META HTTP-EQUIV=Refresh CONTENT='0;URL= ../index.php?idx=".$_SESSION['emp_codigobd']."'>";	
					$_SESSION['idusuario']=NULL;
					$_SESSION['idpersona']=NULL;
					$_SESSION['idperfil']=NULL;
					$_SESSION['persona']=NULL;
					$_SESSION['login']=NULL;
				session_unset();
				session_destroy();	
				
				
				break;

        case "OBTENER_CLAVE":
                $busqueda=$_POST['busqueda'];

                $dni=$busqueda;
                $idmatricula=$busqueda;

                $usuario= $objUsu->ObtenerDatosUsuarioBuscado($dni,$idmatricula);
                if($usuario->rowCount()>0){
                    $usuario=$usuario->fetch(PDO::FETCH_NAMED);
                    $texto="<table class='table table-bordered'><tr>";
                    $texto.="<th>Interesado</th><td>".$usuario['apellidos']." ".$usuario['nombres']."</td>";
                    $texto.="</tr><tr>";
                    $texto.="<th>Usuario</th><td>".$usuario['login']."</td>";
                    $texto.="</tr><tr>";
                    $texto.="<th>Contraseña</th><td>".$usuario['clave']."</td>";
                    $texto.="</tr></table>";
                    echo $texto;
                }else{
                    echo "<div class='box-body' align='center'><span class='text-red'><li class='fa fa-warning'></li> Cuenta de alumno/apoderado no localizado.</span></div>";
                }
             break;
        case "ASIGNAR_PERFILSUCURSAL": 

			try{
					$idperfil = $_POST['idperfil'];
					$idsucursal = $_POST['idsucursal'];
					$idinstitucion=$_POST['idinstitucion'];
					$acceso = $_POST['acceso'];
					
					if ($acceso==1) {
						$estado='N';
						$mensaje="Sucursal Asignada";
					}else{
						$estado='E';
						$mensaje="Acceso a Sucursal Desactivada";
					}
                     
				$idconfiguracion=$objUsu->consultarIDsucursalperfil($idsucursal,$idperfil);

					if ($idconfiguracion == null) {
						$objUsu->registrarSucursalPerfil($idperfil,$idsucursal,$idinstitucion, 'N');
						echo $mensaje;
					}else{

						$objUsu->ActualizarSucursalPerfil($idconfiguracion,$estado); 
						echo $mensaje;
					}
				
					
				}catch(Exception $e){
					echo "*** Error al Asignar Sucursal.";
				}

		 break;
		 case "COPIAR_PERMISOS_PERFIL": 
			try{
				    $idperfil=$_POST['idperfil'];
				    $idperfilDestino=$_POST['idperfilDestino'];
					$permisos=$objCase->getListTableFiltroSimple("acceso","estado","N","idperfil",$idperfil);
					$permisos=$permisos->fetchAll(PDO::FETCH_NAMED);

					$permisosD=$objCase->getListTableFiltroSimple("acceso","estado","N","idperfil",$idperfilDestino);

					if ($permisosD->rowCount()>0) {
						$objCase->eliminarBD('acceso','idperfil',$idperfilDestino);
					}

					foreach ($permisos as $k => $v) {
                        
						$valores[':idacceso']=null;
						$valores[':idperfil']=$idperfilDestino;
						$valores[':idopcion']=$v['idopcion'];
						$valores[':estado']='N';
						$objCase->insertar('acceso',$valores);
												
					}
					echo "Permisos copiados satisfactoriamente";

				}catch(Exception $e){
					echo "*** Error al Copiar Permisos.";
				}

		break;
		case "CONTROL_REGISTRO":
		      try {
		      	$nroUsuarios=$objUsu->consultarusuariosactivos();
                $idopcion=$_POST['idopcion'];
                $opcion = $objCase->getLastRowTableFiltroSimple("opcion", "idopcion", $idopcion);
               if ($opcion['nro_registro']>0) {
               	  if ($nroUsuarios<$opcion['nro_registro']) {
                    echo "NUEVO";
                 }
               }else{
               	   echo "NUEVO";
               }
                
                

		      } catch (Exception $e) {
		      	
		      }
		break;

		default: 
				echo "Debe especificar alguna accion"; 
				break;
	}
	
}


?>