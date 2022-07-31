<?php
require_once("../logica/clsArticulo.php");
require_once("../logica/clsCompartido.php");
require_once("../logica/clsCase.php");
require_once("../logica/clsMovimientoAlmacen.php");
require_once("../logica/clsImagen.php");

controlador($_POST['accion']);

function controlador($accion){
	$objArt=new clsArticulo();
	$objCase = new clsCase;
	$objAlm = new clsMovimientoAlmacen;
	$objImagen = new clsImagen;

	switch ($accion){
		
		case "BUSQUEDA_RAPIDA": 
				$data=array();
				try{
					$producto = str_replace(" ","%",$_POST['query']);
                    $data = $objArt->busquedaProductoRapido($producto, $_POST['idalmacen']);
                    $data = $data->fetchAll(PDO::FETCH_NAMED);
				}catch(Exception $e){
					//nada
				}
				echo json_encode($data);
				break;

		case "CAMBIOS_RAPIDOS": 
				try{
					$idarticulo=$_POST['idarticulo'];
					$codigobarra=$_POST['codigobarra'];
					$nombre=$_POST['nombre'];
					$pcompra=$_POST['pcompra'];
					$pventa=$_POST['pventa'];
					$pminimo=$_POST['pminimo'];
					$pmaximo=$_POST['pmaximo'];
					$peso=$_POST['peso'];
					$pmargen = $_POST['pmargen'];
					$pmargenmin = $_POST['pmargenmin'];
					$moneda = "SOL";

					if(isset($_POST['moneda'])){
						$moneda = $_POST['moneda'];
					}

					if($moneda=="SOL"){
						$objArt->actualizarDatosRapidos($idarticulo, $codigobarra, $nombre, $pcompra, $pventa, $pminimo, $pmaximo,$peso, $pmargen, $pmargenmin);
					}else{
						$objArt->actualizarDatosRapidosMe($idarticulo, $codigobarra, $nombre, $pcompra, $pventa, $pminimo, $pmaximo,$peso, $pmargen, $pmargenmin);
					}

					$objArt->actualizar_pcompra_combo($idarticulo);
					
					//nada
					echo "Datos actualizados";
				}catch(Exception $e){
					echo "*** ERROR al actualizar. ".$e->getMessage();
					//nada
				}
				break;
				
		//@Antonio Fuentes Alcantara	
		case "CAMBIOS_ALPRECIO": 
			try{
				$idprecio = $_POST['idprecio'];
				$idalmacen = $_POST['idalmacen'];
				$idarticulo=$_POST['idarticulo'];
				$codigobarra=$_POST['codigobarra'];
				$nombre=$_POST['nombre'];
				$pcompra=$_POST['pcompra'];
				$pventa=$_POST['pventa'];
				$pminimo=$_POST['pminimo'];
				$pmaximo=$_POST['pmaximo'];
				$peso=$_POST['peso'];
				$pmargen=$_POST['pmargen'];
				$pmargenmin=$_POST['pmargenmin'];

				$moneda = "SOL";
				$sufijomoneda = "";

				if(isset($_POST["moneda"])){
					$moneda = $_POST["moneda"];
				}

				if($moneda!="SOL"){
					$sufijomoneda = "me";
				}

				$objCase->actualizarDatoSimple('alarticulo','codigobarra', $codigobarra,'idarticulo', $idarticulo);
				$objCase->actualizarDatoSimple('alarticulo','nombre', $nombre,'idarticulo', $idarticulo);
				$objCase->actualizarDatoSimple('alarticulo','pcompra'.$sufijomoneda, $pcompra,'idarticulo', $idarticulo);
				$objCase->actualizarDatoSimple('alarticulo','pesokg', $peso,'idarticulo', $idarticulo);

				$objCase->actualizarDatoSimple('alarticulo','pmargen', $pmargen,'idarticulo', $idarticulo);
				$objCase->actualizarDatoSimple('alarticulo','pmargenmin', $pmargenmin,'idarticulo', $idarticulo);

				$valuesPrecio = $objCase->getRowTableFiltroSimple('alprecio', 'idarticulo', $idarticulo, 'idalmacen',$idalmacen,"estado","N");

				if(isset($valuesPrecio)){
					$idprecio = $valuesPrecio['idprecio'];
					$objCase->actualizarDatoSimple('alprecio','pventa'.$sufijomoneda, $pventa,'idprecio', $idprecio);
					$objCase->actualizarDatoSimple('alprecio','pcompra'.$sufijomoneda, $pcompra,'idprecio', $idprecio);
					$objCase->actualizarDatoSimple('alprecio','pminimo'.$sufijomoneda, $pminimo,'idprecio', $idprecio);
					$objCase->actualizarDatoSimple('alprecio','pmaximo'.$sufijomoneda, $pmaximo,'idprecio', $idprecio);										
				}else{
					$valuesPrecio=$objAlm->getRowWithoutValuesPrecio();
					$valuesPrecio[':idarticulo']=$idarticulo;
					$valuesPrecio[':idalmacen']=$idalmacen;
					$valuesPrecio[':pventa'.$sufijomoneda]=$pventa;
					$valuesPrecio[':pcompra'.$sufijomoneda]=$pcompra;
					$valuesPrecio[':pminimo'.$sufijomoneda]=$pminimo;
					$valuesPrecio[':pmaximo'.$sufijomoneda]=$pmaximo;
					$objCase->insertar('alprecio', $valuesPrecio);
				}
				

				$grupoAlmacen = $objCase->getRowTableFiltroSimple('algrupoalmacen', 'idalmacen', $idalmacen, 'estado', 'N');
				if($grupoAlmacen){
					$filaGrupo = $objCase->getListTableFiltroSimple('algrupoalmacen','grupo',$grupoAlmacen['grupo'],'estado','N');
					$filaGrupo = $filaGrupo->fetchAll(PDO::FETCH_NAMED);

					foreach ($filaGrupo as $key => $value) {
						if($value['idalmacen']!=$idalmacen){

							$valuesPrecio = $objCase->getRowTableFiltroSimple('alprecio', 'idarticulo',$idarticulo, 'idalmacen',$value['idalmacen']);

							if(isset($valuesPrecio)){
								$objCase->actualizarDatoSimple('alprecio','pventa'.$sufijomoneda, $pventa,'idprecio', $valuesPrecio['idprecio']);
								$objCase->actualizarDatoSimple('alprecio','pminimo'.$sufijomoneda, $pminimo,'idprecio', $valuesPrecio['idprecio']);
								$objCase->actualizarDatoSimple('alprecio','pmaximo'.$sufijomoneda, $pmaximo,'idprecio', $valuesPrecio['idprecio']);
								$objCase->actualizarDatoSimple('alprecio','pcompra'.$sufijomoneda, $pcompra,'idprecio', $valuesPrecio['idprecio']);
									
							}else{

								$valuesPrecio=$objAlm->getRowWithoutValuesPrecio();
								$valuesPrecio[':idarticulo']=$idarticulo;
								$valuesPrecio[':idalmacen']=$value['idalmacen'];
								$valuesPrecio[':pventa'.$sufijomoneda]=$pventa;
								$valuesPrecio[':pminimo'.$sufijomoneda]=$pminimo;
								$valuesPrecio[':pmaximo'.$sufijomoneda]=$pmaximo;
								$valuesPrecio[':pcompra'.$sufijomoneda]=$pcompra;
								$objCase->insertar('alprecio', $valuesPrecio);
							}
						}
					}

				}
				//nada
				echo "Datos actualizados";
			}catch(Exception $e){
				echo "*** ERROR al actualizar. ".$e->getMessage();
				//nada
			}
			break;
			

		case "GUARDAR_PRECIO_LOTE": 
			try{
				$idalmacen = $_POST['idalmacen'];
				$moneda = "SOL";
				$sufijomoneda = "";

				if(isset($_POST["moneda"])){
					$moneda = $_POST["moneda"];
				}

				if($moneda!="SOL"){
					$sufijomoneda = "me";
				}

				$datos = json_decode($_POST['datos'],true);
				foreach ($datos as $k => $v) {
					
					$idarticulo=$v['idarticulo'];
					$codigobarra=$v['codigobarra'];
					$nombre=$v['nombre'];
					$pcompra=$v['pcompra'];
					$pventa=$v['pventa'];
					$pminimo=$v['pminimo'];
					$pmaximo=$v['pmaximo'];
					$peso=$v['peso'];
					$pmargen=$v['pmargen'];
					$pmargenmin=$v['pmargenmin'];

					if($idalmacen==0){

						if($moneda=="SOL"){
							$objArt->actualizarDatosRapidos($idarticulo, $codigobarra, $nombre, $pcompra, $pventa, $pminimo, $pmaximo,$peso, $pmargen, $pmargenmin);
						}else{
							$objArt->actualizarDatosRapidosMe($idarticulo, $codigobarra, $nombre, $pcompra, $pventa, $pminimo, $pmaximo,$peso, $pmargen, $pmargenmin);
						}

						$objArt->actualizar_pcompra_combo($idarticulo);
					}else{


						$objCase->actualizarDatoSimple('alarticulo','codigobarra', $codigobarra,'idarticulo', $idarticulo);
						$objCase->actualizarDatoSimple('alarticulo','nombre', $nombre,'idarticulo', $idarticulo);
						$objCase->actualizarDatoSimple('alarticulo','pcompra'.$sufijomoneda, $pcompra,'idarticulo', $idarticulo);
						$objCase->actualizarDatoSimple('alarticulo','pesokg', $peso,'idarticulo', $idarticulo);

						$objCase->actualizarDatoSimple('alarticulo','pmargen', $pmargen,'idarticulo', $idarticulo);
						$objCase->actualizarDatoSimple('alarticulo','pmargenmin', $pmargenmin,'idarticulo', $idarticulo);

						$valuesPrecio = $objCase->getRowTableFiltroSimple('alprecio', 'idarticulo', $idarticulo, 'idalmacen',$idalmacen,"estado","N");

						if(isset($valuesPrecio)){
							$idprecio = $valuesPrecio['idprecio'];
							$objCase->actualizarDatoSimple('alprecio','pventa'.$sufijomoneda, $pventa,'idprecio', $idprecio);
							$objCase->actualizarDatoSimple('alprecio','pcompra'.$sufijomoneda, $pcompra,'idprecio', $idprecio);
							$objCase->actualizarDatoSimple('alprecio','pminimo'.$sufijomoneda, $pminimo,'idprecio', $idprecio);
							$objCase->actualizarDatoSimple('alprecio','pmaximo'.$sufijomoneda, $pmaximo,'idprecio', $idprecio);										
						}else{
							$valuesPrecio=$objAlm->getRowWithoutValuesPrecio();
							$valuesPrecio[':idarticulo']=$idarticulo;
							$valuesPrecio[':idalmacen']=$idalmacen;
							$valuesPrecio[':pventa'.$sufijomoneda]=$pventa;
							$valuesPrecio[':pcompra'.$sufijomoneda]=$pcompra;
							$valuesPrecio[':pminimo'.$sufijomoneda]=$pminimo;
							$valuesPrecio[':pmaximo'.$sufijomoneda]=$pmaximo;
							$objCase->insertar('alprecio', $valuesPrecio);
						}
						

						$grupoAlmacen = $objCase->getRowTableFiltroSimple('algrupoalmacen', 'idalmacen', $idalmacen, 'estado', 'N');
						if($grupoAlmacen){
							$filaGrupo = $objCase->getListTableFiltroSimple('algrupoalmacen','grupo',$grupoAlmacen['grupo'],'estado','N');
							$filaGrupo = $filaGrupo->fetchAll(PDO::FETCH_NAMED);

							foreach ($filaGrupo as $key => $value) {
								if($value['idalmacen']!=$idalmacen){

									$valuesPrecio = $objCase->getRowTableFiltroSimple('alprecio', 'idarticulo',$idarticulo, 'idalmacen',$value['idalmacen']);

									if(isset($valuesPrecio)){
										$objCase->actualizarDatoSimple('alprecio','pventa'.$sufijomoneda, $pventa,'idprecio', $valuesPrecio['idprecio']);
										$objCase->actualizarDatoSimple('alprecio','pminimo'.$sufijomoneda, $pminimo,'idprecio', $valuesPrecio['idprecio']);
										$objCase->actualizarDatoSimple('alprecio','pmaximo'.$sufijomoneda, $pmaximo,'idprecio', $valuesPrecio['idprecio']);
										$objCase->actualizarDatoSimple('alprecio','pcompra'.$sufijomoneda, $pcompra,'idprecio', $valuesPrecio['idprecio']);
											
									}else{

										$valuesPrecio=$objAlm->getRowWithoutValuesPrecio();
										$valuesPrecio[':idarticulo']=$idarticulo;
										$valuesPrecio[':idalmacen']=$value['idalmacen'];
										$valuesPrecio[':pventa'.$sufijomoneda]=$pventa;
										$valuesPrecio[':pminimo'.$sufijomoneda]=$pminimo;
										$valuesPrecio[':pmaximo'.$sufijomoneda]=$pmaximo;
										$valuesPrecio[':pcompra'.$sufijomoneda]=$pcompra;
										$objCase->insertar('alprecio', $valuesPrecio);
									}
								}
							}

						}
					}
				}
				//nada
				echo "Datos actualizados";
			}catch(Exception $e){
				echo "*** ERROR al actualizar. ".$e->getMessage();
				//nada
			}
			break;


		case "GUARDAR_FOTO":
			try{
					$idarticulo=$_POST['idarticulo'];
					$carpeta = $_SESSION['emp_nombrebd'];
                    $urlbase="../files/imagenes/".$carpeta;
                    if (!file_exists($urlbase) && !is_dir($urlbase)){
                        mkdir($urlbase);
                        mkdir($urlbase."/articulos",0777);
                    }
					
					if(file_exists($urlbase."/articulos/IMG_".$idarticulo.".JPG")){
						@unlink($urlbase."/articulos/IMG_".$idarticulo.".JPG");
					}				
					
                    if(isset($_POST['txtFoto'])){
                        $str="data:image/jpeg;base64,"; 
                        $_POST['txtFoto']=str_replace($str,"",$_POST['txtFoto']);
                        file_put_contents($urlbase."/articulos/IMG_".$idarticulo.".JPG", base64_decode($_POST['txtFoto']));                        		
                    }
                    $objCase->actualizarDatoSimple('alarticulo', 'imagen', "articulos/IMG_".$idarticulo.".JPG", "idarticulo", $idarticulo);
					echo "IMAGEN GUARDADA SATISFACTORIAMENTE";
			}catch(Exception $e){
				echo "*** No fue posible guardar, intentelo nuevamente. ".$e->getMessage();
			}
			break;

		case "CONSULTAR_SERIE":
		       try {
		       	  $serie=$_POST['serie'];
		       	  $data=$objArt->consultarMoviminetoXSerie($serie);
		       	  $data = $data->fetchAll(PDO::FETCH_NAMED);
		       	  $resultado=array();
					
					foreach ($data as $key => $v) {
						if ($v['tipo']=='VENTA') {
							$resultado['tipoV']=$v['tipo'];
							$resultado['producto']=$v['nombre'];
							$resultado['fecha']=formatoCortoFecha($v['fecha']);
							$resultado['cliente']=$v['nombrecliente'];
							$resultado['documento']= $v['tipo_documento'].' '.$v['serie_documento'].'-'.$v['nro_documento'];
						}else{
							$resultado['fechaC']=formatoCortoFecha($v['fecha']);
							$resultado['tipoC']=$v['tipo'];
							$resultado['proveedor']=$v['nombrecliente'];
							$resultado['documentoC']= $v['tipo_documento'].' '.$v['serie_documento'].'-'.$v['nro_documento'];
						}
					}
					echo json_encode($resultado);

		       } catch (Exception $e) {
		       	    echo "***Los sentimos, datos no pudieron ser obtenidos";
		       }
		    break;
		//@Jose Luis || 12-07-2021
		case "NUEVO_DATO_ADICIONAL":
			try {
				$values = $objArt->getColumnTablaArticuloDetalle();
				$values[':idarticulo']=$_POST['idarticulo'];
				$values[':nombre']=$_POST['nombre'];
				$values[':tipo']=$_POST['tipo'];
				$values[':valor']=$_POST['valor'];
				$objCase->insertarWithoutUpper('alarticulo_detalle', $values);
				//guardarAlarticulo_detalle
				echo 'Datos registrados satisfactoriamente';
			} catch (Exception $e) {
				echo "***Los sentimos, datos no pudieron ser registrados";
			}
			break;

		case "ACTUALIZA_DATO_ADICIONAL":
			try {

				$objCase->actualizarDatoSimple('alarticulo_detalle','nombre', $_POST['nombre'],'idarticulodetalle', $_POST['idarticulodetalle']);
				$objCase->actualizarDatoSimple('alarticulo_detalle','tipo', $_POST['tipo'],'idarticulodetalle', $_POST['idarticulodetalle']);
				$objCase->actualizarDatoSimple('alarticulo_detalle','valor', $_POST['valor'],'idarticulodetalle', $_POST['idarticulodetalle']);

				echo 'Datos actualizados satisfactoriamente';
			} catch (Exception $e) {
				echo "***Los sentimos, datos no pudieron ser actualizados";
			}
			break;

		case "ELIMINAR_DATO_ADICIONAL":
			try {
				$objCase->actualizarDatoSimple('alarticulo_detalle','estado', 'E','idarticulodetalle', $_POST['idarticulodetalle']);

				echo 'Datos actualizados satisfactoriamente';
			} catch (Exception $e) {
				echo "***Los sentimos, datos no pudieron ser actualizados";
			}
			break;

		case "COPIAR_DATOS_ADICIONALES":
			try {
				$idarticuloDestino = $_POST['idarticuloDestino'];
				$idarticuloInicio = $_POST['idarticuloInicio'];
				
				$objCase->actualizarDatoSimple('alarticulo_detalle','estado', 'E','idarticulo', $idarticuloDestino);

				$dataAdicional = $objCase->getListTableFiltroSimple("alarticulo_detalle","idarticulo",$idarticuloInicio, "estado","N");
				while($fila = $dataAdicional->fetch(PDO::FETCH_NAMED)){
					$values = $objArt->getColumnTablaArticuloDetalle();
					$values[':idarticulo']=$idarticuloDestino;
					$values[':nombre']=$fila['nombre'];
					$values[':tipo']=$fila['tipo'];
					$values[':valor']=$fila['valor'];
					$objCase->insertarWithoutUpper('alarticulo_detalle', $values);
				}

				echo 'Datos actualizados satisfactoriamente';
			} catch (Exception $e) {
				echo "***Los sentimos, datos no pudieron ser actualizados";
			}
			break;
			//FIN
		default: 
				echo "Debe especificar alguna accion"; 
				break;
	}
	
	
}


?>