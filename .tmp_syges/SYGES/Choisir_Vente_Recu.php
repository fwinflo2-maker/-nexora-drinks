<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"|| $_SESSION['habilitation']=="OPS" || $_SESSION['habilitation']=="Comptable"))
{

	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Choisir une Vente.</title>
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
					document.getElementById('tvente').innerHTML =leselect;
				}
			}
			// Ici on va voir comment faire du post
			xhr.open("POST","Ajax_Vente_Recu.php",true);
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

          <table id='tvente' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h5>Choisir une vente pour impression du reçu</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center" ><h5>Date de la vente </h5></td>
                <td  align="center"><h5>Client</h5></td>
                <td  align="center" ><h5>Observation </h5></td>
                <td  align="center" ><h5>Double</h5></td>
                <td  align="center" ><h5>Format A4</h5></td>
                <td  align="center" ><h5>Matricielle</h5></td>
				<td  align="center" ><h5>Ticket</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT  ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK, ST.ID_CLIENT, C.ID_CLIENT, C.NOM, ST.OBSERVATION, ST.STATUT FROM SORTIE_STOCK ST, CLIENT C WHERE  ST.ID_CLIENT =C.ID_CLIENT ORDER BY ST.DATESORTIESTOCK DESC LIMIT 0,30' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_SORTIESTOCK']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATESORTIESTOCK']); ?> </td>
                        <td  align="center"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center"> <?php echo $rslt['OBSERVATION']; ?> </td>
                        <?php
						if ($rslt['STATUT']=="V")
						{
							?>
                        	<td  align="center"> <a href="RecuDble.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						else
						{
							?>
                        	<td  align="center"> <a href="Recu_ProformaDble.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						if ($rslt['STATUT']=="V")
						{
							?>
                        	<td  align="center"> <a href="Recu.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						else
						{
							?>
                        	<td  align="center"> <a href="Recu_Proforma.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						if ($rslt['STATUT']=="V")
						{
							?>
                        	<td  align="center"> <a href="RecuMat.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						else
						{
							?>
                        	<td  align="center"> <a href="Recu_ProformaMat.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						if ($rslt['STATUT']=="V")
						{
							?>
                        	<td  align="center"> <a href="RecuTck.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						else
						{
							?>
                        	<td  align="center"> <a href="Recu_ProformaTck.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						?>
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
