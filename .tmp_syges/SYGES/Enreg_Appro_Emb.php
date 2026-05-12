<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Magasinier"))
{
	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  d'enregistrement d'un Appro. d'emballages</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
	}
</style>
<script src="JS/Enreg_Appro_Emb.js" type="text/javascript"></script>
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
        function go()
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
					document.getElementById('emb').innerHTML =leselect;
				}
			}
			// Ici on va voir comment faire du post
			xhr.open("POST","Ajax_Select_Emb.php",true);
			// ne pas oublier ça pour le post
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			// ne pas oublier de poster les arguments
			// ici, l'id du nom
			libemb = document.getElementById('libemb').value;
			//iddept = sel.options[sel.selectedIndex].value;
			xhr.send("libemb="+libemb);
        }
</script>
</head>
 
<body>

<form action="CTRL/Controle_Appro_Emb.php" method="post" onsubmit="return verif_form()" >
<fieldset style=" width:750px; margin-left:150px;"><legend>Informations sur l'approvisionnement</legend>
<table >
<tr>
	<td><label for="codeappro"> Code * </label></td>
    <td><input type="text" id="codeappro" name="codeappro" readonly="readonly" style="width:200px; background-color:#ECECEC" value="<?php echo generer_code_appro_emb();?>"/></td>
</tr>
<tr>
    <td><label for="date_appro"> Date de l'appro * </label> </td>
    <td><input type="text" id="date_appro" name="date_appro"style="width:100px;" value="<?php echo date("d/m/y");?>"/> <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].date_appro,'dd/mm/yyyy',this)" /></td>
	<td><label for="observationappro"> Observation  </label></td>
    <td><input type="text" id="observationappro" name="observationappro" maxlength="40" style="width:200px;"/></td>
</tr>
</table>
</fieldset>
<fieldset style=" width:750px; margin-left:150px;"><legend>Informations sur l'emballage</legend>
<table>
<tr>
	<td><label for="libemb"> Mot Clé  </label></td>
    <td><input type="text" id="libemb" name="libemb" style="width:200px;" onchange="go()"/></td>
</tr>
<tr>
	<td><label for="emb"> Emballage * </label></td>
    <td><select name="emb" id="emb" style="width:200px;">
     <?php
	    $sql = " select id_emballage,libelle from emballage  where statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_emballage"]."'>";
		 echo $rslt["libelle"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>
	<td><label for="qterecu"> Qte Reçu * </label></td>
    <td><input type="text" id="qterecu" name="qterecu" style="width:200px;"/></td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Enregistrer" id="Enregistrer" name="Enregistrer"/></td>
    <td colspan="2" align="right"><input type="reset" align="right" value="Retour" id="Retour" name="Retour" onclick="history.back()"/></td>
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
