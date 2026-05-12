<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"))
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
			xhr.open("POST","Ajax_Vente_Reglement.php",true);
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
<a href="index.php?formulaire=Enreg_Reglement"><input type="button" value="Nouvelle Facture" name="nouveau" style="margin-left:650px; background:#F00; width:140px;"/> </a>
<table id='tvente' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="7"><h5>Choisir une Vente pour Reglement</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" ><h5>Vente </h5></td>
                <td  align="center" ><h5>Date</h5></td>
                <td  align="center"><h5>Client</h5></td>
                <td  align="center" ><h5>MT Total</h5></td>
                <td  align="center" ><h5>MT Restant </h5></td>
                <td  align="center" ><h5>Observation </h5></td>
                <td  align="center" ><h5>Selectionner</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
$i = 0;
$MTVENTE = 0;
$sql='SELECT  DISTINCT (ST.ID_SORTIESTOCK) , ST.DATESORTIESTOCK, ST.MTFACTURE, C.NOM, ST.OBSERVATION FROM SORTIE_STOCK ST, CLIENT C, REGLEMENT REG WHERE  ST.ID_CLIENT =C.ID_CLIENT AND ST.ID_SORTIESTOCK=REG.ID_SORTIESTOCK ORDER BY ST.DATESORTIESTOCK DESC LIMIT 0,30' ;
	$reponse= $DataBase->query($sql);
	while($rslt= $reponse->fetch())
	{
		//on recupere le mt restant et de la vente dans le dernier paiement
		$reste=0;
		$sql5='SELECT MAX(ID_REGLEMENT) AS ID FROM REGLEMENT WHERE ID_SORTIESTOCK="'.$rslt['ID_SORTIESTOCK'].'" ' ;
		$reponse5= $DataBase->query($sql5);
		while($rslt5= $reponse5->fetch())
			{
				$sql6='SELECT MTRESTANT, MONTANT FROM REGLEMENT WHERE ID_REGLEMENT="'.$rslt5['ID'].'" ' ;
				$reponse6= $DataBase->query($sql6);
				while($rslt6= $reponse6->fetch())
				{
					$reste=$rslt6['MTRESTANT'];
					$montant=$rslt6['MONTANT'];
				}
			}
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_SORTIESTOCK']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATESORTIESTOCK']); ?> </td>
                        <td  align="center"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center"> <?php echo number_format($montant, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($reste, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo $rslt['OBSERVATION']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Consulter_Reglement&Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
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
