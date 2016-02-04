<?php
session_start();
include_once("convenience_functions.php");
$page_number = 4;

if(!isset($_GET['page']) || !is_numeric($_GET['page']) || $_GET['page'] >= $page_number)
{
    header('Location:./');
    exit;
}
else
    $page = $_GET['page'];

if($page == $page_number - 1)
{
	

 
    saveToDB();
	header("location:chargement_acquisition.php");
   // session_destroy();
}
else{
    writePOSTtoSESSION();


$nextpage = $page + ((isset($_POST['previous']))? -1 : +1) % $page_number;

header('Location:./?page='.$nextpage);
exit;
}

// Création des dossiers patient + examen
$np=$_SESSION['patient_lastName'];
$pp=$_SESSION['patient_firstName'];
$ins=$_SESSION['patient_insee'];
$date = date("d-m-Y");
$dossier_patient = './Patients/'.$np.'_'.$pp.'_'.$ins;
if(!is_dir($dossier_patient)){
   mkdir($dossier_patient);
}
$val_date='/'.$date;
//$dossier_examen= './Patients/'.$dossier_patient.$val_date;
$dossier_examen= './Patients/.'.$dossier_patient.$val_date;
if(!is_dir($dossier_examen)){
   mkdir($dossier_examen);
}

$_SESSION['examen']=$dossier_examen;

if(!is_dir($dossier_patient)){
   mkdir($dossier_patient);
}
if(isset($_SESSION['current'])){
	if(($_SESSION['current'])!=($_SESSION['patient_insee'])){
		$_SESSION['serie']=0;
	}
}
$_SESSION['current']=$_SESSION['patient_insee'];

//ORGANISATION ROOT : UID racine
$_SESSION['org_root']='1.2.826.0.1.3680043.9.5996.';

/*Architecture de l'UID : 
 -> STUDY : Organisation root + 1 + DATE + HEURE
 -> SERIE : Organisation root + 2 + DATE + HEURE
 -> IMAGE : Organisation root + 3 + DATE + HEURE
 */
 
 


function saveToDB()
{
    $db = openDB();

    //Patient table
    $patientSQL = $db->prepare('INSERT INTO Patient (
            patient_insee, patient_firstName, patient_lastName, patient_dateOfBirth, patient_sex, patient_size, patient_weight,
            patient_typeOfID, patient_insurancePlanIdentification, patient_countryOfResidence)
            VALUES (
            :insee, :firstName, :lastName, :dateOfBirth, :sex, :size, :weight,
            \'INSEE\', \'INSEE\', :countryOfResidence)
            ON DUPLICATE KEY UPDATE
            patient_firstName=:firstName,
            patient_lastName=:lastName,
            patient_dateOfBirth=:dateOfBirth,
            patient_sex=:sex,
            patient_size=:size,
            patient_weight=:weight,
            patient_countryOfResidence=:countryOfResidence;');

    $patientSQL->execute(array(
        'insee'=>$_SESSION['patient_insee'],
        'firstName'=>$_SESSION['patient_firstName'],
        'lastName'=>$_SESSION['patient_lastName'],
        'dateOfBirth'=>$_SESSION['patient_dateOfBirth'],
        'sex'=>$_SESSION['patient_sex'],
        'size'=>$_SESSION['patient_size'],
        'weight'=>$_SESSION['patient_weight'],
        'countryOfResidence'=>$_SESSION['patient_countryOfResidence']
    ));

    $patientSQL->closeCursor();

    $examenSQL = $db->prepare('INSERT INTO Examen (
            examen_instanceCreationDateTime,
            examen_procedureCodeSequence,
            examen_institutionalDepartementName,
            examen_protocolName,
            examen_performedProcedureStepID,
            examen_performedProcedureStepDescription,
            examen_contentDateTime,
            examen_instanceCreatorUID,
            bodyPart_anatomicRegionSequence,
            anatomicOrientation_name,
            posture_name,
            operateur_name,
            realisateur_performingPhysicianName,
            prescripteur_referringPhysicianName,
            patient_insee,
            examen_comment)
        VALUES (
            :time,
            \'DEFAULT PROCEDURE CODE SEQUENCE\',
            \'TODO\',
            \'DEFAULT PROTOCOL\',
            \'DEFAULT PROCEDURE STEP ID\',
            \'DEFAULT PROCEDURE STEP DESCRIPTION\',
            :time,
            \'DEFAULT INSTANCE CREATOR UID\',
            :bodyPart,
            :anatomicOrientation,
            :posture,
            :operateur,
            :realisateur,
            :prescipteur,
            :insee,
            :comment
        );');

			 // EXCTRACTION DATE HEURE
 $date_UID= new DateTime("NOW");
 $years_UID = $date_UID->format('Ymd');
 $hours_UID = $date_UID->format('H');
 $minutes_UID = $date_UID->format('i');
 $seconds_UID = $date_UID->format('s');
		
    $examenSQL->execute(array(
        'time'=>date("Y-m-d H:i:s"),
        'bodyPart'=>$_SESSION['examen_bodyPart'],
        'anatomicOrientation'=>$_SESSION['examen_anatomicOrientation'],
        'posture'=>$_SESSION['examen_posture'],
        'operateur'=>$_SESSION['medic_operateur'],
        'realisateur'=>$_SESSION['medic_prescripteur'],
        'prescipteur'=>$_SESSION['medic_realisateur'],
        'insee'=>$_SESSION['patient_insee'],
        'comment'=>$_SESSION['examen_comment']
    )
	);
	
	  $examenSQL->closeCursor();
	
	$studySQL=$db->prepare('INSERT INTO Study (
	study_studyInstanceUID,
	study_aquisitionsInStudy,
	study_datetime,
	study_referencedStudySequence,
	patient_insee)
	VALUES (
	:uid,
	:acquisitions,
	:time,
	:references,
	:insee
	);'
	);
	
	$_SESSION['study_UID']=$_SESSION['org_root'].'1.'.$years_UID.'.'.$hours_UID.$minutes_UID.$seconds_UID;
	
	$studySQL->execute(array(
	'uid'=>$_SESSION['study_UID'],
	'acquisitions'=>1,
	'time'=>date("Y-m-d H:i:s"),
	'references'=>0,
	'insee'=>$_SESSION['patient_insee']
	));
	
	$studySQL->closeCursor();
	
	$_SESSION['toto']=$months_UID;
  
}
?>


