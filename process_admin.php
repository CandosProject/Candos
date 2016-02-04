<?php
session_start();
include_once('convenience_functions.php');

if(!isset($_GET['page']) || !is_numeric($_GET['page']))
{
    header('Location:admin.php');
    exit;
}

if($_GET['page'] == 1)
{
    $db = openDB();
    if(isset($_POST['add-nom-site']))
    {
        writeSite($db, $_POST['nom-site'], $_POST['adresse']);
    }
    else if(isset($_POST['add-adresse-site']))
    {
        //TODO
    }
    else if(isset($_POST['add-operateur']))
    {
        writeOperateur($db, $_POST['operateur']);
    }
    else if(isset($_POST['add-prescripteur']))
    {
        writePrescripteur($db, $_POST['prescripteur']);
    }
    else if(isset($_POST['add-realisateur']))
    {
        writeRealisateur($db, $_POST['realisateur']);
    }
    else if(isset($_POST['add-position-examen']))
    {
        writePosture($db, $_POST['position-examen']);
    }
    else if(isset($_POST['add-activite-examen']))
    {
        writeAnatomicOrientation($db, $_POST['activite-examen']);
    }
    else if(isset($_POST['add-localisation-examen']))
    {
        writeBodypart($db, $_POST['localisation-examen'], $_POST['localisation-examen']);
    }
	
    header('Location:admin.php');
}
else if($_GET['page'] == 2)
{
	writePOSTtoSESSION();
    //TODO
 // TransfertSyntaxeUID	
	$db = openDB();

    //Patient table
    $dicomSQL = $db->prepare('INSERT INTO DICOM (
            dicom_ip, dicom_port)
            VALUES (
            :ip, :port)
            ON DUPLICATE KEY UPDATE
            dicom_ip=:ip,
            dicom_port=:port
            ;');

    $dicomSQL->execute(array(
        'ip'=>$_SESSION['adresse-ip'],
        'port'=>$_SESSION['port-dicom']
    ));

    $dicomSQL->closeCursor();
	
		
	$paramSQL = $db->prepare('UPDATE Parametres SET delai_sup='.$_SESSION['jours'].' where id_param=1');
    $paramSQL->execute();
    $paramSQL->closeCursor();
	
	header('Location:admin.php');
}
else if($_GET['page'] == 4)
{
    //TODO
}
?>
