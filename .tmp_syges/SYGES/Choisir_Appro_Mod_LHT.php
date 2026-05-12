<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Comptable" || $_SESSION['habilitation']=="Magasinier"))
{

	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Choisir un Approvisionnement.</title>
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
					document.getElementById('tappro').innerHTML =leselect;
				}
			}
			// Ici on va voir comment faire du post
			xhr.open("POST","Ajax_Mod_Appro_LHT.php",true);
			// ne pas oublier ça pour le post
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			// ne pas oublier de poster les arguments
			// ici, l'id du nom
			codeappro = document.getElementById('codeappro').value;
			//iddept = sel.options[sel.selectedIndex].value;
			xhr.send("codeappro="+codeappro);
        }
        </script>
</head>

<body>
<input type="text" name="codeappro" id="codeappro" onchange="go()"/>
<input type="button" value="Afficher" name="aff" onclick="go()" />

          <table id='tappro' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="7"><h5>Liste des approvisionnements pour Modification Liquide HT</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center" ><h5>Date de l'appro </h5></td>
                <td  align="center"><h5>Fournisseur </h5></td>
                <td  align="center"><h5>Liquide HT </h5></td>
                <td  align="center"><h5>Nbre de Colis </h5></td>
                <td  align="center" ><h5>Observation </h5></td>
                <td  align="center" ><h5>Modifier</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT  ID_APPRO, DATE_APPRO, ID_FOURNISSEUR, OBSERVATION, LIQUIDEHT, NBRECOLIS FROM APPROVISIONNEMENT ORDER BY DATE_APPRO DESC LIMIT 0,20' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			//on recupere le nom du fssr
		$sql2='SELECT  NOM, ID_FOURNISSEUR FROM FOURNISSEUR WHERE ID_FOURNISSEUR="'.$rslt['ID_FOURNISSEUR'].'"' ;
		$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			$nom=$rslt2['NOM'];
		}
		//
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_APPRO']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_APPRO']); ?> </td>
                        <td  align="center"> <?php echo  $nom ; ?> </td>
                        <td  align="center"> <?php echo  number_format($rslt['LIQUIDEHT'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo $rslt['NBRECOLIS']; ?> </td>
                        <td  align="center"> <?php echo $rslt['OBSERVATION']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modification_Appro_LHT&Ap=<?php echo $rslt['ID_APPRO'];?> &Fs=<?php echo $rslt['ID_FOURNISSEUR'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
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
