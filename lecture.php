<?php
session_start();
 include_once('convenience_functions.php');
    $db = openDB();
//include_once('convenience_functions.php');

// On définit le nombre de série dans la séquence
	if(!isset($_SESSION['serie'])){
		$_SESSION['serie']=1;
	}
	else {
		$_SESSION['serie']=$_SESSION['serie']+1;
	}

	
/*Ouverture du fichier en lecture seule*/
$dir=$_SESSION['examen'];
$serie='serie_'.$_SESSION['serie'];
$fichier='lecture_serial.txt';

// Création du dossier série
	if(!is_dir($dir.'/'.$serie)){
   mkdir($dir.'/'.$serie);
	}

//$path=$dir.'/'.$serie.'/'.$fichier; //chemin d'accès au fichier à utiliser quand l'enregistrement du signal fonctionnera
$path=$fichier; // ligne a supprimer quand l'enregistrement signal fonctionnera
$path_img=$dir.'/'.$serie.'/images';

// Création dossier image

if(!is_dir($path_img)){
   mkdir($path_img);
}
$date=date("Y-m-d");

// INCREMENTER study_aquisitionsInStudy 
$studySQL = $db->prepare('UPDATE Study 
SET study_aquisitionsInStudy=(study_aquisitionsInStudy)+1 
WHERE patient_insee='.$_SESSION['patient_insee'].'
AND DATE_FORMAT(study_datetime, "%Y-%m-%d")="'.$date.'";');

$studySQL->execute();
$studySQL->closeCursor();

// récuperer l'id de l'étude actuelle
$sql1="Select study_id from Study where study_datetime=(SELECT MAX(study_datetime) FROM Study)";
$info=$db->query($sql1);
$data = $info->fetch();
$idStudy=$data[0];



// EXCTRACTION DATE HEURE
 $date_UID= new DateTime("NOW");
 $years_UID = $date_UID->format('Ymd');
 $hours_UID = $date_UID->format('H');
 $minutes_UID = $date_UID->format('i');
 $seconds_UID = $date_UID->format('s');

//// AJOUT DE LA SERIE DANS LA BASE
$serieSQL=$db->prepare('INSERT INTO Serie (
	serie_seriesDescription,
	serie_imagesInAcquisition,
	serie_serieDatetime,
	serie_acquisitionDatetime,
	serie_SeriesInstanceUID,
	serie_frameOfReferenceUID,
	serie_mediaStorageSOPClassUID,
	serie_SOPClassUID,
	serie_referencedSOPClassUID,
	study_id
	)
	VALUES (
	:description,
	:nbImages,
	:time,
	:time,
	:instanceUID,
	:frameRefUID,
	:media,
	:sopclass,
	:refsop,
	:study
	);'
	);

		$_SESSION['serie_UID']=$_SESSION['org_root'].'2.'.$years_UID.'.'.$hours_UID.$minutes_UID.$seconds_UID;
	
$serieSQL->execute(array(
	'description'=>'12',
	'nbImages'=>0,
	'time'=>date("Y-m-d H:i:s"),
	'instanceUID'=>$_SESSION['serie_UID'],
	'frameRefUID'=>$_SESSION['study_UID'],
	'media'=>'1.2.840.10008.1.3.10',
	'sopclass'=>'1.2.840.10008.1.3.10',
	'refsop'=>$_SESSION['study_UID'],
	'study'=>$idStudy

	)
	

);

$serieSQL->closeCursor();
	

//récupérer id serie actuelle : 
$sql2="Select serie_instanceNumber from Serie where serie_serieDatetime=(SELECT MAX(serie_serieDatetime) from Serie)";
$info2=$db->query($sql2);
$data2 = $info2->fetch();
$idSerie=$data2[0];

//récupérer l'id de la dernière image créée pour eviter les doublons

$maxSQL="SELECT max(image_reference) from image";
$infoMax=$db->query($maxSQL);
$dataMax = $infoMax->fetch();
$cptTest=$dataMax[0]+1;


	


$handle = fopen($path, 'r');
//$tab = array();
$c=1;
$nb_donnees=50; // Image = 5secondes 

/*Si on a réussi à ouvrir le fichier*/
if ($handle)
{
	
	/*Tant que l'on est pas à la fin du fichier*/
	while (!feof($handle))
	{
		
		${'tab'.$c} = array(); 
		
		for($d=0;$d<$nb_donnees;$d++){
			
		/*On lit la ligne courante*/
		$buffer = fgets($handle);
		array_push(${'tab'.$c},(int)$buffer);
		/*On l'affiche*/
		}
		$c++;
	}
	/*On ferme le fichier*/
	fclose($handle);
}

unset($handle);

for($e=1;$e<$c;$e++){
	
	$taille_tab=count(${'tab'.$e});
$val_max=max(${'tab'.$e});
$val_min=min(${'tab'.$e});



//header("Content-type: image/png");


// Variable pour l'etalement de l'affichage : 
$etal=5;
$ratio_max=$val_max/12;

$imgWidth=$taille_tab*$etal;
$imgHeight=(($val_max+10)*2);

// Créer l'image et définir les couleurs
$image=imagecreate($imgWidth, $imgHeight);
$background_color = imagecolorallocate($image, 0, 0, 0);
$colorWhite=imagecolorallocate($image, 255, 255, 255);
$colorgreen=imagecolorallocate($image,0,255,0);

// Créer un graphique en courbes
for ($i=0; $i<$taille_tab-1; $i++){
imageline($image, $i*$etal, (($imgHeight/2)-(${'tab'.$e}[$i])), ($i+1)*$etal, (($imgHeight/2)-(${'tab'.$e}[$i+1])), $colorWhite);
}
// On recommence pour l'épaisseur
for ($i=0; $i<$taille_tab-1; $i++){
imageline($image, $i*$etal, (($imgHeight/2)-(${'tab'.$e}[$i]))-1, ($i+1)*$etal, (($imgHeight/2)-(${'tab'.$e}[$i+1])-1), $colorWhite);
}
// encore
for ($i=0; $i<$taille_tab-1; $i++){
imageline($image, $i*$etal-1, (($imgHeight/2)-(${'tab'.$e}[$i]))-1, ($i+1)*$etal-1, (($imgHeight/2)-(${'tab'.$e}[$i+1])-1), $colorWhite);
}
imageline($image,0,($val_max+10),$imgWidth,($val_max+10), $colorgreen);
imageline($image,0,($val_max+9),$imgWidth,($val_max+9), $colorgreen);

$texte1="Valeur Min=".$val_min;
$texte2="Valeur Max=".$val_max;


//imagepng($image);
//imagejpeg($image, 'dossier/image_originale'.$e.'.jpeg', 100);
$im2=imagecreate(512, 512);
imagecopyresampled($im2,$image,0,0,0,0,512,512,$imgWidth,$imgHeight);

//imagestring($im2, 1, 10, 20,$texte1, $colorgreen);
//imagestring($im2, 1, 10, 10,$texte2, $colorgreen);

imagejpeg($im2, $path_img.'/image_resampled'.$e.'.jpeg', 100);
unset($etal,$ratio_max,$imgWidth,$imgHeigth,${'tab'.$e},$taille_tab);
imagedestroy($image);
imagedestroy($im2);




$imageSQL = $db->prepare('INSERT INTO Image(
	image_reference,
	image_samplesPerPixel,
	image_samplesPerPixelUsed,
	image_photometricInterpretation,
	image_rows,
	image_columns,
	image_bitsAllocated,
	image_bitsStored,
	image_pixelRepresentation,
	image_windowCenter,
	image_windowWidth,
	image_waveformDisplayBkgCIELabValue,
	image_channelRecommendDisplayCIELabValue,
	image_numericValue,
	image_imageFrameOrigin,
	image_annotationSequence,
	image_unformattedTextValue,
	image_graphicLayerDescription,
	image_overlayRows,
	image_overlayColumns,
	image_overlayDescription,
	image_overlayType,
	image_overlayOrigin,
	image_overlayBitsAllocated,
	image_overlayBitPosition,
	image_overlayData,
	image_pixelData,
	image_referencedImageSequence,
	image_mediaStorageSOPInstanceUID,
	image_SOPInstanceUID,
	image_referencedSOPInstanceUID,
	image_link,
	serie_instanceNumber
	)
	VALUES (
	:reference,
	:samplesPixel,
	:samplesPixelUsed,
	:photometricInterp,
	:rows,
	:columns,
	:bitsalloc,
	:bitsstored,
	:pixelrep,
	:windowcenter,
	:windowwidth,
	:bckg,
	:colocourb,
	:numvalue,
	:frameorigin,
	:annotsequence,
	:untxtvalue,
	:graphlayerdesc,
	:overlayrows,
	:overlaycolumn,
	:overlaydesc,
	:overlaytype,
	:overlayorigin,
	:overlaybitsall,
	:overlaybitpos,
	:overlaydata,
	:pixdata,
	:refimgsequence,
	:mediastorage,
	:sopinstance,
	:refsopinstance,
	:link,
	:instancenum
	);'
);

$path2=$path_img.'/image_resampled'.$e.'.jpeg';
$cptTest=$cptTest+1;
$imageSQL->execute(array(
	'reference'=>$cptTest,
	'samplesPixel'=>'1',
	'samplesPixelUsed'=>'1',
	'photometricInterp'=>'LAB',
	'rows'=>'512',
	'columns'=>'512',
	'bitsalloc'=>'8', /// Number of bits allocated for each pixel sample. Each sample shall have the same number of bits allocated. See PS 3.5 for further explanation.
	'bitsstored'=>'2097152', /// Number of bits stored for each pixel sample. Each sample shall have the same number of bits stored. See PS 3.5 for further explanation.
	'pixelrep'=>'0000H', /// 0028,0103
	'windowcenter'=>'200',
	'windowwidth'=>'50',
	'bckg'=>'black',
	'colocourb'=>'white',
	'numvalue'=>'0', //// 0040,A30A Numeric value for this name-value Item. Only a single value shall be present.Required if Value Type (0040,A040) is NUMERIC.
	'frameorigin'=>'1', //// 60xx,0051
	'annotsequence'=>'test', /// Frame number of Multi-frame Image to which this overlay applies; frames are numbered from 1.
	'untxtvalue'=>'test', // 0070,0006
	'graphlayerdesc'=>$texte1.''.$texte2, // min + max
	'overlayrows'=>'512',
	'overlaycolumn'=>'512',
	'overlaydesc'=>'IMAGE ET Image Graphic',
	'overlaytype'=>'Graphic (G)',
	'overlayorigin'=>'1/1',
	'overlaybitsall'=>'1',
	'overlaybitpos'=>'0',
	'overlaydata'=>'1', ///Overlay pixel data. The order of pixels sent for each overlay is left to right, top to bottom, i.e., 
						//the upper left pixel is sent first followed by the remainder of the first row, followed by the first pixel of the 2nd row, 
						//then the remainder of the 2nd row and so on.Overlay data shall be contained in this Attribute.
	'pixdata'=>'0', //// A data stream of the pixel samples that comprise the Image. See Section C.7.6.3.1.4 for further explanation. => tous les pixels
	'refimgsequence'=>'test', // reference sequence
	'mediastorage'=>'1.2.840.10008.1.3.10',
	'sopinstance'=>$_SESSION['org_root'].'3.'.$years_UID.'.'.$hours_UID.$minutes_UID.$seconds_UID.'.'.$cptTest,
	'refsopinstance'=>$_SESSION['serie_UID'],
	'link'=>$path2,
	'instancenum'=>$idSerie
)
);

$imageSQL->closeCursor();

//// UPDATE SERIE -> IMG +1
// -> Prendre l'ID le plus recent de la liste par étude

$serieSQL = $db->prepare('UPDATE Serie 
SET serie_imagesInAcquisition=(serie_imagesInAcquisition)+1 
WHERE serie_instanceNumber='.$idSerie.';');

$serieSQL->execute();

$serieSQL->closeCursor();


	
}
?>
<!DOCTYPE html>
<html>
 <head>
        <title>DICOM - acquisition</title>
        <link rel="stylesheet" href="./UI_acquisition.css" />
        <meta charset="utf-8" />
		<style>
				body{
						font-family:Arial,Helvetica,sans-serif;
						font-size:small;
						background:linear-gradient(to bottom, DarkGray, DimGray);
						background-repeat: no-repeat;
						background-attachment: fixed;
					}
		</style>
	</head>
<body>
<?php


// On affiche les images

for($e=1;$e<$c;$e++){

echo "\n";

	${'rendu'.$e} = $path_img.'/image_resampled'.$e.'.jpeg';

print '<img src="'.${'rendu'.$e}.'" height="250" width="250" >  ';

	
}


	/// AJOUT DU SIGNAL DANS LA BASE
/*
	$signalSQL=$db->prepare('INSERT INTO Signal(
		signal_waveformOriginality,
		signal_numberOfWaveformChannel,
		signal_numberOfWaveformSamples,
		signal_samplingFrequency,
		signal_channelDefinitionSequence,
		signal_waveformChannelNumber,
		signal_channelLabel,
		signal_channelStatut,
		signal_channelSourcesSequence,
		signal_channelSensitivityCorrection,
		signal_channelBaseline,
		signal_channelOfset,
		signal_waveformBitStored,
		signal_notchFilterBandwitdh,
		signal_measurementUnit,
		signal_waveformSequence,
		serie_instanceNumber
		
	));
*/

?>
	<form>
		<input type="button" value="Retour" onclick="history.go(-1)" class="sauvegarder">
	</form>
</body>
</html>
