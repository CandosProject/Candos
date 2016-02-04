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
?>
<!DOCTYPE html>
<html>
    <head>
        <title>DICOM - admin</title>
        <link rel="stylesheet" href="./UI_admin.css" />
        <meta charset="utf-8" />
			<style>
				body{
						font-family:Arial,Helvetica,sans-serif;
						font-size:small;
						background:linear-gradient(to right, DarkGray, DimGray);
						background-repeat: no-repeat;
						background-attachment: fixed;
					}
		</style>
    </head>
    <body>
        <div id="reglagesGeneraux" class="reglagebox_top leftcolumn toprow b1">
            <form action="process_admin.php?page=1" method="post">
                <?php printAddableCombobox('nom-site', "Nom du site", array()); ?>
                <?php printDoubleAddableCombobox('site', "Site", "Nom", "Adresse", array()); ?>
                <?php printAddableCombobox('operateur', "Operateur", loadOperateurs($db)); ?>
                <?php printAddableCombobox('prescripteur', "Prescripteur", loadPrescripteurs($db)); ?>
                <?php printAddableCombobox('realisateur', "Realisateur", loadRealisateurs($db)); ?>
                <?php printAddableCombobox('position-examen', "Position de l'examen", loadPosture($db)); ?>
                <?php printAddableCombobox('activite-examen', "Activite demandee", loadAnatomicOrientation($db)); ?>
                <?php printDoubleAddableCombobox('localisation-examen',"Localisation de l'examen", "region sequence", "nom", loadBodyparts($db)); ?>
				
            </form>
        </div>
        <div id="reglagesDicom" class="reglagebox_top rightcolumn toprow b2">
            <form action="process_admin.php?page=2" method="post">
                <?php printInput('text','adresse-ip','Adresse IP',true); ?>
                <?php printInput('number','port-dicom','Port DICOM',true); ?>
                <?php printDropDownMenu('syntaxe-transfert','Syntaxe de transfert',array('Implicit little endian', 'Explicit little endian', 'Implicit big endian', 'Explicit big endian')) ?>
                 <?php printInput('number','jours','Delai avant suppression des dossiers',true); ?>
				<?php printDefaultSubmitButton(); ?>
            </form>
        </div>
        <div id="Logs" class="reglagebox_bottom leftcolumn bottomrow b3">
            <textarea rows="4" cols="50">Ceci est un test pour les logs.</textarea>
            <br />
        </div>
        <div id="sauvegarder" class="reglagebox_bottom rightcolumn bottomrow b4">
            <form action="process_admin.php?page=4" method="post">
                <input type="submit" name="load" value="charger configuration" class="sauvegarder"/>
                <input type="button" name="save" value="sauvegarder" class="sauvegarder"/>
            </form>
        </div>
    </body>
</html>
