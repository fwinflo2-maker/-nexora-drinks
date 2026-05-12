<?php 
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
{

	include('Connexion.php');
	include('fonctions.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
label
{
	display:block;
	width:100px;
	float: left;
	}
</style>
<script src="JS/Consultation_Mouv_St_Emb.js" type="text/javascript"></script>
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
					document.getElementById('codeemb').innerHTML =leselect;
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
<form method="post" action="CTRL/Controle_Mouv_St_Emb.php" onsubmit="return verif_form()">
<fieldset style="margin-left:250px; width:600px;"><legend>Consultation des Mouvements en stock Emballage</legend>
<table>
<tr>
	<td><label for="libemb"> Mot Clé  </label></td>
    <td><input type="text" id="libemb" name="libemb" style="width:200px;" onchange="go()"/></td>
    <td><label for="codeemb"> Emballage  </label></td>
    <td> <select name="codeemb" id="codeemb" style="width:200px;">
    <?php
    	$sql = " select id_emballage ,libelle from emballage  order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_emballage"]."'>";
		 echo $rslt["libelle"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>
</tr>
<tr>
	<td align="left"><label for="typemouv"> Type Mouv. :</label></td>
    <td><select name="typemouv" id="typemouv" style="width:200px;">
		 <option>Sortie de Stock</option>
		 <option>Entrée en Stock</option>
         <option>Entrée et Sortie</option>
    </select> </td>
</tr>
<tr>
    	<td><label> Période /Début :</label></td>
        <td><input type="text" id="DateD" name="DateD" style="width:100px;"/> 
            <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].DateD,'dd/mm/yyyy',this)" /></td>
        <td><label style="text-align: left;">Fin :</label></td>
        <td><input type="text" id="DateF" name="DateF" style="width:100px;"/> 
        <input type="button" value="Calendrier" onclick="displayCalendar(document.forms[0].DateF,'dd/mm/yyyy',this)" /></td>
</tr>
<tr>
   		<td><input type="submit" value="Valider" name="Valider" id="Valider"/> </td>	
        <td colspan="3" align="right"><input type="reset" value="Annuler" name="Annuler" id="Retour"/></td>
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
