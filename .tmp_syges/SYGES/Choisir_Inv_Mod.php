<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="DGA" || $_SESSION['habilitation']=="Superviseur" || $_SESSION['habilitation']=="Comptable"))
{

	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html>
<html>
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
					document.getElementById('tinv').innerHTML =leselect;
				}
			}
			// Ici on va voir comment faire du post
			xhr.open("POST","Ajax_Mod_Inv.php",true);
			// ne pas oublier ça pour le post
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			// ne pas oublier de poster les arguments
			// ici, l'id du nom
			code = document.getElementById('code').value;
			//iddept = sel.options[sel.selectedIndex].value;
			xhr.send("code="+code);
        }
        </script>
</head>

<body>
<input type="text" name="code" id="code" onchange="go()"/>
<input type="button" value="Afficher" name="aff" onclick="go()" />

          <table id='tinv' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="18"><h5>Liste des Inventaires pour Modification</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center"><h5>Code </h5></td>
                <td  align="center" ><h5>Date</h5></td>
                <td  align="center"><h5> Caisse </h5></td>
                <td  align="center" ><h5>Solde SABC </h5></td>
                <td  align="center" ><h5> OM</h5></td>
                <td  align="center" ><h5> MOMO</h5></td>
                <td  align="center" ><h5>Credit Client</h5></td>
                <td  align="center" ><h5>Credit emballage</h5></td>
                <td  align="center" ><h5>Solde Banque</h5></td>
                <td  align="center" ><h5>Credit SABC</h5></td>
                <td  align="center" ><h5>Credit Banque</h5></td>
                <td  align="center" ><h5>Ristournes</h5></td>
                <td  align="center" ><h5>P. Bois</h5></td>
                <td  align="center" ><h5>P. Plastique(s)</h5></td>
                <td  align="center" ><h5>Emb. pleins</h5></td>
                <td  align="center" ><h5>Emb. Vides</h5></td>
                <td  align="center" ><h5>Modifier</h5></td>
                <td  align="center" ><h5>Imprimer</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT  * FROM INVENTAIRE WHERE STATUT="N" ORDER BY DATE DESC LIMIT 0,20' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_INV']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATE']).' '.$rslt['HEURE']; ?> </td>
                        <td  align="center"> <?php echo   number_format($rslt['SOLDECAISSE'], 0, ',', ' ') ; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['SOLDESABC'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['SOLDEOM'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['SOLDEMOMO'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['CREDITCLIENT'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['CREDITEMBALLAGE'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['SOLDEBANQUE'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['CREDITBRASSERIES'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['CREDITBANQUE'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['RISTOURNESCLIENTS'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['PALETTEBOIS'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['PALETTEPLASTIQUE'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['EMB_PLEIN'], 0, ',', ' '); ?> </td>  
                        <td  align="center"> <?php echo number_format($rslt['EMB_VIDE'], 0, ',', ' '); ?> </td>
                         <?php
                         //ici on verifie La date de l'appro
                         if (($rslt['DATE']= date("Y-m-d")) && ($_SESSION['habilitation']!='Administrateur' && $_SESSION['habilitation']!='DGA'))
                          {
 							  ?>
							  	<td  align="center"></td>
                              <?php
						   	
                          }
						  else
						  {
							  ?>
							  <td  align="center"> <a href="index.php?formulaire=Modification_Inv&Id=<?php echo $rslt['ID_INV'];?>"/> <img src="IMG/b_edit.png"/> </a></td>
                              <?php
						   }
						  ?>
                        
                        <td  align="center"> <a href="index.php?formulaire=Validation_Inv&Id=<?php echo $rslt['ID_INV'];?>"/> <img src="IMG/Liste.png"/> </a></td>
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
