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
	
	echo $_SESSION['toto'];
	

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

	<script>
		var maxprogress = 260;   // total à atteindre
		var actualprogress = 0;  // valeur courante
		var itv = 0;  // id pour setinterval
		
		var centi=0 // initialise les dixtièmes
var secon=0 //initialise les secondes
var minu=0 //initialise les minutes
		
		function prog()
		{
			
			var progressnum = document.getElementById("progressnum");
			var indicator = document.getElementById("indicator");
			actualprogress += 1;	
			indicator.style.width=actualprogress + "px";
			//progressnum.innerHTML = actualprogress;
			if(actualprogress == maxprogress) actualprogress=0; 
			
			centi++; //incrémentation des dixièmes de 1
if (centi>9){centi=0;secon++} //si les dixièmes > 9, 

if (secon>59){secon=0;minu++} //si les secondes > 59, 
document.forsec.secc.value=" "+centi //on affiche les dixièmes
document.forsec.seca.value=" "+secon //on affiche les secondes
document.forsec.secb.value=" "+minu //on affiche les minutes
compte=setTimeout('chrono()',100) //la fonction est relancée 

		}
	
		
	</script>
<div id="pwidget">  
<div id="progressbar">
	<div id "indicator_min"></div>
    <div id="indicator"></div>
	
</div>
<div id="progressnum">
<form name="forsec">
<input type="text" size="3" name="secb"> min
<input type="text" size="3" name="seca"> sec
<input type="text" size="3" name="secc"> dix
</form>

</div>

<input type="button" name="Submit" value="Lancer l'acquisition"
    onclick="itv = setInterval(prog, 100);debut_acquisition(event)" />
<input type="button" name="Submit" value="Stopper l'acquisition"
    onclick="clearInterval(itv);clicked(event);fin_acquisition(event);" />
	
<button onclick="affichage(event)">Enregristrer les données</button>

<form>
  <input type="button" value="Retour" onclick="history.go(-1)">
</form>
</div>

<script>
function clicked(e)
{
    alert('Acquisition terminée')
}
function debut_acquisition(e){
	<?php
		$resultat = exec('python Lecture_port.py');
		//system("sudo python Lecture_port.py");
	?>
}
function fin_acquisition(e){
	<?php
		system("sudo killall Lecture_port.py");
	?>
}
function affichage(e){
	document.location.href="lecture.php"
}
</script>
	
	</body>
</html>
