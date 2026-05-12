<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Comptable" || $_SESSION['habilitation']=="Magasinier" ))
{
	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Formulaire  d'enregistrement d'un Appro.</title>
<style type="text/css">
label
{
	display:block;
	width:130px;
	float: left;
	}
</style>
<script src="JS/Enreg_Appro.js" type="text/javascript"></script>
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
			xhr.open("POST","Ajax_Select_Ar.php",true);
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
			xhr.open("POST","Ajax_Select_Ar2.php",true);
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
			xhr.open("POST","Ajax_Select_Ar3.php",true);
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

<form action="CTRL/Controle_Appro.php" method="post" onsubmit="return verif_form()" >
<fieldset style=" width:1050px; "><legend>Informations sur l'approvisionnement</legend>
<table >
<tr>
    <td colspan="6" align="right"><a href="index.php?formulaire=Enreg_Fournisseur_Appro"><input type="button" value="Nouveau Fournisseur" name="nouveaufournisseur" style="background:#F00;"/> </a></td>
</tr>
	<td><label for="codefournisseur"> Fournisseur * </label></td>
    <td><select name="codefournisseur" id="codefournisseur" style="width:200px;">
         <?php
		$sql4 = " select id_fournisseur ,nom from fournisseur  where statut='Actif' order by nom  ";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
		if ($rslt4["id_fournisseur"]==$_GET['Fs'])
		{	
			 echo "<option selected value='".$rslt4["id_fournisseur"]."'>";
		}
		else
		{
			 echo "<option value='".$rslt4["id_fournisseur"]."'>";
		}
			 echo $rslt4["nom"];
			 echo '</option>';
		
		 }
		 ?>
    </select> </td>
	<td><label for="codeappro"> Code Appro * </label></td>
    <td><input type="text" id="codeappro" name="codeappro" style="width:200px; " /></td>
    <td><label for="liquideht"> MT Liquide HT *</label></td>
    <td><input type="text" id="liquideht" name="liquideht" style="width:200px; " /></td>
</tr>
<tr>
    <td><label for="nbrecolis"> Nbre de Colis *</label></td>
    <td><input type="text" id="nbrecolis" name="nbrecolis" style="width:200px; " /></td>
    <td><label for="date_appro"> Date de l'appro * </label> </td>
    <td><input type="text" id="date_appro" name="date_appro"style="width:100px;" value="<?php echo date("d/m/Y");?>"/> <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].date_appro,'dd/mm/yyyy',this)" /></td>
	<td><label for="observationappro"> Observation  </label></td>
    <td><input type="text" id="observationappro" name="observationappro" maxlength="40" style="width:200px;"/></td>
</tr>
</table>
</fieldset>
<fieldset style=" width:1050px;"><legend>Articles de l'Approvisionnement</legend>
<table>
<tr>
	<td><label for="libar"> Mot Clé  </label></td>
    <td><input type="text" id="libar" name="libar" style="width:200px;" onchange="go()"/></td>
	<td><label for="codeart"> Article 1 * </label></td>
    <td><select name="codeart" id="codeart" style="width:250px;">
     <?php
	    $sql = " select id_article ,marque,libelle, qtestock from article  where statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_article"]."'>";
		 echo $rslt["libelle"].' (Stock :'.$rslt["qtestock"].')';
		 echo '</option>';
		 }
		 ?>
    </select> </td>
	<td><label for="qterecu"> Qte Reçu * </label></td>
    <td><input type="text" id="qterecu" name="qterecu" style="width:150px;"/></td>
</tr>
<tr>
	<td><label for="libar2"> Mot Clé  </label></td>
    <td><input type="text" id="libar2" name="libar2" style="width:200px;" onchange="go2()"/></td>
	<td><label for="codeart2"> Article 2 </label></td>
    <td><select name="codeart2" id="codeart2" style="width:250px;">
     <?php
	    $sql = " select id_article ,marque,libelle, qtestock from article  where statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_article"]."'>";
		 echo $rslt["libelle"].' (Stock :'.$rslt["qtestock"].')';
		 echo '</option>';
		 }
		 ?>
    </select> </td>
	<td><label for="qterecu2"> Qte Reçu  </label></td>
    <td><input type="text" id="qterecu2" name="qterecu2" style="width:150px;"/></td>
</tr>
<tr>
	<td><label for="libar3"> Mot Clé  </label></td>
    <td><input type="text" id="libar3" name="libar3" style="width:200px;" onchange="go3()"/></td>
	<td><label for="codeart3"> Article 3 </label></td>
    <td><select name="codeart3" id="codeart3" style="width:250px;">
     <?php
	    $sql = " select id_article ,marque,libelle, qtestock from article  where statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_article"]."'>";
		 echo $rslt["libelle"].' (Stock :'.$rslt["qtestock"].')';;
		 echo '</option>';
		 }
		 ?>
    </select> </td>
	<td><label for="qterecu3"> Qte Reçu  </label></td>
    <td><input type="text" id="qterecu3" name="qterecu3" style="width:150px;"/></td>
</tr>
<tr>
    <td colspan="3"><input type="submit" align="left" value="Enregistrer" id="Enregistrer" name="Enregistrer"/></td>
    <td colspan="3" align="right"><input type="reset" align="right" value="Retour" id="Retour" name="Retour" onclick="history.back()"/></td>
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
