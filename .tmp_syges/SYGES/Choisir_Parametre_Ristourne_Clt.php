<?php 
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{

	include('Connexion.php');
	include('fonctions.php');
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Parametre Pour Ristourne</title>
<style type="text/css">
label
{
	display:block;
	width:180px;
	float: left;
	}
</style>
<script src="JS/Consultation_Ristourne_clt.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
	<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
<script type='text/javascript'>
//FONCTION POUR TESTER SI LE NAVIGATEUR PEUT GERER AJAX
	function getXhr()
			{
				var xhr = null;
				if(window.XMLHttpRequest) // Firefox et autres
					xhr = new XMLHttpRequest();
				else if(window.ActiveXObject)
				{ // Internet Explorer
					try {
							xhr = new ActiveXObject("Msxml2.XMLHTTP");
						} 
					catch (e) {
						xhr = new ActiveXObject("Microsoft.XMLHTTP");
						}
				}
				else 
				{ // XMLHttpRequest non supporté par le navigateur
					alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
					xhr = false;
				}
				return xhr;
			}
        /**
        * Méthode qui sera appelée sur le click du bouton
        */
		function goclt()
		{
			var xhr = getXhr();
			// On défini ce qu'on va faire quand on aura la réponse
			xhr.onreadystatechange = function()
			{
				// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
				if(xhr.readyState == 4 && xhr.status == 200)
				{
					leselect = xhr.responseText;
					// On se sert de innerHTML pour rajouter les options a la liste
					document.getElementById('codeclient').innerHTML =leselect;
				}
			}
			// Ici on va voir comment faire du post
			xhr.open("POST","Ajax_Select_Clt.php",true);
			// ne pas oublier ça pour le post
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			// ne pas oublier de poster les arguments
			// ici, l'id du nom
			nomclt = document.getElementById('nomclt').value;
			//iddept = sel.options[sel.selectedIndex].value;
			xhr.send("nomclt="+nomclt);
        
        }
		
</script>
</head>

<body>
<form method="post" action="CTRL/Controle_Ristourne_Clt.php" onsubmit="return verif_form()">
<fieldset style="width:800px; margin-left:150px"><legend align="center">Consultation des Ristournes Client</legend>
<table >
<tr>
	<td><label for="nomclt"> Mot Clé (Client) </label></td>
    <td><input type="text" id="nomclt" name="nomclt" style="width:180px;" onchange="goclt()"/></td>
	<td><h4><label for="codeclient"> Client * </label></h4></td>
    <td colspan="3"> <select name="codeclient" id="codeclient" style="width:180px;">
         <?php
		$sql4 = " select id_client ,nom from client  where statut='Actif' order by nom  ";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
		if ($rslt4["id_client"]==$_GET['Clt'])
		{	
			 echo "<option selected value='".$rslt4["id_client"]."'>";
		}
		else
		{
			 echo "<option value='".$rslt4["id_client"]."'>";
		}
			 echo $rslt4["nom"];
			 echo '</option>';
		
		 }
		 ?>
    </select> </td>
</tr>
	<tr>
    	<td> <h4><label>Période Début* </label> </h4></td>
        <td><input type="text" id="DateD" name="DateD" style="width:90px;"/> 
            <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].DateD,'dd/mm/yyyy',this)" /></td>
        <td ><h4><label style="text-align: left;">Fin* </label></h4></td>
        <td ><input type="text" id="DateF" name="DateF" style="width:90px;"/> 
        <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].DateF,'dd/mm/yyyy',this)" /></td>
    </tr>
<tr>
	<td align="left"><h4><label for="retenue"> Retenues</label></h4></td>
</tr>
<tr>
    <td align="left"><label for="Retfrigo"> Ret. Frigo :</label></td>
 	<td><input type="text" id="Retfrigo" name="Retfrigo" style="width:180px;" value="0"/> </td>
</tr>
<tr>
    <td align="left"><label for="RetDA"> Ret. Droit d'Auteur :</label></td>
 	<td><input type="text" id="RetDA" name="RetDA" style="width:180px;" value="0"/> </td>
    <td align="left"><label for="RetCGA"> Ret. CGA à la Source :</label></td>
 	<td><input type="text" id="RetCGA" name="RetCGA" style="width:180px;" value="0"/> </td>
</tr>

<tr>
	<td align="left"><h4><label> Regularisations</label></h4></td>
</tr>
<tr>
    <td align="left"><label for="RegRistourne"> Reg. Ristourne :</label></td>
 	<td><input type="text" id="RegRistourne" name="RegRistourne" style="width:180px;" value="0"/> </td>
    <td align="left"><label for="RegPSAEC"> Reg. PSA Exc Encours :</label></td>
 	<td><input type="text" id="RegPSAEC" name="RegPSAEC" style="width:180px;" value="0"/> </td>
</tr>
<tr>
    <td align="left"><label for="RegPSAAnt"> Reg. PSA Exc Anterieur :</label></td>
 	<td><input type="text" id="RegPSAAnt" name="RegPSAAnt" style="width:180px;" value="0"/> </td>
    <td align="left"><label for="RegDA"> Reg. Droit d'Auteur :</label></td>
 	<td><input type="text" id="RegDA" name="RegDA" style="width:180px;" value="0"/> </td>
</tr>
<tr>
    <td align="left"><label for="RegEntfrigo"> Reg. Entretien Frigo :</label></td>
 	<td><input type="text" id="RegEntfrigo" name="RegEntfrigo" style="width:180px;" value="0"/> </td>
    <td align="left"><label for="RegCGA"> Reg. CGA :</label></td>
 	<td><input type="text" id="RegCGA" name="RegCGA" style="width:180px;" value="0"/> </td>
</tr>
<tr>
   		<td><input type="submit" value="Valider" name="Valider" id="Valider"/> </td>	
        <td colspan="3" align="right"><input type="button" value="Retour" name="Retour" id="Retour" onclick="history.back()"/></td>
   </tr>
</table>
</fieldset>
</form>
</body>
</html>
<?php 
}
else
{
?>
				<script language="javascript" type="text/javascript">
				alert('Vous n\'etes pas habiliter a  acceder a cette page.');
				history.back();
				</script>
<?php
}
?>
