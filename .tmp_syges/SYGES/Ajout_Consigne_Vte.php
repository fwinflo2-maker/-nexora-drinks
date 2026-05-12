<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"|| $_SESSION['habilitation']=="Comptable" || $_SESSION['habilitation']=="OPS"))
{
	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  d'ajout d'une consigne à une vente.</title>
<style type="text/css">
label
{
	display:block;
	width:100px;
	float: left;
	}
</style>
<script src="JS/Ajout_Consigne_Vente.js" type="text/javascript"></script>
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

<form action="CTRL/Ctrl_Ajout_Consigne_Vte.php" method="post" onsubmit="return verif_form()" >
<fieldset style=" width:800px; margin-left:120px;"><legend>Ajout d'une consigne à une vente</legend>
<table>
<tr>
	<td><label for="codevente">Vente *</label> </td>
    <td><input type="text" id="codevente" name="codevente" value="<?php echo $_GET['Vte']?>" readonly="readonly" style="background:#ECECEC; width:100px;"/></td>
    <td ><label>Date Consigne :</label></td>
    <td><input type="text" id="Date" name="Date" style="width:110px;" value="<?php echo dateFormatFrancais($_GET['DateV'])?>"/><input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].Date,'dd/mm/yyyy',this)" /></td>
</tr>
<tr>
	<td><label for="libemb"> Mot Clé  </label></td>
    <td><input type="text" id="libemb" name="libemb" style="width:100px;" onchange="go()"/></td>
	<td><label for="Emb"> Emballage * </label></td>
    <td><select name="Emb" id="Emb" style="width:200px;">
     <?php
	    $sql = " select id_emballage,libelle, qtestock from emballage  where statut='Actif' and id_emballage not in (select id_emballage from consigne where id_sortiestock ='".$_GET['Vte']."') order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_emballage"]."'>";
		 echo $rslt["libelle"].' (Stock :'.$rslt['qtestock'].')';
		 echo '</option>';
		 }
		 ?>
    </select> </td>
    <td><label for="qte"> Quantite * </label></td>
    <td><input type="text" id="qte" name="qte" style="width:100px;"/></td>
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
