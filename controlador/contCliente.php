<?php 
require_once("../logica/clsCliente.php");
require_once("../logica/clsCompartido.php");
require_once("../logica/clsCase.php");
require_once("../phpmailer/PHPMailerAutoload.php");
controlador($_POST['accion']);

function controlador($accion){

	$objPersona=new clsCliente();
	$objCase = new clsCase();

	switch ($accion){
		
		case "NUEVO_CLIENTE": 
				try{	
						$fnacimiento = formatoBDFecha($_POST['fnacimiento']);
						//@Jose Luis || 31-05-2021
						$txtNroOrden = $_POST['txtNroOrden'];
						if ($txtNroOrden == "") {
							$txtNroOrden = NULL;
						}
						$ubigeo_dir_dist = $_POST['ubigeo_dir_dist'];

						if($ubigeo_dir_dist!="" && $txtNroOrden!="" && $txtNroOrden>0){
							$datacliente = $objPersona->getCliente($txtNroOrden, $ubigeo_dir_dist);
							$datacliente = $datacliente->fetchAll(PDO::FETCH_NAMED);
							if (count($datacliente) != 0) {
								throw new Exception("Este número de orden ya fue asignado a un cliente.", 1);
							}
						}
						//FIN
						
						if($fnacimiento==''){
							$fnacimiento=NULL;
						}
						if($_POST['txtLineaCredito']==""){
							$_POST['txtLineaCredito']=0;
						}

						$registro=$objCase->getRowTableFiltroSimple("persona","nro_documento", $_POST['txtNroDocumento'], 'estado', 'N');
						if($registro!=NULL){
		    				$_POST['txtIdCliente']=$registro['idpersona'];
						}
						if($_POST['txtIdCliente']==''){
							//if (strlen($_POST['txtNroDocumento'])==8){
								//@Jose Luis || 31-05-2021
								$objPersona->RegistrarClienteDNI($_POST['txtApellidos'],$_POST['txtNombres'],$_POST['txtNroDocumento'],$_POST['txtEmail'],$_POST['txtDireccion'],$_POST['txtDireccionAl'],$_POST['cboSexo'],$_POST['txtTelefonoFijo'],$_POST['txtTelefonoCelular'],$_POST['txtTelefonoOtro'],$fnacimiento,$_POST['cboRuta'],$_POST['txtLineaCredito'],$_POST['txtObservacion'],$_POST['cboTipoDoc'],$_POST['txtPalabraClave'],$txtNroOrden,$_POST['txtFacebook'],$_POST['medio_comunicacion'],$_POST['txtOcupacion']);
								//FIN
							/*}
							else
							{
								$objPersona->RegistrarClienteRUC($_POST['txtApellidos'],$_POST['txtNombres'],$_POST['txtApellidos'],$_POST['txtNroDocumento'],$_POST['txtEmail'],$_POST['txtDireccion'],$_POST['txtDireccionAl'],$_POST['cboSexo'],$_POST['txtTelefonoFijo'],$_POST['txtTelefonoCelular'],$_POST['txtTelefonoOtro'],$fnacimiento,$_POST['cboRuta'],$_POST['txtLineaCredito'],$_POST['txtObservacion']);
							}*/

							$idcliente = $objCase->getLastIdInsert('persona','idpersona');


							$values = array(
								':idpersona'=>$idcliente,
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
									':idcliente'=>$idcliente,
									':fhregistro_cliente'=>date('Y-m-d H:i:s'),
									':idregistrador_cliente'=>$_SESSION['idpersona']
								);
								$objCase->actualizar('prospecto','idprospecto', $values);
							}
						}else{
							//@Jose Luis || 31-05-2021
							$objPersona->EditarCliente($_POST['txtIdCliente'],$_POST['txtApellidos'],$_POST['txtNombres'],$_POST['txtNroDocumento'],$_POST['txtEmail'],$_POST['txtDireccion'],$_POST['txtDireccionAl'],$_POST['cboSexo'],$_POST['txtTelefonoFijo'],$_POST['txtTelefonoCelular'],$_POST['txtTelefonoOtro'],$fnacimiento,$_POST['cboRuta'],$_POST['txtLineaCredito'],$_POST['txtObservacion'],$_POST['cboTipoDoc'],$_POST['txtPalabraClave'],$txtNroOrden,$_POST['txtFacebook'],$_POST['medio_comunicacion'],$_POST['txtOcupacion']);
							//FIN
							$idcliente = $_POST['txtIdCliente'];

							$values = array(
								':idpersona'=>$idcliente,
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
									':idcliente'=>$idcliente,
									':fhregistro_cliente'=>date('Y-m-d H:i:s'),
									':idregistrador_cliente'=>$_SESSION['idpersona']
								);
								$objCase->actualizar('prospecto','idprospecto', $values);
							}

						}

						echo $idcliente;
				}catch(Exception $e){
					echo "*** Error al registrar Cliente. ". $e->getMessage();
					// echo "Lo sentimos el cliente no ha podido ser registrado, intentelo nuevamente";
				}
				break;	

		case "ACTUALIZAR_CLIENTE": 
				try{
					//@Jose Luis || 31-05-2021
					$txtNroOrden = $_POST['txtNroOrden'];
					$ubigeo_dir_dist = $_POST['ubigeo_dir_dist'];

					if($ubigeo_dir_dist!="" && $txtNroOrden>0){
						$datacliente = $objPersona->getCliente($txtNroOrden, $ubigeo_dir_dist, $_POST['txtIdCliente']);
						$datacliente = $datacliente->fetchAll(PDO::FETCH_NAMED);

						if (count($datacliente) != 0) {
							throw new Exception("Este número de orden ya fue asignado a un cliente.", 1);
						}
					}

					if ($txtNroOrden == "") {
						$txtNroOrden = NULL;
					}
					//FIN
					
					$fnacimiento = formatoBDFecha($_POST['fnacimiento']);
					if($fnacimiento==''){
							$fnacimiento=NULL;
					}
					if($_POST['txtLineaCredito']==""){
						$_POST['txtLineaCredito']=0;
					}
					if($_POST['txtIdCliente']!=''){
						//@Jose Luis || 31-05-2021
						$objPersona->EditarCliente($_POST['txtIdCliente'],$_POST['txtApellidos'],$_POST['txtNombres'],$_POST['txtNroDocumento'],$_POST['txtEmail'],$_POST['txtDireccion'],$_POST['txtDireccionAl'],$_POST['cboSexo'],$_POST['txtTelefonoFijo'],$_POST['txtTelefonoCelular'],$_POST['txtTelefonoOtro'],$fnacimiento,$_POST['cboRuta'],$_POST['txtLineaCredito'],$_POST['txtObservacion'],$_POST['cboTipoDoc'],$_POST['txtPalabraClave'],$txtNroOrden,$_POST['txtFacebook'],$_POST['medio_comunicacion'],$_POST['txtOcupacion']);
						//FIN
							$values = array(
								':idpersona'=>$_POST['txtIdCliente'],
								':ubigeo_dir_dist'=>$_POST['ubigeo_dir_dist'],
								':ubigeo_dir_prov'=>$_POST['ubigeo_dir_prov'],
								':ubigeo_dir_dep'=>$_POST['ubigeo_dir_dep'],
								':ubigeo' => $_POST['ubigeo']
							);
							$objCase->actualizar('persona','idpersona', $values);
					}
					echo "Cliente editado satisfactoriamente";
				}catch(Exception $e){
					echo "*** Error al actualizar Cliente. ". $e->getMessage();
					//echo "Lo sentimos el cliente no ha podido ser actualizado, intentelo nuevamente";
				}
				break;

		case "CAMBIAR_ESTADO_CLIENTE": 
				try{											
					$objPersona->cambiarEstadoCliente($_POST['id'],$_POST['estado']); 
						echo "Cliente eliminado satisfactoriamente";
				}catch(Exception $e){
					echo "Lo sentimos no se ha podido eliminar el cliente, intentelo nuevamente";
				}
				break;



		case "ENVIAR_EMAIL_ESTADO_CUENTA":				
			try {

				//===================ENVIO EMIAL FACTURA=====================
				//
				$idpersona = $_POST["idpersona"];
				
				include_once("../presentacion/pdfFormatoEstadoCuenta.php");

				$pdf->SetAutoPageBreak('auto',2);

				if($emisor){
					$rutabase = "../fe/archivos_xml_sunat/imgqr/produccion/".$emisor['ruc'];
					$ruta_pdf = "../fe/archivos_xml_sunat/imgqr/produccion/".$emisor['ruc']."/ESTADO_CUENTA_CLIENTE_".$idpersona.".pdf";
					if (!file_exists($rutabase) && !is_dir($rutabase)){
                        mkdir($rutabase,0777);
                    }
					@unlink($ruta_pdf);
					$pdf->Output('F',$ruta_pdf);
				}else{
					throw new Exception("No está habilitado el envío de estados de cuenta por correo. Contáctese con el área de Soporte de  TAQINI.", 1);
				}
				//ENVIO DE CORREO
				//
				//@Jose Luis || 23-02-2021
				$permisoCredenciales = false;
				if(getConfig(110)==1){
					$permisoCredenciales = true;
				}
				$subject = 'ESTADO DE CUENTA DE CLIENTE - '.trim($cliente['nombres'].' '.$cliente['apellidos']);
				
				

				if($permisoCredenciales){
					$credenciales = $objCase->getListTableFiltroSimple("mgtabgend","codtabla","057");
					$credenciales = $credenciales->fetchAll(PDO::FETCH_NAMED);

					$datosArray = array();

				    foreach ($credenciales as $key => $value){
				        $datosArray[$value['codelemento']] = $value['descripcion'];
				    }

				    $mail_config = array(
						'SMTPAuth'   => $datosArray['SMTPAuth'],
						'SMTPSecure' => $datosArray['SMTPSecure'],
						'Host' => $datosArray['Host'],
						'Port' => $datosArray['Port'],
						'Username' => $datosArray['Username'],
						'Password' => $datosArray['Password'],
						'From'=>$datosArray['Username'],
						'From2'=>$emisor['razon_social'].' - Estados de Cuenta',
						'Subject' => $subject
					);

				}else{
					//@Jose Luis || 02-03-2021
					global $mail_config;
					$mail_config['From2']=$emisor['razon_social'].' - Estados de Cuenta';
					$mail_config['Subject']=$subject;
					//FIN
				}

				$mail = new PHPMailer();
				$mail->isSMTP();
				$mail->CharSet = 'UTF-8';
				$mail->SMTPAuth   = $mail_config['SMTPAuth'];
				$mail->SMTPSecure = $mail_config['SMTPSecure'];
				$mail->Host = $mail_config['Host'];
				$mail->Port = $mail_config['Port'];
				$mail->Username = $mail_config['Username'];
				$mail->Password = $mail_config['Password'];
				$mail->setFrom($mail_config['From'],$mail_config['From2']);
				$mail->Subject = $mail_config['Subject'];

				$mail->AddAttachment($ruta_pdf);
				
				$email=$_POST['email'];

				if($idpersona!=10){
					$objCase->actualizarDatoSimple('persona','email',$email,'idpersona',$idpersona);	
				}

				$contenido="Estimado cliente adjuntamos en este email su ESTADO DE CUENTA<br/>
				".$subject."<br/>
				Fecha de envío: ".date('d/m/Y')." <br/>
				Cliente: ".trim($cliente['nombres'].' '.$cliente['apellidos'])."<br/>
				Proveedor: ".$emisor['nom_comercial'].' - '.$emisor['razon_social']." <br/>
				<br/>";
				$contenido.="Saludos<br/><br/><br/>";
				$contenido.="--------------------------------------------------------------<br/>";
				$contenido.="Envío de estados de cuenta través de TaqiniSoft<br/>";
				$contenido.="Taqini Technology S.A.C.<br/>";
				$contenido.="www.taqini.pe<br/>";
				$contenido.="979176300 - 956649174<br/>";

				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					$mail->msgHTML($contenido, dirname(__FILE__));
					$mail->AltBody = 'Estimado cliente adjuntamos en este email su ESTADO DE CUENTA:
					'.$subject.'
					Fecha de envío: '.date('d/m/Y').'
					Cliente: '.trim($cliente['nombres'].' '.$cliente['apellidos']).'
					Proveedor: '.$emisor['nom_comercial'].' - '.$emisor['razon_social'].'			


					------------------------------------------------------------
					Envío de estados de cuenta a través de TaqiniSoft
					Taqini Technology S.A.C
					www.taqini.pe
					979176300 - 956649174
					';
					$mail->clearAddresses();
					$mail->addAddress($email);
					if (!$mail->send()) {
						echo " No fue posible enviar estado de cuenta a: " . $email."<BR/>";
					}else{
						echo " Se envió email satisfactoriamente a: " . $email."<BR/>";
					}
					
				}else{
					echo " Correo no válido: " . $email.", por favor brinde otro correo<BR/>";
				}


				echo "PROCESO TERMINADO";
			} catch (Exception $e) {
				echo "NO FUE POSIBLE ENVIAR EMAIL: ".$e->getMessage();
			}
			break;

		//@Jose Luis || 31-05-2021
		case "ORDENAR_CLIENTE":				
			try {
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
				$cnx->beginTransaction();

				$data_distritos = $objPersona->getDistritos();
				while ($fila=$data_distritos->fetch(PDO::FETCH_NAMED)){
					$pos = 1;
					$data_orden = $objPersona->getDataClienteOrden($fila['ubigeodist']);
					while ($fila02=$data_orden->fetch(PDO::FETCH_NAMED)){
						$objCase->actualizarDatoSimple('persona', 'orden_atencion', $pos, 'idpersona', $fila02['idpersona']);
						$pos++;
					}
				}

				$cnx->commit();		
				echo "Datos actualizados satisfactoriamente";
			} catch (Exception $e){
				echo "***NO FUE POSIBLE ACTUALIZAR: ".$e->getMessage();
			}

			break;
			
		case "REGISTRAR_ORDEN":				
			try {
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
				$cnx->beginTransaction();
				$iddistrito=$_POST['iddistrito'];
				$nro_orden=$_POST['nro_orden'];
				$idpersona=$_POST['idpersona'];

				if($iddistrito!="" && $nro_orden>0){
					$existe_orden = $objPersona->verificarOrdenClienteByDistrito($iddistrito, $nro_orden);
					if ($existe_orden>0) {
						throw new Exception("Este número de orden ya fue asignado a un cliente.", 1);
					}
					
				}

				$objCase->actualizarDatoSimple('persona', 'orden_atencion', $nro_orden, 'idpersona', $idpersona);

				$cnx->commit();		
				echo "Datos actualizados satisfactoriamente";
			} catch (Exception $e){
				echo "***NO FUE POSIBLE ACTUALIZAR: ".$e->getMessage();
			}
			break;

		case "ACTUALIZAR_ORDENAR_CLIENTE":				
			try {
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
				$cnx->beginTransaction();
				$iddistrito=$_POST['iddistrito'];
			
				$pos = 1;
				$data_orden = $objPersona->getDataClienteOrden($iddistrito);
				while ($fila02=$data_orden->fetch(PDO::FETCH_NAMED)){
					$objCase->actualizarDatoSimple('persona', 'orden_atencion', $pos, 'idpersona', $fila02['idpersona']);
					$pos++;
				}

				$cnx->commit();		
				echo "Datos actualizados satisfactoriamente";
			} catch (Exception $e){
				echo "***NO FUE POSIBLE ACTUALIZAR: ".$e->getMessage();
			}
		break;

		case 'NUEVO_DATO_CONFIGURACION':
			try {
				$values = $objPersona->getColumnTablaPersona_dato_configuracion();
				$values[':nombre']=$_POST['nombre'];
				$values[':tipo']=$_POST['tipo'];
				$values[':valor']=$_POST['valor'];
				$values[':opcion']=$_POST['opcion'];
				$objCase->insertarWithoutUpper('persona_dato_configuracion', $values);
				echo 'Datos registrados satisfactoriamente';
			} catch (Exception $e) {
				echo "***Los sentimos, datos no pudieron ser registrados";
			}
			break;

		case "ACTUALIZA_DATO_CONFIGURACION":
			try {
				$values = $objPersona->getColumnTablaPersona_dato_configuracion();
				$values[':idcampo']=$_POST['idcampo'];
				$values[':nombre']=$_POST['nombre'];
				$values[':tipo']=$_POST['tipo'];
				$values[':valor']=$_POST['valor'];
				$values[':opcion']=$_POST['opcion'];
				$objCase->actualizarWithoutUpper('persona_dato_configuracion','idcampo',$values);

				echo 'Datos actualizados satisfactoriamente';
			} catch (Exception $e) {
				echo "***Los sentimos, datos no pudieron ser actualizados";
			}
		break;

		case 'ELIMINAR_DATO_CONFIGURACIÓN':
		    try {
		    	$objCase->actualizarDatoSimple('persona_dato_configuracion','estado', 'E','idcampo', $_POST['idcampo']);
				echo 'Datos actualizados satisfactoriamente';
			} catch (Exception $e) {
				echo "***Los sentimos, datos no pudieron ser actualizados";
			}

		break;
		
		case 'REGISTRAR_ANTECEDENTE':
		    //registrar
            try{
            	global $cnx;
            	$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            	$cnx->beginTransaction();
            	$idmovimiento=$_POST['idmovimiento'];
            	$idpersona=$_POST['idpersona'];
            	$campos = $objCase->getListTableFiltroSimple("persona_dato_configuracion",'estado','N');
            	$campos = $campos->fetchAll(PDO::FETCH_NAMED);
            	$ultimoregistro= $objCase->getLastRowTableFiltroSimple('persona_dato_adicional','idpersona',$idpersona,'estado','N','','','','','idantecedente','desc');
            	if ( is_null($ultimoregistro)) {
            	        $ultimoregistro=1;  
            	}else{
            		$ultimoregistro=$ultimoregistro['idantecedente']+1;

            	}
            	//actuaizar
            	if ($_POST['editar']=='EDITAR') {
            		$campos = $objCase->getListTableFiltroSimple("persona_dato_adicional",'idpersona',$idpersona,'idmovimiento',$idmovimiento,'estado','N');
            		$campos = $campos->fetchAll(PDO::FETCH_NAMED);
            		$objCase->CambiarEstado('persona_dato_adicional','E','idmovimiento',$idmovimiento);
            		$ultimoregistro=$_POST['idantecedente'];
            	}
            	//fin
            	
            	$adicional = $objPersona->getRowWithoutValuesDatosAdicionales();
            	
            	$adicional[':idpersona']=$idpersona;
            	$adicional[':idmovimiento']=$idmovimiento;
            	$adicional[':idantecedente']=$ultimoregistro;
            	foreach ($campos as $k => $v) {
            		$adicional[':idcampo']=$v['idcampo'];
            		$adicional[':tipo']=$v['tipo'];
            		$adicional[':nombre']=$v['nombre'];
            		if ($v['tipo']=='char' ) {
            			if (isset($_POST[scanear_string($v['nombre'])])) {
            				$adicional[':valor']='S';
            			}else{
            				$adicional[':valor']='N';
            			}
            		}elseif ($v['tipo']=='date') {
            			$adicional[':valor']=formatoBDFecha($_POST[scanear_string($v['nombre'])]);
            		}elseif ($v['tipo']=='multiselect') {
            			if(isset($_POST[scanear_string($v['nombre'])]) && $_POST[scanear_string($v['nombre'])]!=""){
            			$adicional[':valor']=implode(",",$_POST[scanear_string($v['nombre'])]);
            			}else{
            				$adicional[':valor']="";
            			}
            		}else{
                      $adicional[':valor']=$_POST[scanear_string($v['nombre'])];
                    }
                    if ($v['tipo']=='select'||$v['tipo']=='multiselect') {
                    	$adicional[':opcion']=$_POST["txtopcion".scanear_string($v['nombre'])];
                    }
                    $objCase-> insertarWithoutUpper('persona_dato_adicional',$adicional);
                }
                $cnx->commit();
              	echo "Datos Registrados satisfactoriamente";
			}catch(Exception $e){
				$cnx->rollBack();
				echo "*** Los sentimos, datos no pudieron ser registrados".$e->getMessage();
			}


		break;

		default: 
				echo "Debe especificar alguna accion"; 
		break;
	}
	
}
?>