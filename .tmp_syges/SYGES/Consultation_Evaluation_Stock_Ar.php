<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"  || $_SESSION['habilitation']=="Comptable"))
{
	include('Connexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Evaluation Chriffrée des stocks</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="10"><h3>Evaluation Chiffrée des Stocks</h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="10"><h4> ARTICLES</h4></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="3" align="center"><h4>ARTICLE</h4></td>
          	<td colspan="3" align="center"><h4>MAGASIN</h4></td>
          	<td colspan="3" align="center"><h4>FRIGO  </h4></td>
            <td colspan="" align="center"><h4>MT TOTAL</h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Code </h5> </td>
                <td align="center" ><h5>Conditionnement</h5> </td>
                <td align="center" ><h5>Libellé </h5> </td>
                <td  align="center"><h5>Qte </h5></td>
                <td  align="center"><h5>PU</h5></td>
                <td  align="center"><h5>Montant </h5></td>
                <td  align="center"><h5>Qte </h5></td>
                <td  align="center"><h5>PU</h5></td>
                <td  align="center"><h5>Montant</h5></td>
                <td  align="center"><h5>Montant Total</h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$totalcolis=0;
$totalprcolis=0;
$totalprbtle=0;
$totalbtle=0;
$motantcolis=0;
$motantbtle=0;
$montanttotal=0;
$sql = "SELECT * FROM ARTICLE ORDER BY ID_FAMILLE";
$reponse= $DataBase->query($sql);
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$prfrigo=number_format($rslt['PRIXREVIENT']/$rslt['NBREBTE'], 0, ',', ' ');
			$mtmag=$rslt['PRIXREVIENT']*$rslt['QTESTOCK'];
			$mtfr=$prfrigo*$rslt['STOCKFRIGO'];
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo $rslt['ID_ARTICLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['MARQUE'].' '.$rslt['NBREBTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['QTESTOCK']; ?> </td>
                        <td  align="center"> <?php echo $rslt['PRIXREVIENT'].' F'; ?> </td>
                        <td  align="center"> <?php echo number_format($mtmag, 0, ',', ' ').' F'; ?> </td>
                        <td  align="center"> <?php echo $rslt['STOCKFRIGO']; ?> </td>
                        <td  align="center"> <?php echo $prfrigo.' F'; ?></td>
                        <td  align="center"> <?php echo number_format($mtfr, 0, ',', ' ').' F'; ?></td>
                        <td  align="center"> <?php echo number_format($mtmag+$mtfr, 0, ',', ' ').' F'; ?></td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$totalcolis=$totalcolis+$rslt['QTESTOCK'];
				$totalprcolis=$totalprcolis+$rslt['PRIXREVIENT'];
				$motantcolis=$motantcolis+$mtmag;
				$totalbtle=$totalbtle+$rslt['STOCKFRIGO'];
				$totalprbtle=$totalprbtle+$prfrigo;
				$motantbtle=$motantbtle+$mtfr;
				$montanttotal=$montanttotal+$mtmag+$mtfr;
		 }
?>
<tr>
	<td colspan="2" align="center"><h4>Nombre d'article:<?php echo $nbre; ?> </h4></td>
    <td colspan="" align="center"><h4>Totaux : </h4></td>
    <td colspan="" align="center"><h4><?php echo $totalcolis; ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo number_format($totalprcolis, 0, ',', ' ').' F'; ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo number_format($motantcolis, 0, ',', ' ').' F'; ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo $totalbtle; ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo $totalprbtle; ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo number_format($motantbtle, 0, ',', ' ').' F'; ?> </h4></td>
    <td colspan="" align="center" bgcolor="#FF6600"><h3><?php echo number_format($montanttotal, 0, ',', ' ').' F'; ?> </h3></td>
</tr>
</table>
<table id='listeemballage' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h4> EMBALLAGES</h4></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="2" align="center"><h4>EMBALLAGE</h4></td>
          	<td colspan="3" align="center"><h4>STOCK TOTAL</h4></td>
          	<td colspan="3" align="center"><h4>STOCK DISPONIBLE  </h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Code </h5> </td>
                <td align="center" ><h5>Libellé </h5> </td>
                <td  align="center"><h5>Qte </h5></td>
                <td  align="center"><h5>PU</h5></td>
                <td  align="center"><h5>Montant </h5></td>
                <td  align="center"><h5>Qte </h5></td>
                <td  align="center"><h5>PU</h5></td>
                <td  align="center"><h5>Montant</h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre2=0;
$mtstock=0;
$mtdispo=0;
$ttmtstock=0;
$ttmtdispo=0;
$totalembdispo=0;
$totalembstock=0;

$sql = "SELECT * FROM EMBALLAGE";
$reponse= $DataBase->query($sql);
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$mtstock=$rslt['MT_CONSIGNE']*$rslt['QTE'];
			$mtdispo=$rslt['MT_CONSIGNE']*$rslt['QTESTOCK'];
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo $rslt['ID_EMBALLAGE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['QTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['MT_CONSIGNE'].' F'; ?> </td>
                        <td  align="center"> <?php echo number_format($mtstock, 0, ',', ' ').' F'; ?> </td>
                        <td  align="center"> <?php echo $rslt['QTESTOCK']; ?> </td>
                       <td  align="center"> <?php echo $rslt['MT_CONSIGNE'].' F'; ?> </td>
                        <td  align="center"> <?php echo number_format($mtdispo, 0, ',', ' ').' F'; ?></td>
                     </tr>
                <?php
				$i++;
				$nbre2++;
				$ttmtstock=$ttmtstock+$mtstock;
				$ttmtdispo=$ttmtdispo+$mtdispo;
				$totalembdispo=$totalembdispo+$rslt['QTESTOCK'];
				$totalembstock=$totalembstock+$rslt['QTE'];
		 }
?>
<tr>
	<td colspan="" align="center"><h4>Nombre d'emballage:<?php echo $nbre2; ?> </h4></td>
    <td colspan="" align="center"><h4>Totaux : </h4></td>
    <td colspan="" align="center"><h4><?php echo $totalembstock; ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo '//'; ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo number_format($ttmtstock, 0, ',', ' ').' F'; ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo $totalembdispo; ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo '//'; ?> </h4></td>
    <td colspan="" align="center"  bgcolor="#FF6600"><h3><?php echo number_format($ttmtdispo, 0, ',', ' ').' F'; ?> </h3></td>
</tr>
<tr>
	<td><a href="Etat_Evaluation_Stock_Ar.php"/><input type="button" value="Imprimer" /></td></a>
    <td colspan="3" align="center"><h4><?php echo 'MONTANT TOTAL DES STOCKS '; ?> </h4></td>
    <td colspan="4" align="center"  bgcolor="#FF6600"><h3><?php echo number_format($ttmtdispo+$montanttotal, 0, ',', ' ').' F'; ?> </h3></td>
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