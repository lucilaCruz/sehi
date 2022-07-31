<?php 
require_once("../logica/clsCase.php");
require_once("../logica/clsCompartido.php");
controlador($_POST['accion']);

function controlador($accion){

	$obj=new clsCase();
	switch ($accion){
		
		case "NUEVO": 
				try{
					$idopcion=$_POST['idoptx'];
					$opcionMenu = $obj->getRowTableById("opcion",$idopcion);
					$table=$opcionMenu['table'];


					//VALIDACIONES DUPLICADOS
					$validar_duplicados = $obj->getListTableFiltroSimple('sis_case_mant','idopcion',$idopcion, 'tabla',$table, 'validarduplicado',1 ,'estado','N');

					while($fila = $validar_duplicados->fetch(PDO::FETCH_NAMED)){
						if(isset($_POST[$fila['columna']])){
							if($_POST[$fila['columna']]!=''){					
								$duplicado = $obj->getRowTableFiltroSimple($table, $fila['columna'],$_POST[$fila['columna']],'estado','N');
								if(!$duplicado){
									$duplicado = $obj->getRowTableFiltroSimple($table, $fila['columna'],$_POST[$fila['columna']],'estado','A');
								}
									if($duplicado){
										if($duplicado['estado']!='E'){
											$nombrecampo = $fila['columna'];
											if($fila['alias']){
												$nombrecampo = $fila['alias'];
											}

											throw new Exception("<br/><strong>El campo [".$nombrecampo."] con valor [".$_POST[$fila['columna']]."] ya existe en otro registro y no puede duplicarse</strong>");	
										}
									}
								}
						}	
					}
					//FIN VALIDACION DUPLICADO

					//DETECTAR CAMPOS QUE RESPETEN MINUSCULAS
						$resp_minuscula = array();
						$listado_resp_minus = $obj->getListTableFiltroSimple('sis_case_mant','idopcion',$idopcion, 'tabla',$table, 'respetarminuscula',1 ,'estado','N');
						while($fila = $listado_resp_minus->fetch(PDO::FETCH_NAMED)){
							$resp_minuscula[]=$fila['columna'];
						}

					//FIN DETECTAR CAMPOS QUE RESPETEN MINUSCULAS

					$campos = $obj->getCampos($table);
					$campos = $campos->fetchAll(PDO::FETCH_NAMED);
					$parametros=array();
					$values=array();
					$pk="";
					foreach($campos as $k=>$v){
						if($v['Field']=='estado'){
							if($_POST[$v['Field']]==''){
								$_POST[$v['Field']]='N';
							}
						}

						if($v['Key']=="PRI"){
							$pk=$v['Field'];
						}

						//@Antonio Fuentes Alcantara
						if($v['Field']==$pk){
							if($_POST[$v['Field']]==''){
								$_POST[$v['Field']]=NULL;
							}
						}

						$tipo = $v['Type'];
						$pos_par = strrpos($tipo, "(");
						if($pos_par>0){
							$tipo = substr($tipo,0,$pos_par);
						}

						if(!isset($_POST[$v['Field']])){
							$_POST[$v['Field']] = null;
						}

						//lucila cruz ||08-03-2021
						if (is_array($_POST[$v['Field']])) {
							$_POST[$v['Field']]=implode(",",$_POST[$v['Field']]);
						}
                        //fin

						if(in_array($tipo, array('int','smallint','decimal','double'))){
							if($_POST[$v['Field']]==''){
								$_POST[$v['Field']]=0;
							}							
						}

						$values[':'.$v['Field']] = $_POST[$v['Field']];

						if(in_array($tipo, array('date','datetime'))){
							if($_POST[$v['Field']]==''){
								$_POST[$v['Field']]=NULL;
							}
							//@Antonio Fuentes Alcantara
							if(strlen($_POST[$v['Field']])>10){
								$values[':'.$v['Field']]=$_POST[$v['Field']];
							}else{
								$values[':'.$v['Field']]=formatoBDFecha($_POST[$v['Field']]);
							}
						}

						if(in_array($tipo, array('char','varchar','text','textarea'))){
							if(in_array($v['Field'], $resp_minuscula)){
								$parametros[]='TRIM(:'.$v['Field'].')';
							}else{
								$parametros[]='UPPER(TRIM(:'.$v['Field'].'))';
							}
						}else{
							$parametros[]=':'.$v['Field'];
						}
					}					

					if(count($resp_minuscula)>0){
						$obj->insertarWithoutUpper($table, $values, $parametros);
					}else{
						$obj->insertar($table, $values, $parametros);
					}

					$_POST[$pk]=$obj->getLastIdInsert($table, $pk);
					//para el caso de datos calculados
					//@Jose Luis || 12-10-2021
					$calculados = $obj->getListTableFiltroSimple('sis_case_mant_calculado','idopcion',$idopcion, 'tabla',$table,'estado','N',"accion","actualizar","orden");
					if($calculados->rowCount()>0){
						while($fila = $calculados->fetch(PDO::FETCH_NAMED)){
							if($fila['tipo_calculo']=="SCRIPT"){
								$campos_calculo = explode(",", $fila['campos_procesar']);
								$values = array();
								$faltan_parametros=false;
								foreach ($campos_calculo as $v) {
									if(isset($_POST[$v])){
										$values[':'.$v]=$_POST[$v];
									}else{
										$faltan_parametros=true;
										break;
									}
								}
								if(!$faltan_parametros){
									$obj->ejecucionSimpleParametrosSQL($fila['script_sql'], $values);
								}
							}
						}
					}

					echo "Datos registrados satisfactoriamente";
						
				}catch(Exception $e){
					echo "***Lo sentimos no se completó el registro, inténtelo nuevamente. ".$e->getMessage();
				}
				break;
				
		case "ACTUALIZAR": 
				try{
					$idopcion=$_POST['idoptx'];
					$opcionMenu = $obj->getRowTableById("opcion",$idopcion);
					$table=$opcionMenu['table'];

					$campos = $obj->getCampos($table);
					$campos = $campos->fetchAll(PDO::FETCH_NAMED);
					$parametros=array();
					$values=array();
					$pk="";
					

					//DETECTAR CAMPOS QUE RESPETEN MINUSCULAS
						$resp_minuscula = array();
						$listado_resp_minus = $obj->getListTableFiltroSimple('sis_case_mant','idopcion',$idopcion, 'tabla',$table, 'respetarminuscula',1 ,'estado','N');
						while($fila = $listado_resp_minus->fetch(PDO::FETCH_NAMED)){
							$resp_minuscula[]=$fila['columna'];
						}

					//FIN DETECTAR CAMPOS QUE RESPETEN MINUSCULAS


					foreach($campos as $k=>$v){
						$tipo = $v['Type'];
						$pos_par = strrpos($tipo, "(");
						if($pos_par>0){
							$tipo = substr($tipo,0,$pos_par);
						}

						if($v['Key']=="PRI"){
							$pk=$v['Field'];
						}else{
							if(in_array($tipo, array('char','varchar','text','textarea'))){
								if(in_array($v['Field'], $resp_minuscula)){
									$parametros[]='`'.$v['Field'].'`=TRIM(:'.$v['Field'].')';
								}else{
									$parametros[]='`'.$v['Field'].'`=UPPER(TRIM(:'.$v['Field'].'))';
								}								
							}else{
								$parametros[]='`'.$v['Field'].'`=:'.$v['Field'];
							}
						}
						
						if(in_array($tipo, array('int','smallint','decimal','double'))){
							if($_POST[$v['Field']]==''){
								$_POST[$v['Field']]=0;
							}							
						}

						if(!isset($_POST[$v['Field']])){
							$_POST[$v['Field']] = null;
						}

						//lucila cruz ||08-03-2021
						if (is_array($_POST[$v['Field']])) {
							$_POST[$v['Field']]=implode(",",$_POST[$v['Field']]);
						}
                        //fin

						$values[':'.$v['Field']]=$_POST[$v['Field']];

						if(in_array($tipo, array('date','datetime'))){
							if($_POST[$v['Field']]==''){
								$_POST[$v['Field']]=NULL;
							}
							//@Antonio Fuentes Alcantara
							if(strlen($_POST[$v['Field']])>10){
								$values[':'.$v['Field']]=$_POST[$v['Field']];
							}else{
								$values[':'.$v['Field']]=formatoBDFecha($_POST[$v['Field']]);
							}
						}						
					}


					//VALIDACIONES DUPLICADOS
					$validar_duplicados = $obj->getListTableFiltroSimple('sis_case_mant','idopcion',$idopcion, 'tabla',$table, 'validarduplicado',1 ,'estado','N');

					while($fila = $validar_duplicados->fetch(PDO::FETCH_NAMED)){
						if(isset($_POST[$fila['columna']])){
							if($_POST[$fila['columna']]!=''){					
								$duplicado = $obj->getRowTableFiltroSimple($table, $fila['columna'],$_POST[$fila['columna']],'estado','N');
								if(!$duplicado){
									$duplicado = $obj->getRowTableFiltroSimple($table, $fila['columna'],$_POST[$fila['columna']],'estado','A');
								}
									if($duplicado){
										if($duplicado['estado']!='E' && $duplicado[$pk]!=$_POST[$pk]){
											$nombrecampo = $fila['columna'];
											if($fila['alias']){
												$nombrecampo = $fila['alias'];
											}

											throw new Exception("<br/><strong>El campo [".$nombrecampo."] con valor [".$_POST[$fila['columna']]."] ya existe en otro registro y no puede duplicarse</strong>");	
										}
									}
								}
						}	
					}
					//FIN VALIDACION DUPLICADO

					if(count($resp_minuscula)>0){
						$obj->actualizarWithoutUpper($table, $pk, $values, $parametros);
					}else{
						$obj->actualizar($table, $pk, $values, $parametros);				
					}	

					//para el caso de datos calculados
					//@Jose Luis || 12-10-2021
					$calculados = $obj->getListTableFiltroSimple('sis_case_mant_calculado','idopcion',$idopcion, 'tabla',$table,'estado','N','accion','actualizar');
					if($calculados->rowCount()>0){
						while($fila = $calculados->fetch(PDO::FETCH_NAMED)){
							if($fila['tipo_calculo']=="SCRIPT"){
								$campos_calculo = explode(",", $fila['campos_procesar']);
								$values = array();
								$faltan_parametros=false;
								foreach ($campos_calculo as $v) {
									if(isset($_POST[$v])){
										$values[':'.$v]=mb_strtoupper($_POST[$v]);
									}else{
										$faltan_parametros=true;
										break;
									}
								}
								if(!$faltan_parametros){
									$obj->ejecucionSimpleParametrosSQL($fila['script_sql'], $values);
								}
							}
						}
					}

					echo "Datos actualizados satisfactoriamente";
				}catch(Exception $e){
					echo "***Lo sentimos cambios no registrados, intentelo nuevamente".$e->getMessage();
				}
				break;		

		case "CAMBIAR_ESTADO": 
				try{

					$obj->cambiarEstado($_POST['table'],$_POST['estado'], $_POST['pk'], $_POST['pkvalue']);
					//@Jose Luis || 12-10-2021
					$data = $obj->getListTableFiltroSimple('sis_case_mant_calculado','idopcion',$_POST['idoptx'], 'tabla',$_POST['table'],'estado','N',"accion","eliminar","orden");
					if($data->rowCount()>0){
						while($fila = $data->fetch(PDO::FETCH_NAMED)){
							if($fila['tipo_calculo']=="SCRIPT"){
								$campos_calculo = explode(",", $fila['campos_procesar']);
								$values = array();
								$faltan_parametros=false;
								foreach ($campos_calculo as $v) {
									if($_POST['pk'] == $v){
										$values[':'.$v]=$_POST['pkvalue'];
									}else{
										$faltan_parametros=true;
										break;
									}
								}
								if(!$faltan_parametros){
									$obj->ejecucionSimpleParametrosSQL($fila['script_sql'], $values);
								}
							}
						}
					}
					echo "Datos actualizados satisfactoriamente";
				}catch(Exception $e){
					echo "***Lo sentimos registro no ha podido ser atualizado, intentelo nuevamente";
				}
				break;

		case "UPDATE_DATO_SIMPLE": 
				try{
					$table=$_POST['table'];

					$campos = $obj->getCampos($table);
					$campos = $campos->fetchAll(PDO::FETCH_NAMED);
					$parametros=array();
					$values=array();
					$pk="";
					
					foreach($campos as $k=>$v){
						$tipo = $v['Type'];
						$pos_par = strrpos($tipo, "(");
						if($pos_par>0){
							$tipo = substr($tipo,0,$pos_par);
						}
						if(isset($_POST[$v['Field']])){
							if($v['Key']=="PRI"){
								$pk=$v['Field'];
							}else{							
								if(in_array($tipo, array('char','varchar','text','textarea'))){
									$parametros[]=$v['Field'].'=UPPER(TRIM(:'.$v['Field'].'))';
								}else{
									$parametros[]=$v['Field'].'=:'.$v['Field'];
								}
							}
		
							if(in_array($tipo, array('int','smallint','decimal','double'))){
								if($_POST[$v['Field']]==''){
									$_POST[$v['Field']]=0;
								}							
							}

							$values[':'.$v['Field']]=$_POST[$v['Field']];					

							if(in_array($tipo, array('date','datetime'))){
								if($_POST[$v['Field']]==''){
									$_POST[$v['Field']]=NULL;
								}
								//@Antonio Fuentes Alcantara
								if(strlen($_POST[$v['Field']])>10){
									$values[':'.$v['Field']]=$_POST[$v['Field']];
								}else{
									$values[':'.$v['Field']]=formatoBDFecha($_POST[$v['Field']]);
								}
							}	
						}					
					}

					if($pk==''){
						throw new Exception("No ha definido el código principal del registro", 1);					
					}

					if(count($parametros)==0 || count($values)==0){
						throw new Exception("No se ha definido correctamente los datos a actualizar", 1);	
					}

					$obj->actualizar($table, $pk, $values, $parametros);

					echo "Datos actualizados satisfactoriamente";
				}catch(Exception $e){
					echo "***Lo sentimos registro no ha sido actualizado. ".$e->getMessage();
				}
				break;

		case "COMBO": 
				try{
					$table = $_POST['table'];
					$campos = $obj->getCampos($table);
					$campos = $campos->fetchAll(PDO::FETCH_NAMED);
					$filtro=array();
					$columnas=array();
					$pk="";
					$columna_ver="";
					if(isset($_POST['columna_ver'])){
						$columna_ver=$_POST['columna_ver'];
					}
					foreach($campos as $k=>$v){
						if(isset($_POST[$v['Field']])){
								$v['Value']=$_POST[$v['Field']];
								$filtro[]=$v;
						}

						if($v['Key']=='PRI'){
							$columnas = array($v['Field']=>$v) + $columnas;
							$pk=$v['Field'];
						}else{
							if($columna_ver=="" && $k==1){
								$columnas[$v['Field']]=$v;
							}else{
								if($columna_ver==$v['Field']){
									$columnas[$v['Field']]=$v;
								}
							}

						}						
					}					
					$data=$obj->getList($table, $columnas, $filtro, 0, 0, false, true);
					if(isset($_POST["tipo_ut"])){
						if($_POST["tipo_ut"]=='SI'){
							echo "<option value='0'>- Todos -</option>";
						}
					}
					if(isset($_POST["tipo_us"])){
						if($_POST["tipo_us"]=='SI'){
							echo "<option value='0'>- Seleccion uno -</option>";
						}
					}					
					while($fila=$data->fetch(PDO::FETCH_NUM)){
						echo "<option value='".$fila[0]."'>".$fila[1]."</option>";
					}
				}catch(Exception $e){
					echo "***Lo sentimos, datos no pudieron ser obtenidos";
				}
				break;									
		
		case "ROW_VALUE": 
				try{
					$table = $_POST['table'];
					$campos = $obj->getCampos($table);
					$campos = $campos->fetchAll(PDO::FETCH_NAMED);
					$filtro=array();
					$columnas=array();
					$pk="";

					foreach($campos as $k=>$v){
						if(isset($_POST[$v['Field']])){
								$v['Value']=$_POST[$v['Field']];
								$filtro[]=$v;
						}
						if($v['Key']=='PRI'){
							$columnas = array($v['Field']=>$v) + $columnas;
							$pk=$v['Field'];
						}						
					}
					$data=$obj->getList($table, $columnas, $filtro, 0, 0, false, true);
					$resultado=array();
					if($fila=$data->fetch(PDO::FETCH_NAMED)){
						$resultado=$fila;
					}
					echo json_encode($resultado);
				}catch(Exception $e){
					echo "***Los sentimos, datos no pudieron ser obtenidos";
				}
				break;

		case "LIST_TABLE": 
				try{
					$table = $_POST['table'];
					$campos = $obj->getCampos($table);
					$campos = $campos->fetchAll(PDO::FETCH_NAMED);
					$filtro=array();
					$columnas=array();
					$pk="";

					foreach($campos as $k=>$v){
						if(isset($_POST[$v['Field']])){
								$v['Value']=$_POST[$v['Field']];
								$filtro[]=$v;
						}
						if($v['Key']=='PRI'){
							$columnas = array($v['Field']=>$v) + $columnas;
							$pk=$v['Field'];
						}						
					}
					$data=$obj->getList($table, $columnas, $filtro, 0, 0, false, true);
					$resultado=$data->fetchAll(PDO::FETCH_NAMED);
					echo json_encode($resultado);
				}catch(Exception $e){
					echo "***Los sentimos, datos no pudieron ser obtenidos";
				}
				break;

		case "SIMPLE_VALUE": 
				try{
					$table = $_POST['table'];
					$campos = $obj->getCampos($table);
					$campos = $campos->fetchAll(PDO::FETCH_NAMED);
					$filtro=array();
					$columnas=array();
					$pk="";
					$columna_ver="";
					if(isset($_POST['columna_ver'])){
						$columna_ver=$_POST['columna_ver'];
					}
					foreach($campos as $k=>$v){
						if(isset($_POST[$v['Field']])){
								$v['Value']=$_POST[$v['Field']];
								$filtro[]=$v;
						}

						if($v['Key']=='PRI'){
							$columnas = array($v['Field']=>$v) + $columnas;
							$pk=$v['Field'];
						}else{
							if($columna_ver=="" && $k==1){
								$columnas[$v['Field']]=$v;
							}else{
								if($columna_ver==$v['Field']){
									$columnas[$v['Field']]=$v;
								}
							}

						}						
					}					
					$data=$obj->getList($table, $columnas, $filtro, 0, 0, false, true);
					$resultado="";
					if($fila=$data->fetch(PDO::FETCH_NUM)){
						$resultado=$fila[1];
					}
					echo $resultado;
				}catch(Exception $e){
					echo "***Lo sentimos, datos no pudieron ser obtenidos";
				}
				break;	

		case "VERIFY_DUPLICATE": 
				try{
					$table = $_POST['table'];
					$campos = $obj->getCampos($table);
					$campos = $campos->fetchAll(PDO::FETCH_NAMED);
					$filtro=array();
					$columnas=array();
					$pk="";
					$columna_ver="";
					if(isset($_POST['columna_ver'])){
						$columna_ver=$_POST['columna_ver'];
					}
					foreach($campos as $k=>$v){
						if(isset($_POST[$v['Field']])){
								$v['Value']=$_POST[$v['Field']];
								$filtro[]=$v;
						}

						if($v['Key']=='PRI'){
							$columnas = array($v['Field']=>$v) + $columnas;
							$pk=$v['Field'];
						}else{
							if($columna_ver=="" && $k==1){
								$columnas[$v['Field']]=$v;
							}else{
								if($columna_ver==$v['Field']){
									$columnas[$v['Field']]=$v;
								}
							}

						}						
					}					
					$data=$obj->getList($table, $columnas, $filtro, 0, 0, false, true);
					$duplicado=0;
					if($data->rowCount()>0){
						$duplicado=1;
					}
					echo $duplicado;
				}catch(Exception $e){
					echo "***Lo sentimos, datos no pudieron ser obtenidos";
				}
				break;	

		case "NUEVO_REGISTRO_RAPIDO": 
				try{
					$table=$_POST['tabla'];
					$campo=$_POST['campo'];
					$codigoauto=$_POST['codigoauto'];
					$_POST[$campo]=$_POST["txtRegistroRapido"];

					$campos = $obj->getCampos($table);
					$campos = $campos->fetchAll(PDO::FETCH_NAMED);
					$parametros=array();
					$values=array();
					$pk="";
					$mensajeextra="";
					if(boolval($codigoauto)){
						$correlativo = $obj->getRowTableFiltroSimple("mgcorr","codsis","AL","codarch","001");
						$correlativo['correlativo']=$correlativo['correlativo']+1;
						$obj->actualizarDatoSimple("mgcorr", "correlativo", $correlativo["correlativo"], "codsis", "AL", "codarch","001");
						$_POST["codigobarra"]="M".$correlativo['correlativo'];
						$mensajeextra="<br/><center><h3>CODIGO GENERADO:</h3><h1>".$_POST["codigobarra"]."</h1></center>";
					}
					foreach($campos as $k=>$v){
						if($v['Field']=='estado'){
							$_POST[$v['Field']]='N';
						}

						if($v['Key']=="PRI"){
							$pk=$v['Field'];
						}

						$tipo = $v['Type'];
						$pos_par = strrpos($tipo, "(");
						if($pos_par>0){
							$tipo = substr($tipo,0,$pos_par);
						}

						if(isset($_POST[$v['Field']])){
							if(in_array($tipo, array('int','smallint','decimal','double'))){
								if($_POST[$v['Field']]==''){
									$_POST[$v['Field']]=0;
								}
							}
							$values[':'.$v['Field']]=$_POST[$v['Field']];
						}else{
							$values[':'.$v['Field']]=NULL;
						}

						if(in_array($tipo, array('char','varchar','text','textarea'))){
							$parametros[]='UPPER(:'.$v['Field'].')';
						}else{
							$parametros[]=':'.$v['Field'];
						}
					}
					
					$obj->insertar($table, $values, $parametros);
					$idinserted=$obj->getLastIdInsert($table, $pk);

					$resultado["mensaje"]="Datos registrados satisfactoriamente".$mensajeextra;
					$resultado["id"]=$idinserted;
					$resultado["text"]=$_POST["txtRegistroRapido"];
					echo json_encode($resultado);

				}catch(Exception $e){
					echo "***Lo sentimos no se completó el registro, inténtelo nuevamente ".$e->getMessage();
				}
				break;

		case "CODIGO_AUTOMATICO": 
				try{
					$codsis=$_POST['codsis'];
					$codarch=$_POST['codarch'];
					$prefijo="";
					if(isset($_POST['prefijo'])){
						$prefijo=$_POST['prefijo'];
					}
					$correlativo = $obj->getRowTableFiltroSimple("mgcorr","codsis",$codsis,"codarch",$codarch);
					$correlativo['correlativo']=$correlativo['correlativo']+1;
					$obj->actualizarDatoSimple("mgcorr", "correlativo", $correlativo["correlativo"], "codsis", $codsis, "codarch",$codarch);
					$codigogenerado=$prefijo.$correlativo['correlativo'];
					echo $codigogenerado;
				}catch(Exception $e){
					echo "***Lo sentimos no se completó el registro, inténtelo nuevamente ".$e->getMessage();
				}
				break;

		case "LAST_REGISTER": 
				try{
					$table = $_POST['table'];
					$pk = $_POST['pk'];
					$cantidad = $_POST['cantidad'];					
					$data=$obj->getLastRegiter($table, $pk, $cantidad);
					$resultado = $data->fetchAll(PDO::FETCH_NAMED); 
					echo json_encode($resultado);
				}catch(Exception $e){
					echo "***Los sentimos, datos no pudieron ser obtenidos";
				}
				break;
		//*lucila*/
		case "CONTROL_REGISTRO":
                try {
                $idopcion=$_POST['idopcion'];
                $table=$_POST['table'];
                $columnas=array();
		        $filtro=array();

		      	$nroRegistros=$obj->getList($table, $columnas, $filtro,0,12, true,false,true,true);
                $opcion = $obj->getLastRowTableFiltroSimple("opcion", "idopcion", $idopcion);
                if ($nroRegistros<$opcion['nro_registro']) {
                    echo "NUEVO";
                 }
		      } catch (Exception $e) {
		      	
		      }

		        break;


		default: 
				echo "*** Debe especificar alguna accion"; 
				break;
	}
	
}

?>