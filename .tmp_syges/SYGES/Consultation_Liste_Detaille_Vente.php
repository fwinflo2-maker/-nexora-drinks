<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	if ($_GET['Stat']=='Mixte')
	{	
	$sql='SELECT V.DATESORTIESTOCK, V.ID_SORTIESTOCK, A.LIBELLE, A.MARQUE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXREVIENT, AV.PRIXVENTE FROM SORTIE_STOCK V, ARTICLE A, ARTICLEVENDU AV WHERE  A.ID_ARTICLE=AV.ID_ARTICLE AND V.ID_SORTIESTOCK=AV.ID_SORTIESTOCK AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
	}
	else
		if ($_GET['Stat']=='N')
		{
	$sql='SELECT V.DATESORTIESTOCK, V.ID_SORTIESTOCK, A.LIBELLE, A.MARQUE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXREVIENT, AV.PRIXVENTE FROM SORTIE_STOCK V, ARTICLE A, ARTICLEVENDU AV WHERE  A.ID_ARTICLE=AV.ID_ARTICLE AND V.STATUT="N" AND V.ID_SORTIESTOCK=AV.ID_SORTIESTOCK AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
		}
		else
		{
	$sql='SELECT V.DATESORTIESTOCK, V.ID_SORTIESTOCK, A.LIBELLE, A.MARQUE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXREVIENT, AV.PRIXVENTE FROM SORTIE_STOCK V, ARTICLE A, ARTICLEVENDU AV WHERE  A.ID_ARTICLE=AV.ID_ARTICLE AND V.STATUT="V" AND V.ID_SORTIESTOCK=AV.ID_SORTIESTOCK AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de la Liste Detaillee des Ventes</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="9"><h3>Etat detaille des ventes  </h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          		<td colspan="4"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
                <td colspan="4" align="center"><h5>Statut : <?php echo $_GET['Stat']; ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" width="15%"><h5>Date de vente </h5> </td>
                <td align="left" width="8%"><h5>Vente </h5> </td>
                <td align="left" width="15%"><h5>Conditionnement </h5> </td>
                <td align="left" width="15%"><h5>Libelle</h5> </td>
                <td align="center" width="5%"><h5>Qte </h5> </td>
                <td align="center" width="12%"><h5>TT Prix Revient </h5> </td>
                <td align="center" width="10%"><h5>TT Prix Vente </h5> </td>
                <td align="center" width="10%"><h5>TT Benef </h5> </td>
			</tr>
            
<?php
$TTPR=0;
$TTPV=0;
$TTB=0;
$NbeArt=0;
$couleur = "darkgray";
$i = 0;
$nbre=0;
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$Benef=$rslt['PRIXVENTE']-$rslt['PRIXREVIENT'];
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATESORTIESTOCK']); ?> </td>
                        <td  align="left"> <?php echo $rslt['ID_SORTIESTOCK']; ?> </td>
                        <td  align="left"> <?php echo $rslt['MARQUE']; ?> </td>
                        <td  align="left"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['QTESORTIE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['PRIXREVIENT']; ?> </td>
                        <td  align="center"> <?php echo $rslt['PRIXVENTE']; ?> </td>
                        <td  align="center"> <?php echo $Benef ; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$TTPR = ($TTPR + $rslt['PRIXREVIENT']);
				$TTPV = ($TTPV + $rslt['PRIXVENTE']);
				$TTB = ($TTB + $Benef);
				$NbeArt = ($NbeArt+$rslt['QTESORTIE']);
		 }
?>
<tr>
<?php

			if($_SESSION['habilitation']=='Administrateur')
			{
			?>
			<td><a href="Liste_Detaille_Vente.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Statut=<?php echo $_GET['Stat'];?>"/><input type="button" value="Imprimer" /></a></td>
            <?php
			}
			if	($_SESSION['habilitation']=='Gerant')
			{
			?>
			<td><a href="Liste_Detaille_Vente.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Statut=<?php echo $_GET['Stat'];?>"/><input type="button" value="Imprimer" /></a></td>
            <?php
			}

?>
	
	<td colspan="2"><h4>Nbre Articles :<?php echo $nbre; ?> </h4></td>
    <td align="center"><h4>Totaux : </h4></td>
    <td align="center"><h4><?php echo $NbeArt; ?> </h4></td>
    <td align="center"><h4><?php echo $TTPR; ?> </h4></td>
    <td align="center"><h4><?php echo $TTPV; ?> </h4></td>
    <td align="center"><h4><?php echo $TTB; ?> </h4></td>
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