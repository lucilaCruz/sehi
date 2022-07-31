<?php
require_once("../logica/clsImagen.php");
$objImagen = new clsImagen();
if (empty($_FILES)) {
   echo json_encode(['error'=>'No files found for upload.']); 
   return;
}
$directory="";
foreach($_FILES as $k=>$v){
	$directory=$k;
}

$images = $_FILES[$directory];
$success = null;
$paths= [];
$filenames = $images['name'];
if(count($filenames)>1){
	for($i=0; $i < count($filenames); $i++){
		$ext = explode('.', basename($filenames[$i]));
		$target = "..".DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR .str_replace("__",DIRECTORY_SEPARATOR,$directory). DIRECTORY_SEPARATOR . $images['name'][$i];
		if(move_uploaded_file($images['tmp_name'][$i], $target)) {
			$success = true;
			$paths[] = $target;
		} else {
			$success = false;
			break;
		}
	}
}else{
        $nombreFile= $images['name'];
        if(isset($_GET['idmat'])){
            $nombreFile="IMG_".$_GET['idmat'].".JPG";    
        }
    
		$target = "..".DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR .str_replace("__",DIRECTORY_SEPARATOR,$directory) . DIRECTORY_SEPARATOR . $nombreFile;
		if(move_uploaded_file($images['tmp_name'], $target)) {
			$success = true;
			$paths[] = $target;
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

echo json_encode($output);
?>