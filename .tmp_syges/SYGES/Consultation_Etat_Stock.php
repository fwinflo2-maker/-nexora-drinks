<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="OPS" || $_SESSION['habilitation']=="Comptable" || $_SESSION['habilitation']=="Magasinier" ))
{
	include('Connexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de l'etat des stocks</title>
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
					document.getElementById('tstock').innerHTML =leselect;
				}
			}
			// Ici on va voir comment faire du post
			xhr.open("POST","AjaxStock_Article.php",true);
			// ne pas oublier ça pour le post
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			// ne pas oublier de poster les arguments
			// ici, l'id du nom
			famille = document.getElementById('famille').value;
			//iddept = sel.options[sel.selectedIndex].value;
			xhr.send("famille="+famille);
        }
        </script>
</head>

<body>
<tr>
	<td><label for="famille" style=" width:350px; text-align:right"> Famille   :  </label></td>
    <td><select name="famille" id="famille" style="width:300px;" onchange="go()">
    <option>TOUTES</option>
    <?php
    	$sql = " select id_famille ,libelle from famille order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_famille"]."'>";
		 echo $rslt["libelle"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>
</tr>
<input type="button" value="Afficher" name="aff" onclick="go()" />
<table id='tstock' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="9"><h3>Etat des stocks  </h3></td>
          </tr>

            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$colismag=0;
$colisfr=0;
$cassier=0;
$sql = "SELECT DISTINCT ID_FAMILLE FROM ARTICLE";
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
{
	
	$sscasier=0;
	$ssnbre=0;
	$sscolis=0;
	$sscolismag=0;
	$sscolisfr=0;
	?>
	<tr>
		<td colspan="6" align="center"><h4>Famille : <?php echo $rslt['ID_FAMILLE']; ?> </h4></td>
    </tr>
    <tr bgcolor="#CCCCCC">
        <td align="center" ><h5>Code </h5> </td>
        <td align="center" ><h5>Conditionnement</h5> </td>
        <td align="center" ><h5>Libellé </h5> </td>
        <td  align="center"><h5>Magasin </h5></td>
        <td  align="center"><h5>Frigo(Bouteille) </h5></td>
        <td align="center" ><h5>Statut </h5></td>
    </tr>

    <?php
	$sql2 = "SELECT * FROM ARTICLE WHERE ID_FAMILLE='".$rslt['ID_FAMILLE']."'ORDER BY ID_FAMILLE";
	$reponse2= $DataBase->query($sql2);
	while($rslt2= $reponse2->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo $rslt2['ID_ARTICLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt2['MARQUE'].' '.$rslt2['NBREBTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt2['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt2['QTESTOCK']; ?> </td>
                        <td  align="center"> <?php echo $rslt2['STOCKFRIGO']; ?> </td>
                        <td  align="center"> <?php echo $rslt2['STATUT']; ?> </td>
                     </tr>
                <?php
				$i++;
				$ssnbre++;
				$sscolismag=$sscolismag+$rslt2['QTESTOCK'];
				$sscolisfr=$sscolisfr+$rslt2['STOCKFRIGO'];
				//on compte les casiers
				  if(($rslt2['MARQUE']=="CASIER") || ($rslt2['MARQUE']=="casier")|| ($rslt2['MARQUE']=="CASIERS")|| ($rslt2['MARQUE']=="casiers"))
				  {
					  $sscasier=$sscasier+$rslt2['QTESTOCK'];
				  }

		}
		?>
		<tr>
            <td colspan="2" align="center"><h4>Sous Total Famille : <?php echo $rslt['ID_FAMILLE']; ?> </h4></td>
            <td colspan="" align="center"><h4>Articles(s) : <?php echo $ssnbre; ?> </h4></td>
            <td colspan="" align="center"><h4>Colis au Magasin : <?php echo $sscolismag; ?> </h4></td>
            <td colspan="" align="center"><h4>Bouteille(s) au Frigo : <?php echo $sscolisfr; ?> </h4></td>
            <td colspan="" align="center"><h4>Casier(s) : <?php echo $sscasier; ?> </h4></td>
        </tr>
		<?php
				
				$nbre=$nbre+$ssnbre;
				$colismag=$colismag+$sscolismag;
				$colisfr=$colisfr+$sscolisfr;
				$cassier=$cassier+$sscasier;
				
		 
}
?>
<tr>
    <td align="center"><a href="Etat_Stock.php?famille=TOUTES"/><input type="button" value="Imprimer" /></td></a>
    <td colspan="" align="center"><h4>TOTAUX :  </h4></td>
	<td colspan="" align="center"><h4>Nombre d'article(s) : <?php echo $nbre; ?> </h4></td>
    <td colspan="" align="center"><h4>Colis au Magasin : <?php echo $colismag; ?> </h4></td>
    <td colspan="" align="center"><h4> Bouteille(s) au Frigo : <?php echo $colisfr; ?> </h4></td>
    <td colspan="" align="center"><h4> Casier(s) : <?php echo $cassier; ?> </h4></td>
</tr>
</table>
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