<?php 
require_once("../logica/clsPersona.php");
require_once("../logica/clsCase.php");
require_once("../logica/clsCompartido.php");
require_once("../logica/clsEmpresa.php");
//require_once("../logica/clsProspecto.php");
controlador($_POST['accion']);

function controlador($accion){
	
	//$objProspecto = new clsProspecto();
	$objPersona=new clsPersona();
	$objEmpresa=new clsEmpresa();
	$objCase = new clsCase();
	switch ($accion){
		
		case "NUEVA_PERSONA_SIMPLE": 
				try{
					if(!isset($_POST['cboTipoDocDirectorio'])){
						$_POST['cboTipoDocDirectorio'] = 0;
						if(strlen($_POST['txtNroDocPersona'])==8){
							$_POST['cboTipoDocDirectorio'] = 1;
						}else if(strlen($_POST['txtNroDocPersona'])==11){
							$_POST['cboTipoDocDirectorio'] = 6;
						}
					}
					$per=$objPersona->consultarPersonaDni($_POST['txtNroDocPersona']);
					if($per->rowCount()<=0){
					$objPersona->insertarPersonaNuevo("",$_POST['txtNombrePersonaNew'],$_POST['txtNroDocPersona'],"N","","","","","","","","","",$_POST['escliente'], $_POST['esproveedor'], $_POST['estrabajador'],$_POST['cboTipoDocDirectorio']);
					$idpersona=$objPersona->getUltimaPersona();
					echo "Datos registrados satisfactoriamente%%__".$idpersona."%%__".$_POST['txtNombrePersonaNew'];
					}else{
					//@liza - Inicio
					//$pre=$per->fetch(PDO::FETCH_NAMED);
					$per=$per->fetch(PDO::FETCH_NAMED);
					//@liza - Fin
					echo "Registro de persona ya existe, seran tomados los datos.%%__".$per['idpersona']."%%__".$per['apellidos'].' '.$per['nombres'];	
					}
				}catch(Exception $e){
					echo "***Lo sentimos persona no ha podido ser registrada, intentelo nuevamente";
				}
				break;


		case "NUEVO_DIRECTORIO":
				try{
						
						/*if($nro_documentoAux->rowCount()>0) {
							throw new Exception("Lo sentimos ya existe un registro con el mismo NRO DE DOCUMENTO");
						}*/
						$fnacimiento = formatoBDFecha($_POST['fnacimiento']);
						if($fnacimiento==''){
							$fnacimiento=NULL;
						}
						if(isset($_POST['chkCliente'])) {
							$escliente=$_POST['chkCliente'];
						}else{
							if(isset($_POST['origen']) && ($_POST['origen']=='VED' || $_POST['origen']=='CVE')){ 
								$escliente= 1;
							}else{
								$escliente= 0;
							}
						}

						if (isset($_POST['chkProveedor'])) {
							$esproveedor=$_POST['chkProveedor'];
						}else{
							$esproveedor= 0;
						}

						if (isset($_POST['chkTrabajador'])) {
							$estrabajador=$_POST['chkTrabajador'];
						}else{
							$estrabajador= 0;
						}

						if($_POST['txtLineaCredito']==""){
							$_POST['txtLineaCredito']=0;
						}

						if(!isset($_POST['cboRuta'])){
							$_POST['cboRuta']=0;
						}

						if(!isset($_POST['codhuellero'])){
							$_POST['codhuellero']='';
						}

						$nro_documentoAux = $objPersona->consultarPersonaDni($_POST['txtNroDocumento']);
						if($nro_documentoAux->rowCount()==0) {
							
						if(strlen($_POST['txtNroDocumento'])==8){
							$objPersona->RegistrarDirectorioDNI($_POST['txtApellidos'],$_POST['txtNombres'],$_POST['txtNroDocumento'],$_POST['txtE-mail'],$_POST['txtDireccion'],$_POST['cboSexo'],$_POST['txtTelefonoFijo'],$_POST['txtTelefonoCelular'],$_POST['txtTelefonoOtro'],$fnacimiento,$_POST['cboRuta'],$_POST['txtLineaCredito'],$escliente,$esproveedor,$estrabajador,$_POST['txtObservacion'],$_POST['cboTipoDocDirectorio'],$_POST['codhuellero'],$_POST['txtFacebook'],$_POST['medio_comunicacion'],$_POST['txtOcupacion'],$_POST['cboEmpresa'],$_POST['cboTipoTrabajador'],$_POST['cboPuestoTrabajo'],$_POST['cboTurno'],$_POST['cboTipoContrato']);
						}else{
							$objPersona->RegistrarDirectorioRUC($_POST['txtApellidos'],$_POST['txtNombres'],$_POST['txtNombres'],$_POST['txtNroDocumento'],$_POST['txtE-mail'],$_POST['txtDireccion'],$_POST['cboSexo'],$_POST['txtTelefonoFijo'],$_POST['txtTelefonoCelular'],$_POST['txtTelefonoOtro'],$fnacimiento,$_POST['cboRuta'],$_POST['txtLineaCredito'],$escliente,$esproveedor,$estrabajador,$_POST['txtObservacion'],$_POST['cboEmpresa'],$_POST['cboTipoTrabajador'],$_POST['cboPuestoTrabajo'],$_POST['cboTurno'],$_POST['cboTipoContrato']);
						}

						$idpersona = $objCase->getLastIdInsert('persona', 'idpersona');

						$values = array(
											':idpersona'=>$idpersona,
											':ubigeo_dir_dist'=>$_POST['ubigeo_dir_dist'],
											':ubigeo_dir_prov'=>$_POST['ubigeo_dir_prov'],
											':ubigeo_dir_dep'=>$_POST['ubigeo_dir_dep'],
											':ubigeo' => $_POST['ubigeo']
										);
						$objCase->actualizar('persona','idpersona', $values);

						if($_POST['txtIdProspecto']!='0'){
							$values = array(
								':idprospecto'=>$_POST['txtIdProspecto'],
								':nro_documento'=>$_POST['txtNroDocumento'],
								':idcliente'=>$idpersona,
								':fhregistro_cliente'=>date('Y-m-d H:i:s'),
								':idregistrador_cliente'=>$_SESSION['idpersona']
							);
							$objCase->actualizar('prospecto','idprospecto', $values);
						}
				    }else{

				    	$objPersona->EditarDirectorio($_POST['txtIdDirectorio'],$_POST['txtApellidos'],$_POST['txtNombres'],$_POST['txtNroDocumento'],$_POST['txtE-mail'],$_POST['txtDireccion'],$_POST['cboSexo'],$_POST['txtTelefonoFijo'],$_POST['txtTelefonoCelular'],$_POST['txtTelefonoOtro'],$fnacimiento,$_POST['cboRuta'],$_POST['txtLineaCredito'],$escliente,$esproveedor,$estrabajador,$_POST['txtObservacion'],$_POST['cboTipoDocDirectorio'],$_POST['codhuellero'],$_POST['txtFacebook'],$_POST['medio_comunicacion'],$_POST['txtOcupacion'],$_POST['cboEmpresa'],$_POST['cboTipoTrabajador'],$_POST['cboPuestoTrabajo'],$_POST['cboTurno'],$_POST['cboTipoContrato']);

						$values = array(
							':idpersona'=>$_POST['txtIdDirectorio'],
							':ubigeo_dir_dist'=>$_POST['ubigeo_dir_dist'],
							':ubigeo_dir_prov'=>$_POST['ubigeo_dir_prov'],
							':ubigeo_dir_dep'=>$_POST['ubigeo_dir_dep'],
							':ubigeo' => $_POST['ubigeo']
						);
						$objCase->actualizar('persona','idpersona', $values);

						$registro=$objCase->getRowTableFiltroSimple("prospecto","nro_documento", $_POST['txtNroDocumento'], 'estado', 'N');

						if($registro!=NULL){
							$values = array(
								':idprospecto'=>$registro['idprospecto'],
								':nro_documento'=>$_POST['txtNroDocumento'],
								':idcliente'=>$_POST['txtIdDirectorio'],
								':fhregistro_cliente'=>date('Y-m-d H:i:s'),
								':idregistrador_cliente'=>$_SESSION['idpersona']
							);
							$objCase->actualizar('prospecto','idprospecto', $values);
						}
				    }

						echo "Directorio registrado satisfactoriamente";
				}catch(Exception $e){
					echo "Lo sentimos no se efectuaron los cambios en el sistema. \n ".$e->getMessage();
				}
				break;	

		case "ACTUALIZAR_DIRECTORIO": 
				try{
					$fnacimiento = formatoBDFecha($_POST['fnacimiento']);
						if($fnacimiento==''){
							$fnacimiento=NULL;
						}					
					if (isset($_POST['chkCliente'])) {
							$escliente=$_POST['chkCliente'];
						}
						else{
							$escliente= 0;
						}

						if (isset($_POST['chkProveedor'])) {
							$esproveedor=$_POST['chkProveedor'];
						}
						else{
							$esproveedor= 0;
						}

						if (isset($_POST['chkTrabajador'])) {
							$estrabajador=$_POST['chkTrabajador'];
						}
						else{
							$estrabajador= 0;
						}

						if($_POST['txtLineaCredito']==""){
							$_POST['txtLineaCredito']=0;
						}

						if(!isset($_POST['cboRuta'])){
							$_POST['cboRuta']=0;
						}

						if(!isset($_POST['codhuellero'])){
							$_POST['codhuellero']='';
						}
	
						$objPersona->EditarDirectorio($_POST['txtIdDirectorio'],$_POST['txtApellidos'],$_POST['txtNombres'],$_POST['txtNroDocumento'],$_POST['txtE-mail'],$_POST['txtDireccion'],$_POST['cboSexo'],$_POST['txtTelefonoFijo'],$_POST['txtTelefonoCelular'],$_POST['txtTelefonoOtro'],$fnacimiento,$_POST['cboRuta'],$_POST['txtLineaCredito'],$escliente,$esproveedor,$estrabajador,$_POST['txtObservacion'],$_POST['cboTipoDocDirectorio'],$_POST['codhuellero'],$_POST['txtFacebook'],$_POST['medio_comunicacion'],$_POST['txtOcupacion'],$_POST['cboEmpresa'],$_POST['cboTipoTrabajador'],$_POST['cboPuestoTrabajo'],$_POST['cboTurno'],$_POST['cboTipoContrato']);

						$values = array(
							':idpersona'=>$_POST['txtIdDirectorio'],
							':ubigeo_dir_dist'=>$_POST['ubigeo_dir_dist'],
							':ubigeo_dir_prov'=>$_POST['ubigeo_dir_prov'],
							':ubigeo_dir_dep'=>$_POST['ubigeo_dir_dep'],
							':ubigeo' => $_POST['ubigeo']
						);
						$objCase->actualizar('persona','idpersona', $values);

						$registro=$objCase->getRowTableFiltroSimple("prospecto","nro_documento", $_POST['txtNroDocumento'], 'estado', 'N');

						if($registro!=NULL){
							$values = array(
								':idprospecto'=>$registro['idprospecto'],
								':nro_documento'=>$_POST['txtNroDocumento'],
								':idcliente'=>$_POST['txtIdDirectorio'],
								':fhregistro_cliente'=>date('Y-m-d H:i:s'),
								':idregistrador_cliente'=>$_SESSION['idpersona']
							);
							$objCase->actualizar('prospecto','idprospecto', $values);
						}

					echo "Directorio actualizado satisfactoriamente";
				}catch(Exception $e){
					echo "Lo sentimos no se efectuaron los cambios en el sistema, intentelo nuevamente";
				}
				break;

		case "CAMBIAR_ESTADO_DIRECTORIO": 
				try{											
					$objPersona->cambiarEstadoDirectorio($_POST['id'],$_POST['estado']); 
						echo "Directorio eliminado satisfactoriamente";
				}catch(Exception $e){
					echo "Lo sentimos no se ha podido eliminar directorio, inténtelo nuevamente";
				}
				break;

		case "GET_NRO_DOCUMENTO":
		        
				$user = $objPersona->consultarPersonaDni($_POST['nro_documento']);
				if($user->rowCount()>0){
    				$user = $user->fetch(PDO::FETCH_ASSOC);
    				$user['fnacimiento']=formatoCortoFecha($user['fnacimiento']);
    				$user['idprospecto']=0;
				}else{	
					$user = array();
				}
				echo json_encode($user);	
				break;
		
		
		case "GET_PERSONA":
		        
				$user = $objPersona->consultarPersonaById($_POST['idpersona']);
				if($user->rowCount()>0){
					$user = $user->fetch(PDO::FETCH_ASSOC);
					$user['nro_documento']=$user['nro_documento'];
					$user['apellidos']=$user['apellidos'];
					$user['nombres']=$user['nombres'];
					$user['firma']=$user['firma'];
					$user['licencia']=$user['licencia'];
				}else{
					$user = array();
				}
				echo json_encode($user);	
				break;
		
		case "GUARDAR_FIRMA":
			try{
					global $cnx;
					$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
					$cnx->beginTransaction();
					$idpersona=$_POST['idpersona'];
					$idempresa=$_POST['idempresa'];
					$persona = $objPersona->consultarPersonaById($idpersona);
					$persona = $persona->fetch(PDO::FETCH_NAMED);
					$puesto = $objCase->getRowTableFiltroSimple("orgpuesto_trabajo","idpuesto_trabajo",$persona['idpuesto_trabajo'],"estado","N");
					$empresa = $objEmpresa->consultarEmpresaPorID($idempresa);
					$empresa = $empresa->fetch(PDO::FETCH_NAMED);
					$inputFoto = $_POST['foto'];
					$firmafile="";
					if ($inputFoto !='') {
						$nombre_empresa = str_replace(' ','_',$empresa['razon_social']);
						$nombre_trabajador = str_replace(' ','_',$persona['nombres'].$persona['apellidos']);
	                    $urlbase="../files/firmas/".$nombre_empresa."/".$nombre_trabajador;
	                    //eliminar si existe imagen
						if(file_exists("../files/firmas/".$persona['firma'])){
							@unlink("../files/firmas/".$persona['firma']);
						}	

	                    $numero = rand();
	                    $nombre_imagen = "IMG_".$idpersona.$numero.".JPG";

	                    if (!file_exists($urlbase) && !is_dir($urlbase)){
	                        //mkdir($urlbase);
	                        mkdir($urlbase,0777,true);
	                    }
						
						
						if(file_exists($urlbase."/".$nombre_imagen)){
							@unlink($urlbase."/".$nombre_imagen);
						}				
						
	                    if(isset($_POST['txtFoto'])){
	                        $str="data:image/jpeg;base64,"; 
	                        $_POST['txtFoto']=str_replace($str,"",$_POST['txtFoto']);
	                        file_put_contents($urlbase."/".$nombre_imagen, base64_decode($_POST['txtFoto']));                        		
	                    }
	                    $firmafile = $nombre_empresa."/".$nombre_trabajador."/".$nombre_imagen;
	                    $objCase->actualizarDatoSimple('persona', 'firma', $firmafile , "idpersona", $idpersona);

					}


					
                    //guardar en la tabla del registro
                    $tabla = $_POST['tabla'];
					$id=$objCase->getPkFromTable($tabla);
                    $firma = $objCase->getRowTableFiltroSimple($tabla, "idregistro", $_POST['idregistro'],"idtrabajador",$_POST['idpersona'],'estado','N');
                    $registro=false;
                    if( $firma==null && ($inputFoto!='' || $persona['firma']!= null)){
                    	$firmaimg=$firmafile;
                    	if ($firmaimg=='') {
                    		$firmaimg = $persona['firma'];
                    	}

						$valuesCabecera[':idprocedimiento_firma']= NULL;
						$valuesCabecera[':idregistro']= $_POST['idregistro'];
						$valuesCabecera[':idtrabajador']= $_POST['idpersona'];
						$valuesCabecera[':txtcargo']= $puesto['nombre'];
						$valuesCabecera[':firma']= $firmaimg;
						$valuesCabecera[':estado']= 'N';
						$objCase->insertarWithoutUpper($tabla, $valuesCabecera);
						$registro=true;
					}

					if ($inputFoto=='' && $firma==null && $persona['firma']== null) {
						echo "selecionar una imagén";
					}else{
						if ($registro) {
							echo "Firma registrada de forma satisfactoria.";
						}else{
							echo " trabajador ya fue asignado";
						}
					}

					$cnx->commit();
					

			}catch(Exception $e){
				echo "*** No fue posible guardar, intentelo nuevamente. ".$e->getMessage();
			}
			break;
		case 'EDITAR_CARGO_FIRMA':
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idvalor = $_POST['id'];
				$value = $_POST['value'];
				$tabla = $_POST['tabla'];
				$idtxt=$objCase->getPkFromTable($tabla);
	            $objCase->actualizarDatoSimple($tabla, 'txtcargo', $value, $idtxt, $idvalor);

				$cnx->commit();
				echo "Firma actualizada de forma satisfactoria.";
				

			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al eliminar la firma. ". $e->getMessage();
			}
			
			break;
		case "CAMBIAR_ESTADO_FIRMA":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$idvalor = $_POST['id'];
				$tabla = $_POST['tabla'];
				$idtxt=$objCase->getPkFromTable($tabla);
	            $objCase->actualizarDatoSimple($tabla, 'estado', $_POST['estado'], $idtxt, $idvalor);

				$cnx->commit();
				echo "Firma actualizada de forma satisfactoria.";
				

			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al eliminar la firma. ". $e->getMessage();
			}
			break;
		case 'GUARDAR_FIRMA_DIRECTORIO':
			try {
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();
				$idpersona=$_POST['idpersona'];
				$idempresa=$_POST['idempresa'];
				$persona = $objPersona->consultarPersonaById($idpersona);
				$persona = $persona->fetch(PDO::FETCH_NAMED);
				$empresa = $objEmpresa->consultarEmpresaPorID($idempresa);
				$empresa = $empresa->fetch(PDO::FETCH_NAMED);
				$inputFoto = $_POST['inputfoto'];
				$firmafile="";
				if ($inputFoto !='') {
					$nombre_empresa = str_replace(' ','_',$empresa['razon_social']);
					$nombre_trabajador = str_replace(' ','_',$persona['nombres'].$persona['apellidos']);
                    $urlbase="../files/firmas/".$nombre_empresa."/".$nombre_trabajador;
                    //eliminar si existe imagen
					if(file_exists("../files/firmas/".$persona['firma'])){
						@unlink("../files/firmas/".$persona['firma']);
					}	

                    $numero = rand();
                    $nombre_imagen = "IMG_".$idpersona.$numero.".JPG";

                    if (!file_exists($urlbase) && !is_dir($urlbase)){
                        //mkdir($urlbase);
                        mkdir($urlbase,0777,true);
                    }
					
					
					if(file_exists($urlbase."/".$nombre_imagen)){
						@unlink($urlbase."/".$nombre_imagen);
					}				
					
                    if(isset($_POST['txtFoto'])){
                        $str="data:image/jpeg;base64,"; 
                        $_POST['txtFoto']=str_replace($str,"",$_POST['txtFoto']);
                        file_put_contents($urlbase."/".$nombre_imagen, base64_decode($_POST['txtFoto']));                        		
                    }
                    $firmafile = $nombre_empresa."/".$nombre_trabajador."/".$nombre_imagen;
                    $objCase->actualizarDatoSimple('persona', 'firma', $firmafile , "idpersona", $idpersona);
                    echo "Firma registrada satisfactoriamente";
				}else{
					echo "selecionar una imagén";
				}
				$cnx->commit();
			} catch (Exception $e) {
				echo "*** No fue posible guardar imagén, intentelo nuevamente. ".$e->getMessage();
			}
			
			break;

		default: 
				echo "***Debe especificar alguna accion"; 
				break;
	}
	
}

?>