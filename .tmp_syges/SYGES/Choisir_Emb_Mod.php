<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"||$_SESSION['habilitation']=="Gerant"))
{

	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Choisir un Emballage.</title>
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
					document.getElementById('tarticle').innerHTML =leselect;
				}
			}
			// Ici on va voir comment faire du post
			xhr.open("POST","AjaxMod_Emballage.php",true);
			// ne pas oublier ça pour le post
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			// ne pas oublier de poster les arguments
			// ici, l'id du nom
			libelle = document.getElementById('libelle').value;
			//iddept = sel.options[sel.selectedIndex].value;
			xhr.send("libelle="+libelle);
        }
        </script>
</head>

<body>
<input type="text" name="libelle" id="libelle" onchange="go()"/>
<input type="button" value="Afficher" name="aff" onclick="go()" />

          <table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h5>Liste des Emballages pour Modification</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center" ><h5>Libelle </h5></td>
                <td  align="center"><h5>Mt Consigne </h5></td>
                <td  align="center"><h5>Statut </h5></td>
                <td  align="center" ><h5>Modifier</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
$i = 0;	
$sql='SELECT  ID_EMBALLAGE, LIBELLE, MT_CONSIGNE, STATUT FROM EMBALLAGE ORDER BY ID_EMBALLAGE ' ;
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_EMBALLAGE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['MT_CONSIGNE'].' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $rslt['STATUT']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modification_Emb&Id=<?php echo $rslt['ID_EMBALLAGE'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>
</body>
</html>
<?php 
}
else
{
?>
				<script language="javascript" type="text/javascript">
				alert('Vous n\'etes pas habiliter a acceder a cette page.');
				history.back();
				</script>
<?php
}
?>
