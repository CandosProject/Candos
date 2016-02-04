<?php
    session_start();
    $page_number = 4;
    $current_building_page = 0;
    if(!isset($_GET['page']) || !is_numeric($_GET['page']))
        $page = 0;
    else
        $page = $_GET['page']%$page_number;

    include_once('convenience_functions.php');
    $db = openDB();
	
	// Suppression de la base et des dossiers des examens vieux de 7 jours
	$nb_exm="SELECT COUNT(*) FROM Examen";
	$res_nb=$db->query($nb_exm);
	$baseVide=$res_nb->fetch();
	
	// Récuperer la valeur du délai indiqué dans les parametres 
	$querydel="SELECT delai_sup FROM parametres where id_param='1'";
	$resdel=$db->query($querydel);
	$rowjour=$resdel->fetch();
	$jours=$rowjour[0];
	
	echo $jours;
	
	//if($baseVide!=0){
	$date= new DateTime("NOW");
	$queryexm="SELECT DATE_FORMAT(examen_instanceCreationDateTime, '%d-%m-%Y'),examen_accessionNumber, patient_insee FROM examen";
	
	$resexm=$db->query($queryexm)  or die('Erreur SQL !<br />'.$queryexm.'<br />'.mysql_error());
	while($rowexm=$resexm->fetch()){ 
		$date2= new DateTime($rowexm[0]);
		$index=$rowexm[1];
		$insee_patient=$rowexm[2];
		$interval=date_diff($date,$date2);	
		$int2=$interval->days;


	
	//$res=$db->query($query)  or die('Erreur SQL !<br />'.$query.'<br />'.mysql_error());
	
		// Suppression examens
		if($int2>$jours){
			$sql1="Select patient_firstName, patient_lastName from patient where patient_insee=$insee_patient";
			$info=$db->query($sql1)  or die('Erreur SQL !<br />'.$sql1.'<br />'.mysql_error());
			$data = $info->fetch();
			$nom=$data[1];
			$prenom=$data[0];
			$dossier_examen= './Patients/'.$nom.'_'.$prenom.'_'.$insee_patient.'/'.$rowexm[0];
			if(is_dir($dossier_examen)){
			
				delTree($dossier_examen);
			
			}
			
			
			$sqlsup="DELETE FROM examen WHERE examen_accessionNumber= $index";
			$db->query($sqlsup)  or die('Erreur SQL !<br />'.$sqlsup.'<br />'.mysql_error());
			
		}
		
	}
	
	/// Suppression séries/études/images de la base
		$queryetu="SELECT DATE_FORMAT(study_datetime, '%d-%m-%Y'),study_id FROM study";
	
	$resetu=$db->query($queryetu)  or die('Erreur SQL !<br />'.$queryetu.'<br />'.mysql_error());
	while($rowetu=$resetu->fetch()){ 
		$date3= new DateTime($rowetu[0]);
		$indexetu=$rowetu[1];
		$interval2=date_diff($date,$date3);	
		$int3=$interval2->days;
	
	//$res=$db->query($query)  or die('Erreur SQL !<br />'.$query.'<br />'.mysql_error());

		if($int3>$jours){
			
	
			
			$query4="SELECT serie_instanceNumber from serie where study_id=$indexetu";
	
				$res4=$db->query($query4)  or die('Erreur SQL !<br />'.$query4.'<br />'.mysql_error());
				while($row4=$res4->fetch()){ 
				$index_serie=$row4[0];
				
					$sql4="DELETE FROM image WHERE serie_instanceNumber= $index_serie";
					$db->query($sql4)  or die('Erreur SQL !<br />'.$sql4.'<br />'.mysql_error());
					
				}
				$sql3="DELETE FROM serie WHERE study_id= $indexetu";
				$db->query($sql3)  or die('Erreur SQL !<br />'.$sql3.'<br />'.mysql_error());
				$sql2="DELETE FROM study WHERE study_id= $indexetu";
				$db->query($sql2)  or die('Erreur SQL !<br />'.$sql2.'<br />'.mysql_error());
			
		}
		
	}
	
	

	
	
	// Fonction de suppression des dossiers
	function delTree($dir) { 
   $files = array_diff(scandir($dir), array('.','..')); 
    foreach ($files as $file) { 
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file"); 
    } 
    return rmdir($dir); 
  } 
	
	//}
	
?>
<!DOCTYPE html>
<html>
    <head>
        <title>DICOM - utilisateur</title>
        <link rel="stylesheet" href="./UI_responsive.css" />
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
        <div id="reglagesPatient" class="<?php disable() ?> reglagebox leftcolumn toprow b1">
		
		<?php
			if($baseVide[0]!=0){ ?>
		
			<form action="load_patient.php" method="post">
                <input type="text" id="chargerPatient" name="patient_loading" list="liste-charger-patient" placeholder="Nom du patient &agrave; charger" <?php disable() ?> />
                <?php printDatalist("liste-charger-patient",loadPatients($db)) ?>
                <input type="submit" id="boutonChargerPatient" value="charger patient" <?php disable() ?> class="chargerPatient" />
            </form>
		<?php	}
		?>
			
            <form action="./process_page.php?page=<?php echo $current_building_page ?>" method="post">
                <?php printInput("number", "patient_insee", "Numéro INSEE", true);?>
                <?php printInput("text", "patient_firstName", "Prénom du patient", true);?>
                <?php printInput("text", "patient_lastName", "Nom du patient", true);?>
                <?php printInput("date", "patient_dateOfBirth", "Date de naissance", true); ?>
                <?php printRadioButton("patient_sex", "Sexe", array("F"=>"Femme","M"=>"Homme","O"=>"Autre"), true); ?>
                <?php printInput("number", "patient_size", "Taille (cm)", true); ?>
                <?php printInput("number", "patient_weight", "Poids (kg)", true); ?>
                <?php printCountryDropDownList("patient_countryOfResidence","Pays de résidence", true); ?>
                <?php printNextButton() ?>
            </form>
        </div>
        <?php ++$current_building_page; ?>
        <div id="reglagesExamen" class="<?php disable() ?> reglagebox rightcolumn toprow b2">
            <form action="./process_page.php?page=<?php echo $current_building_page ?>" method="post">
                <?php printDropDownMenu("examen_anatomicOrientation","Position de l'examen",loadAnatomicOrientation($db)); ?>
                <?php printDropDownMenu("examen_posture","État du muscle / activité demandée",loadPosture($db)); ?>
                <?php printDropDownMenu("examen_bodyPart","Localisation de l'examen", loadBodyparts($db)); ?>
				<?php printInput("textarea","examen_comment", "Commentaire", false); ?>
                <?php printPreviousButton() ?>
                <?php printNextButton() ?>
            </form>
        </div>
        <?php ++$current_building_page; ?>
        <div id="reglagesMedecins" class="<?php disable() ?> reglagebox leftcolumn bottomrow b3">
            <form action="./process_page.php?page=<?php echo $current_building_page ?>" method="post">
                <?php printDropDownMenu("medic_operateur", "Opérateur", loadOperateurs($db)); ?>
                <?php printDropDownMenu("medic_prescripteur", "Prescripteur", loadPrescripteurs($db)); ?>
                <?php printDropDownMenu("medic_realisateur", "Réalisateur", loadRealisateurs($db)); ?>
                <?php printPreviousButton() ?>
                <?php printNextButton() ?>
            </form>
        </div>
        <?php ++$current_building_page; ?>
        <div id="sauvegarder" class="<?php disable() ?> reglagebox rightcolumn bottomrow b4">
            <form action="./process_page.php?page=<?php echo $current_building_page ?>" method="post">
                
				<input type="submit" name="save" value="Examen" class="<?php disable()?> sauvegarder">
            </form>
			<form method="POST" action="./?page=2">
				<input type="submit" name="Précédent" value="Précédent" class="sauvegarder" >
			</form>
        </div>
    </body>
</html>
