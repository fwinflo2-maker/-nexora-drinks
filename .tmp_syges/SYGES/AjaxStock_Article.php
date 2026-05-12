<?php
	include("Connexion.php");
	include('fonctions.php');
?>
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
if ($_POST['famille']=='TOUTES')
{
	$sql = "SELECT DISTINCT ID_FAMILLE FROM ARTICLE";
}
else
{
	$sql = "SELECT DISTINCT ID_FAMILLE FROM ARTICLE  WHERE ID_FAMILLE='".$_POST['famille']."'";
}

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
	$sql2 = "SELECT * FROM ARTICLE WHERE ID_FAMILLE='".$rslt['ID_FAMILLE']."' ORDER BY ID_FAMILLE";
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
    <td align="center"><a href="Etat_Stock.php?famille=<?php echo $_POST['famille']; ?>"/><input type="button" value="Imprimer" /></td></a>
    <td colspan="" align="center"><h4>TOTAUX :  </h4></td>
	<td colspan="" align="center"><h4>Nombre d'article(s) : <?php echo $nbre; ?> </h4></td>
    <td colspan="" align="center"><h4>Colis au Magasin : <?php echo $colismag; ?> </h4></td>
    <td colspan="" align="center"><h4> Bouteille(s) au Frigo : <?php echo $colisfr; ?> </h4></td>
    <td colspan="" align="center"><h4> Casier(s) : <?php echo $cassier; ?> </h4></td>
</tr>
</table>