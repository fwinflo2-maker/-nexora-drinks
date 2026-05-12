<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Comptable"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de l'etat des pertes par article</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="6"><h3>Etat des pertes par article</h3></td>
          </tr>
          <tr bgcolor="#CCCCCC">
                <td colspan="6" align="center" ><h5>Période : Du : <?php echo dateFormatFrancais($Debut); ?>  Au : <?php echo dateFormatFrancais($Fin); ?></h5>
                </td>

          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Code </h5> </td>
                <td align="center" ><h5>Libellé</h5> </td>
                <td  align="center"><h5>Qté Vendue </h5></td>
                <td align="center"><h5>TT Prix Revient </h5> </td>
                <td align="center" ><h5>TT Prix Vente </h5> </td>
                <td align="center" ><h5>TT Benef </h5> </td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$QTE=0;
$TTPV=0;
$TTPR=0;
$TTB=0;
$nbre=0;
$MT=0;
$cod='PER';
//Ici on recupre la liste sans doublons des articles vendus dans la periode 
 $sql='SELECT DISTINCT  AR.ID_ARTICLE FROM ARTICLEVENDU_FRIGO AR, SORTIE_STOCK_FRIGO ST  WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" AND  ST.ID_SORTIESTOCK LIKE "%'.$cod.'%" ORDER BY AR.ID_ARTICLE';
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
//Ici on recupere les quantites de la meme article puis on somme
$qte=0;
$PRIXVENTE=0;
$PRIXREVIENT=0;
$BENEF=0;
$sql1 = 'SELECT  AR.ID_ARTICLE, AR.QTESORTIE,AR.PRIXVENTE,AR.PRIXREVIENT, AR.ID_SORTIESTOCK, ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK FROM ARTICLEVENDU_FRIGO AR, SORTIE_STOCK_FRIGO ST WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AR.ID_ARTICLE="'.$rslt['ID_ARTICLE'].'" AND ST.STATUT="V" AND  ST.ID_SORTIESTOCK LIKE "%'.$cod.'%"';
$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$qte=$qte+$rslt1['QTESORTIE'];
			$PRIXVENTE=$PRIXVENTE+$rslt1['PRIXVENTE'];
	 		$PRIXREVIENT= ($PRIXREVIENT + $rslt1['PRIXREVIENT']);
	 		$BENEF= ($PRIXVENTE-$PRIXREVIENT);
		}
		
//ici on recupere le libelle et la marque de l'article
$sql2 = 'SELECT  LIBELLE FROM ARTICLE WHERE ID_ARTICLE="'.$rslt['ID_ARTICLE'].'"';
$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
    		$libelle=$rslt2['LIBELLE'];
		}
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo $rslt['ID_ARTICLE']; ?> </td>
                        <td  align="center"> <?php echo $libelle; ?> </td>
                        <td  align="center"> <?php echo $qte; ?> </td>
                        <td  align="center"> <?php echo $PRIXREVIENT.' F'; ?> </td>
                        <td  align="center"> <?php echo $PRIXVENTE.' F'; ?> </td>
                        <td  align="center"> <?php echo $BENEF.' F'; ?> </td>
                     </tr>
                <?php
				$i++;
				$TTPV = ($TTPV + $PRIXVENTE);
				$TTPR = ($TTPR + $PRIXREVIENT);
				$TTB = ($TTB + $BENEF);
				$QTE=$QTE+$qte;
		 }
?>
<tr>
	<td><a href="Etat_Perte.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="" align="center"><h4>Totaux : </h4></td>
    <td colspan="" align="center"><h4><?php echo $QTE ; ?></h4></td>
    <td colspan="" align="center"><h4><?php echo number_format($TTPR, 0, ',', ' ').' FCFA'; ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo number_format($TTPV, 0, ',', ' ').' FCFA'; ?> </h4></td>
    <td colspan="" align="center"><h4><?php echo number_format($TTB, 0, ',', ' ').' FCFA'; ?> </h4></td>
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