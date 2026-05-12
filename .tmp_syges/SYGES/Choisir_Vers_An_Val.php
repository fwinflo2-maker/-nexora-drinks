<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{

	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Choisir une Versement.</title>
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
					document.getElementById('tvers').innerHTML =leselect;
				}
			}
			// Ici on va voir comment faire du post
			xhr.open("POST","Ajax_An_Val_Vers.php",true);
			// ne pas oublier ça pour le post
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			// ne pas oublier de poster les arguments
			// ici, l'id du nom
			codevers = document.getElementById('codevers').value;
			//iddept = sel.options[sel.selectedIndex].value;
			xhr.send("codevers="+codevers);
        }
        </script>
</head>

<body>
<input type="text" name="codevers" id="codevers" onChange="go()"/>
<input type="button" value="Afficher" name="aff" onClick="go()" />

          <table id='tvers' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="6"><h5>Liste des Versements pour Annulation de la Validation</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>N° Versement </h5></td>
                <td  align="center" ><h5>Date  </h5></td>
                <td  align="center"><h5>Vendeur</h5></td>
                <td  align="center"><h5>Montant</h5></td>
                <td  align="center" ><h5>Observation </h5></td>
                <td  align="center" ><h5>Annuler</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT  V.NUM_VERS, V.DATE_VERS, V.VENDEUR,VD.LOGIN, VD.NOM, V.OBSERVATION, V.MONTANT FROM VERSEMENT V, USER VD WHERE  V.VENDEUR=VD.LOGIN AND  V.STATUT="V" ORDER BY V.DATE_VERS DESC LIMIT 0,20' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['NUM_VERS']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_VERS']); ?> </td>
                        <td  align="center"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['MONTANT'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo $rslt['OBSERVATION']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=An_Val_Vers&Vers=<?php echo $rslt['NUM_VERS'];?> &VD=<?php echo $rslt['LOGIN'];?> "/> <img src="IMG/Supp.png"/> </a></td>
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
