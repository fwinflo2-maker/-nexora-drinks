<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  d'ajout d'un EMBALLAGE à un Appro. Cession</title>
<style type="text/css">
label
{
	display:block;
	width:150px;
	float: left;
	}
</style>
<script src="JS/Ajout_Emballage_APC.js" type="text/javascript"></script>
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
					document.getElementById('Emb').innerHTML =leselect;
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

<form action="CTRL/Ctrl_Ajout_Emballage_APC.php" method="post" onsubmit="return verif_form()" >
<fieldset style=" width:1050px;"><legend>Ajout d'un Emballage à un Appro Cession</legend>
<table>
<tr>
	<td><label for="codeappro">Cession *</label> </td>
    <td><input type="text" id="codeappro" name="codeappro" value="<?php echo $_GET['APC']?>" readonly="readonly" style="background:#ECECEC; width:150px;"/></td>
    </td>
</tr>
<tr>
	<td><label for="libemb"> Mot Clé  </label></td>
    <td><input type="text" id="libemb" name="libemb" style="width:150px;" onchange="go()"/></td>
	<td><label for="Emb"> Emballage * </label></td>
    <td><select name="Emb" id="Emb" style="width:250px;">
     <?php
	    $sql = " select id_emballage,libelle from emballage  where statut='Actif'";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_emballage"]."'>";
		 echo $rslt["libelle"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>
	<td><label for="qte"> Nombre * </label></td>
    <td><input type="text" id="qte" name="qte" style="width:150px;"/></td>
</tr>
<tr>
    <td colspan="2"><input type="submit" align="left" value="Enregistrer" id="Enregistrer" name="Enregistrer"/></td>
    <td colspan="4" align="right"><input type="reset" align="right" value="Fin de Saisie" id="Retour" name="Retour" onclick="history.back()"/></td>
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
