<?php

if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur")||($_SESSION['habilitation']=="Gerant")||($_SESSION['habilitation']=="Caissier" || $_SESSION['habilitation']=="Comptable"))
{
	include("Connexion.php");
	include("fonctions.php");
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire d'enregistrement d'une charge.</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
}
</style>
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
		
		function go2()
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
					document.getElementById('TypeCharge').innerHTML =leselect;
				}
			}
			// Ici on va voir comment faire du post
			xhr.open("POST","Ajax_Select_Charge.php",true);
			// ne pas oublier ça pour le post
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			// ne pas oublier de poster les arguments
			// ici, l'id du nom
			tcharge = document.getElementById('tcharge').value;
			//iddept = sel.options[sel.selectedIndex].value;
			xhr.send("tcharge="+tcharge);
        }
</script>

<script src="JS/Enreg_Charge.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen" />
<script type="text/javascript" src="dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
<body>
<fieldset style="width:750px; margin-left:150px; border-color:#FFFBF0" ><legend>Enregistrement d'une charge.</legend>
<table>
<form action="CTRL/Controle_Charge.php" method="post" onsubmit="return verif_form()" >
<tr>
	<td><label for="Code"> Code *</label></td>
	<td><input type="text" id="Code" name="Code" style="width:200px; background-color:#ECECEC;" readonly="readonly" value="<?php echo generer_code_charge();?>"/> </td>
    <td><label for="Date"> Date d'enregistrement * </label> </td>
    <td><input type="text" id="Date" name="Date"style="width:130px;" value="<?php echo date("d/m/y");?>"/> <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].Date,'dd/mm/yyyy',this)" /></td>
</tr>
<tr>
	<td><label for="tcharge"> Mot Clé (Type Charge) </label></td>
    <td><input type="text" id="tcharge" name="tcharge" style="width:200px;" onchange="go2()"/></td>
    <td><label for="TypeCharge"> Type *</label></td>
	<td><select name="TypeCharge" id="TypeCharge" style="width:250px;">
         <?php
		$sql4 = " select id_typecharge ,libelle from type_charge  where statut='Actif' order by libelle  ";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
			 echo "<option value='".$rslt4["id_typecharge"]."'>";
			 echo $rslt4["libelle"];
			 echo '</option>';
		 }
		 ?>
    </select> </td>
</tr>
<tr>
    <td><label for="Description"> Description *</label></td> 
    <td><input type="text" id="Description" name="Description" style="width:200px" maxlength="30"/></td>
    <td><label for="Montant"> Montant *</label></td>
    <td><input type="text" id="Montant" name="Montant" style="width:250px;"></td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Enregistrer" id="Enreg" name="Enreg"/></td>
    <td colspan="2" align="right"><input type="button" align="right" value="Retour" id="Retour" name="Retour" onclick="history.back()"/></td>
</tr>
</form>
</table>
</fieldset>
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
