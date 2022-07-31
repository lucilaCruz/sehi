<?php 
require_once("../logica/clsPersona.php");
require_once("../logica/clsEmpresa.php");
require_once("../logica/clsCase.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){
	
	$objEmpresa = new clsEmpresa();
	$objPersona=new clsPersona();
	$objCase = new clsCase();

	$cod_user='';
	$iduser=0;
	if($_SESSION['idperfil']>=1){
		$user = $objCase->getRowTableFiltroSimple('perfil', 'idperfil', $_SESSION['idperfil']);
		$cod_user = $user['descripcion'];
		$iduser = $_SESSION['idusuario'];
	}

	switch ($accion){
		
	
		case "NUEVO_EMPRESA":
				try{

					global $cnx;
					$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
					$cnx->beginTransaction();
						
					//INICIO REGISTRO DE EMPRESA
					$valuesCabecera = $objEmpresa->getColumnTablaEmpresa();
			
					$valuesCabecera[':ruc']=$_POST['txtNroDocumento'];
					$valuesCabecera[':razon_social']=$_POST['txtRazonSocial'];
					$valuesCabecera[':nombre_comercial']=$_POST['txtNombreComercial'];
					$valuesCabecera[':representante']=$_POST['txtRepresentante'];
					$valuesCabecera[':direccion']=$_POST['txtDireccion'];
					$valuesCabecera[':direccion_alterna']=$_POST['txtDireccionAlterna'];
					$valuesCabecera[':ubigeo']=$_POST['ubigeo'];
					$valuesCabecera[':ubigeo_dir_dep']=$_POST['ubigeo_dir_dep'];
					$valuesCabecera[':ubigeo_dir_prov']=$_POST['ubigeo_dir_prov'];
					$valuesCabecera[':ubigeo_dir_dist']=$_POST['ubigeo_dir_dist'];
					$valuesCabecera[':email']=$_POST['txtEmail'];
					$valuesCabecera[':telfijo']=$_POST['txtTelefonoFijo'];
					$valuesCabecera[':telcelular']=$_POST['txtTelefonoCelular'];
					$valuesCabecera[':telotro']=$_POST['txtTelefonoOtro'];
					$valuesCabecera[':nro_trabajadores']=$_POST['txtNroTrabajadores'];
					$valuesCabecera[':mision']=$_POST['txtMision'];
					$valuesCabecera[':vision']=$_POST['txtVision'];
					$valuesCabecera[':observacion']=$_POST['txtObservacion'];
					$valuesCabecera[':f_registro']=date('Y-m-d');
					$valuesCabecera[':f_modificar']=date('Y-m-d');
					$valuesCabecera[':cod_user']=$iduser;
	
					$objCase->insertar('orgempresa', $valuesCabecera);

					$cnx->commit();
					echo "Empresa registrado satisfactoriamente";
				}catch(Exception $e){
					echo "Lo sentimos no se efectuaron los cambios en el sistema. \n ".$e->getMessage();
				}
				break;	

		case "ACTUALIZAR_EMPRESA": 
				try{

					global $cnx;
					$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
					$cnx->beginTransaction();

					$idempresa = $_POST['txtIdEmpresa'];
					$empresa = $objEmpresa->consultarEmpresaPorID($idempresa);
					$empresa = $empresa->fetch(PDO::FETCH_NAMED);

					//ACTUALIZAR EMPRESA
					$valuesCabecera = $objEmpresa->getColumnTablaEmpresa();

					$valuesCabecera[':idempresa']=$idempresa;
					$valuesCabecera[':ruc']=$_POST['txtNroDocumento'];
					$valuesCabecera[':razon_social']=$_POST['txtRazonSocial'];
					$valuesCabecera[':nombre_comercial']=$_POST['txtNombreComercial'];
					$valuesCabecera[':representante']=$_POST['txtRepresentante'];
					$valuesCabecera[':direccion']=$_POST['txtDireccion'];
					$valuesCabecera[':direccion_alterna']=$_POST['txtDireccionAlterna'];
					$valuesCabecera[':ubigeo']=$_POST['ubigeo'];
					$valuesCabecera[':ubigeo_dir_dep']=$_POST['ubigeo_dir_dep'];
					$valuesCabecera[':ubigeo_dir_prov']=$_POST['ubigeo_dir_prov'];
					$valuesCabecera[':ubigeo_dir_dist']=$_POST['ubigeo_dir_dist'];
					$valuesCabecera[':email']=$_POST['txtEmail'];
					$valuesCabecera[':telfijo']=$_POST['txtTelefonoFijo'];
					$valuesCabecera[':telcelular']=$_POST['txtTelefonoCelular'];
					$valuesCabecera[':telotro']=$_POST['txtTelefonoOtro'];
					$valuesCabecera[':nro_trabajadores']=$_POST['txtNroTrabajadores'];
					$valuesCabecera[':mision']=$_POST['txtMision'];
					$valuesCabecera[':vision']=$_POST['txtVision'];
					$valuesCabecera[':observacion']=$_POST['txtObservacion'];
					$valuesCabecera[':f_registro']=$empresa['f_registro'];
					$valuesCabecera[':f_modificar']=date('Y-m-d');
					$valuesCabecera[':cod_user']=$iduser;
					
					$objCase->actualizar('orgempresa', 'idempresa', $valuesCabecera);

					$cnx->commit();
					echo "Empresa actualizado satisfactoriamente";
				}catch(Exception $e){
					echo "Lo sentimos no se efectuaron los cambios en el sistema, intentelo nuevamente";
				}
				break;

		/* case "CAMBIAR_ESTADO_EMPRESA": 
				try{											
					$objEmpresa->cambiarEstadoEmpresa($_POST['id'],$_POST['estado']); 
						echo "Empresa eliminado satisfactoriamente";
				}catch(Exception $e){
					echo "Lo sentimos no se ha podido eliminar empresa, inténtelo nuevamente";
				}
				break; */

		case "GET_NRO_DOCUMENTO":
				$empresa = $objEmpresa->consultarEmpresaRuc($_POST['nro_documento']);
				if($empresa->rowCount()>0){
					$empresa = $empresa->fetch(PDO::FETCH_ASSOC);
    				$empre['idempresa']=$empresa['idempresa'];
    				$empre['tipo_documento']=6;
    				$empre['razon_social']=$empresa['razon_social'];
    				$empre['nombre_comercial']=$empresa['nombre_comercial'];
    				$empre['representante']=$empresa['representante'];
    				$empre['direccion']=$empresa['direccion'];
    				$empre['direccion_alterna']=$empresa['direccion_alterna'];
    				$empre['email']=$empresa['email'];
    				$empre['telfijo']=$empresa['telfijo'];
    				$empre['telcelular']=$empresa['telcelular'];
    				$empre['telotro']=$empresa['telotro'];
					$empre[':nro_trabajadores']=$empresa['nro_trabajadores'];
    				$empre['mision']=$empresa['mision'];
    				$empre['vision']=$empresa['vision'];
    				$empre['observacion']=$empresa['observacion'];
				}else{
					$empre = array();
				}

				echo json_encode($empre);	
				break;

		case "GET_ID_EMPRESA":
		        
			$empresa = $objEmpresa->consultarEmpresaPorID($_POST['idempresa']);
			if($empresa->rowCount()>0){
				$empresa = $empresa->fetch(PDO::FETCH_ASSOC);
				$empresa['ruc']=$empresa['ruc'];
				$empresa['razon_social']=$empresa['razon_social'];
				$empresa['nombre_comercial']=$empresa['nombre_comercial'];
			}else{
				$empresa = array();
			}
			echo json_encode($empresa);	
			break;

		case "EMPRESA_MISION":
		    try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$mision = $_POST['txtMision'];
				$idempresa = $_POST['idempresa'];

				$objCase->actualizarDatoSimple('orgempresa', "mision",  $mision, "idempresa", $idempresa);

				$cnx->commit();
				echo "Misión actualizado de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al modificar Datos. ". $e->getMessage();
			}
			break;

		case "EMPRESA_VISION":
		    try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$vision = $_POST['txtVision'];
				$idempresa = $_POST['idempresa'];

				$objCase->actualizarDatoSimple('orgempresa', "vision",  $vision, "idempresa", $idempresa);

				$cnx->commit();
				echo "Visión actualizado de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al modificar Datos. ". $e->getMessage();
			}
			break;

		case "GUARDAR_LOGO":
			try{
					$idempresa=$_POST['idempresa'];
					$empresa = $objEmpresa->consultarEmpresaPorID($idempresa);
					$empresa = $empresa->fetch(PDO::FETCH_NAMED);

                    $urlbase="../files/logos/".$empresa['razon_social'];
                    if (!file_exists($urlbase) && !is_dir($urlbase)){
                        //mkdir($urlbase);
                        mkdir($urlbase,0777,true);
                    }
					
					
					if(file_exists($urlbase."/IMG_".$idempresa.".JPG")){
						@unlink($urlbase."/IMG_".$idempresa.".JPG");
					}				
					
                    if(isset($_POST['txtFoto'])){
                        $str="data:image/jpeg;base64,"; 
                        $_POST['txtFoto']=str_replace($str,"",$_POST['txtFoto']);
                        file_put_contents($urlbase."/IMG_".$idempresa.".JPG", base64_decode($_POST['txtFoto']));                        		
                    }
                    $objCase->actualizarDatoSimple('orgempresa', 'logo', $empresa['razon_social']."/IMG_".$idempresa.".JPG", "idempresa", $idempresa);
					echo "Logo guardado satisfactoriamente.";
			}catch(Exception $e){
				echo "*** No fue posible guardar, intentelo nuevamente. ".$e->getMessage();
			}
			break;

		case "CAMBIAR_ESTADO_EMPRESA":
			try{
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				$estado = $_POST['estado'];
				$idempresa = $_POST['idempresa'];

				$objCase->actualizarDatoSimple('orgempresa', "estado",  $estado, "idempresa", $idempresa);

				$cnx->commit();
				echo "Datos actualizado de forma satisfactoria.";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Error al modificar Datos. ". $e->getMessage();
			}
			break;
			
		default: 
				echo "***Debe especificar alguna accion"; 
				break;
	}
	
}

?>