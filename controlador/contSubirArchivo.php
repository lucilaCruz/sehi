<?php 
require_once("../logica/clsCase.php");
require_once("../logica/clsCompartido.php");
require_once("../logica/clsEmpresa.php");
require_once("../logica/clsPersona.php");

controlador($_POST['accion']);

function controlador($accion){

	$objCase = new clsCase;
	$objEmpresa = new clsEmpresa;
	$objPersona = new clsPersona;

	switch ($accion){
		
		/* PROCEDIMIENTO ACTIVIDAD */
		case "CARGAR_PROCEDIMIENTO_ACTIVIDAD":
			try{	
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				if (empty($_FILES)) {
					echo json_encode(['error'=>'No files found for upload.']); 
					return;
				}
				$directory="";
				foreach($_FILES as $k=>$v){
					$directory=$k;
				}

				$nivel=0;
				if(isset($_POST['nivel'])){
					$nivel = $_POST['nivel'];
				}

				$idactividad="";
				if(isset($_POST['idactividad'])){
					$idactividad = $_POST['idactividad'];
				}

				$actividad = $objCase->getRowTableFiltroSimple('orgactividad', 'idactividad', $idactividad, 'estado', 'N');

				if(!file_exists("../files/actividades/".$actividad['nombre'])){ 
					mkdir("../files/actividades/".$actividad['nombre'], 0777, true); //crear directory si no existe
				}

				$url = 'files/actividades/'.$actividad['nombre'];
				$images = $_FILES[$directory];
				$success = null;
				$paths= [];
				$filenames = $images['name'];

				for($i=0; $i < count($filenames); $i++){
					$ext = explode('.', basename($filenames[$i]));
					$target = "..".DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR . $images['name'][$i];
					if(move_uploaded_file($images['tmp_name'][$i], $target)) {
						$success = true;
						$paths[] = $target;
						$valuesCabecera = $objEmpresa->getColumnTablaMultimediaProcedimientoActividad();
						$valuesCabecera[':idactividad']=$idactividad;
						$valuesCabecera[':input']=$directory;
						$valuesCabecera[':link']='files/actividades/'.$actividad['nombre'].'/'.$images['name'][$i];
						$valuesCabecera[':archivo']=$images['name'][$i];
						$valuesCabecera[':descripcion']='';
						$valuesCabecera[':fecha']=$_POST['fecha'];
						$valuesCabecera[':hora']=date("h:i a");
						$valuesCabecera[':cod_user']=$_POST['coduser'];
						$objCase->insertarWithoutUpper('multimedia_procedimiento_actividad', $valuesCabecera);
					} else {
						$success = false;
						break;
					}
				}

				if ($success === true) {
					$output = [];

				} elseif ($success === false) {
					$output = ['error'=>'Error while uploading images. Contact the system administrator'];    
					foreach ($paths as $file) {
						unlink($file);
					}
				} else {
					$output = ['error'=>'No files were processed.'];
				}

				$cnx->commit();
				echo json_encode($output);

			}catch(Exception $e){
				echo "*** Error al cargar Data. ". $e->getMessage();
			}
			break;

		case "BORRAR_PROCEDIMIENTO_ACTIVIDAD":
			try{

				$idprocedimiento_actividad=$_POST['idarchivo'];
				$estado=$_POST['estado'];

				$archivo = $objCase->getRowTableFiltroSimple('multimedia_procedimiento_actividad', 'idprocedimiento_actividad', $idprocedimiento_actividad, 'estado', 'N');

				$urlbase="../".$archivo['link'];
				if(file_exists($urlbase)){
					@unlink($urlbase);
				}
				$objCase->actualizarDatoSimple('multimedia_procedimiento_actividad', 'estado', $estado, "idprocedimiento_actividad", $idprocedimiento_actividad);

				echo "Archivo Borrado de forma satisfactoria.";
			}catch(Exception $e){
				echo "*** Error al borrar el archivo. ". $e->getMessage();
			}


		/* CONSENTIMIENTO */
		case "CARGAR_CONSENTIMIENTO":
			try{	
				global $cnx;
				$cnx->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$cnx->beginTransaction();

				if (empty($_FILES)) {
					echo json_encode(['error'=>'No files found for upload.']); 
					return;
				}
				$directory="";
				foreach($_FILES as $k=>$v){
					$directory=$k;
				}

				$nivel=0;
				if(isset($_POST['nivel'])){
					$nivel = $_POST['nivel'];
				}

				$idtrabajador=0;
				if(isset($_POST['idtrabajador'])){
					$idtrabajador = $_POST['idtrabajador'];
				}

				$persona = $objCase->getRowTableFiltroSimple('persona', 'idpersona', $idtrabajador, 'estado', 'N');

				if(!file_exists("../files/cosentimientos/".$persona['nro_documento'])){ 
					mkdir("../files/cosentimientos/".$persona['nro_documento'], 0777, true); //crear directory si no existe
				}

				$url = 'files/cosentimientos/'.$persona['nro_documento'];
				$images = $_FILES[$directory];
				$success = null;
				$paths= [];
				$filenames = $images['name'];

				for($i=0; $i < count($filenames); $i++){
					$ext = explode('.', basename($filenames[$i]));
					$target = "..".DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR . $images['name'][$i];
					if(move_uploaded_file($images['tmp_name'][$i], $target)) {
						$success = true;
						$paths[] = $target;
						$valuesCabecera = $objPersona->getColumnTablaMultimediaConsentimiento();
						$valuesCabecera[':idtrabajador']=$idtrabajador;
						$valuesCabecera[':input']=$directory;
						$valuesCabecera[':link']='files/cosentimientos/'.$persona['nro_documento'].'/'.$images['name'][$i];
						$valuesCabecera[':archivo']=$images['name'][$i];
						$valuesCabecera[':descripcion']='';
						$valuesCabecera[':fecha']=$_POST['fecha'];
						$valuesCabecera[':hora']=date("h:i a");
						$valuesCabecera[':cod_user']=$_POST['coduser'];
						$objCase->insertarWithoutUpper('multimedia_consentimiento', $valuesCabecera);
					} else {
						$success = false;
						break;
					}
				}

				if ($success === true) {
					$output = [];

				} elseif ($success === false) {
					$output = ['error'=>'Error while uploading images. Contact the system administrator'];    
					foreach ($paths as $file) {
						unlink($file);
					}
				} else {
					$output = ['error'=>'No files were processed.'];
				}

				$cnx->commit();
				echo json_encode($output);

			}catch(Exception $e){
				echo "*** Error al cargar Data. ". $e->getMessage();
			}
			break;

		case "BORRAR_CONSENTIMIENTO":
			try{

				$idconsentimiento=$_POST['idarchivo'];
				$estado=$_POST['estado'];

				$archivo = $objCase->getRowTableFiltroSimple('multimedia_consentimiento', 'idconsentimiento', $idconsentimiento, 'estado', 'N');

				$urlbase="../".$archivo['link'];
				if(file_exists($urlbase)){
					@unlink($urlbase);
				}
				$objCase->actualizarDatoSimple('multimedia_consentimiento', 'estado', $estado, "idconsentimiento", $idconsentimiento);

				echo "Archivo Borrado de forma satisfactoria.";
			}catch(Exception $e){
				echo "*** Error al borrar el archivo. ". $e->getMessage();
			}

        default:
            echo "***Debe especificar alguna accion";
            break;
	}
	
}

?>