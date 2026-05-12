<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"|| $_SESSION['habilitation']=="Caissier" ))
{
	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  d'enregistrement d'une vente Frigo.</title>
<style type="text/css">
label
{
	display:block;
	width:130px;
	float: left;
	}
</style>
<script src="JS/Enreg_Vente_Frigo.js" type="text/javascript"></script>
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
					document.getElementById('codeart').innerHTML =leselect;
				}
			}
			// Ici on va voir comment faire du post
			xhr.open("POST","Ajax_Select_Ar_Fr.php",true);
			// ne pas oublier ça pour le post
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			// ne pas oublier de poster les arguments
			// ici, l'id du nom
			libar = document.getElementById('libar').value;
			//iddept = sel.options[sel.selectedIndex].value;
			xhr.send("libar="+libar);
        }
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
					document.getElementById('codeart2').innerHTML =leselect;
				}
			}
			// Ici on va voir comment faire du post
			xhr.open("POST","Ajax_Select_Ar_Fr2.php",true);
			// ne pas oublier ça pour le post
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			// ne pas oublier de poster les arguments
			// ici, l'id du nom
			libar2 = document.getElementById('libar2').value;
			//iddept = sel.options[sel.selectedIndex].value;
			xhr.send("libar2="+libar2);
        }
		function go3()
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
					document.getElementById('codeart3').innerHTML =leselect;
				}
			}
			// Ici on va voir comment faire du post
			xhr.open("POST","Ajax_Select_Ar_Fr3.php",true);
			// ne pas oublier ça pour le post
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			// ne pas oublier de poster les arguments
			// ici, l'id du nom
			libar3 = document.getElementById('libar3').value;
			//iddept = sel.options[sel.selectedIndex].value;
			xhr.send("libar3="+libar3);
        }
</script>
</head>
 
<body>

<form action="CTRL/Controle_Vente_Frigo.php" method="post" onsubmit="return verif_form()" >

<fieldset style=" width:1050px;"><legend>Informations sur la vente Frigo</legend>
<table >
<tr>
	<td><label for="codevente"> Code * </label></td>
    <td><input type="text" id="codevente" name="codevente" readonly="readonly" style="width:150px; background-color:#ECECEC" value="<?php echo generer_code_ventefrigo();?>"/></td>
<?php if ($_SESSION['habilitation']=="Administrateur")
{
?>
    <td><label for="date_vente"> Date de la vente * </label> </td>
    <td><input type="text" id="date_vente" name="date_vente" style="width:90px;" value="<?php echo date("d/m/Y");?>"/> <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].date_vente,'dd/mm/yyyy',this)" /></td>
 <?php 
}
else
{
?>
    <td><label for="date_vente"> Date de la vente * </label> </td>
    <td><input type="text" id="date_vente" name="date_vente" style="width:180px; ; background-color:#ECECEC" value="<?php echo date("d/m/Y");?>" readonly="readonly"/></td>
<?php 
}
?>
	<td><label for="observationsortie"> Observation  </label></td>
    <td><input type="text" id="observationsortie" name="observationsortie" maxlength="35" style="width:265px;"/></td>
</tr>
</table>
</fieldset>
<fieldset style=" width:1050px;"><legend>Informations sur l'article</legend>
<table>
<tr>
	<td><label for="libar"> Mot Clé (Article 1) </label></td>
    <td><input type="text" id="libar" name="libar" style="width:150px;" onchange="go()"/></td>
	<td><label for="codeart"> Article * </label></td>
    <td><select name="codeart" id="codeart" style="width:300px;">
     <?php
	    $sql = " select id_article ,marque,libelle, stockfrigo from article  where statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_article"]."'>";
		 echo $rslt["libelle"].' (Stock :'.$rslt["stockfrigo"].')';
		 echo '</option>';
		 }
		 ?>
    </select> </td>
	<td><label for="qtevendu"> Quantite  * </label></td>
    <td><input type="text" id="qtevendu" name="qtevendu" style="width:150px;"/></td>
</tr>
<tr>
	<td><label for="libar2"> Mot Clé (Article 2)  </label></td>
    <td><input type="text" id="libar2" name="libar2" style="width:150px;" onchange="go2()"/></td>
	<td><label for="codeart2"> Article 2 </label></td>
    <td><select name="codeart2" id="codeart2" style="width:300px;">
     <?php
	    $sql = " select id_article ,marque,libelle, stockfrigo from article  where statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_article"]."'>";
		 echo $rslt["libelle"].' (Stock :'.$rslt["stockfrigo"].')';
		 echo '</option>';
		 }
		 ?>
    </select> </td>
	<td><label for="qtevendu2"> Quantité 2 </label></td>
    <td><input type="text" id="qtevendu2" name="qtevendu2" style="width:150px;"/></td>
</tr>
<tr>
	<td><label for="libar3"> Mot Clé (Article 3)  </label></td>
    <td><input type="text" id="libar3" name="libar3" style="width:150px;" onchange="go3()"/></td>
	<td><label for="codeart3"> Article 3 </label></td>
    <td><select name="codeart3" id="codeart3" style="width:300px;">
     <?php
	    $sql = " select id_article ,marque,libelle, stockfrigo from article  where statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_article"]."'>";
		 echo $rslt["libelle"].' (Stock :'.$rslt["stockfrigo"].')';;
		 echo '</option>';
		 }
		 ?>
    </select> </td>
	<td><label for="qtevendu3"> Quantité 3 </label></td>
    <td><input type="text" id="qtevendu3" name="qtevendu3" style="width:150px;"/></td>
</tr
><tr>
    <td colspan="2"><input type="submit" align="left" value="Enregistrer" id="Enregistrer" name="Enregistrer"/></td>
    <td colspan="4" align="right"><input type="reset" align="right" value="Retour" id="Retour" name="Retour" onclick="history.back()"/></td>
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
