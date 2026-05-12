<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Comptable"  || $_SESSION['habilitation']=="CC"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	if ($_GET['Clt'] != 'TOUS')
	{
		$sql='SELECT V.DATESORTIESTOCK, V.ID_SORTIESTOCK, A.LIBELLE, A.NBREBTE, A.MARQUE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXREVIENT, AV.PRIXVENTE, V.ID_CLIENT, V.STATUT, C.NOM FROM SORTIE_STOCK V, ARTICLE A, ARTICLEVENDU AV, CLIENT C WHERE V.ID_CLIENT=C.ID_CLIENT AND  A.ID_ARTICLE=AV.ID_ARTICLE AND V.ID_SORTIESTOCK=AV.ID_SORTIESTOCK AND V.ID_CLIENT="'.$_GET['Clt'].'" AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
	}
	else
	{
				$sql='SELECT V.DATESORTIESTOCK, V.ID_SORTIESTOCK, A.LIBELLE, A.NBREBTE, A.MARQUE, AV.ID_ARTICLE, AV.QTESORTIE, AV.PRIXREVIENT, AV.PRIXVENTE, V.ID_CLIENT, V.STATUT, C.NOM FROM SORTIE_STOCK V, ARTICLE A, ARTICLEVENDU AV, CLIENT C WHERE V.ID_CLIENT=C.ID_CLIENT AND  A.ID_ARTICLE=AV.ID_ARTICLE AND V.ID_SORTIESTOCK=AV.ID_SORTIESTOCK AND  V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
	}


	if ($_GET['Clt'] != 'TOUS')
	{
			$nom=$rslt2['NOM'];
	}
	else
	{
			$nom='Tous les Clients';
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de la Liste Detaillee des Achats d'un client</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h3>Etat detaille des Achats client (s) </h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          		<td colspan="3"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
                <td colspan="2" align="center"><h5>Code : <?php echo $_GET['Clt']; ?></h5></td>
                <td colspan="3" align="center"><h5>Client : <?php echo utf8_decode($nom); ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" width="10%"><h5>Date </h5> </td>
                <td align="left" width="8%"><h5>Reference </h5> </td>
                <td align="left" width="15%"><h5> Article</h5> </td>
                <td align="center" width="5%"><h5>Qte </h5> </td>
                <td align="center" width="8%"><h5>TT Prix Revient </h5> </td>
                <td align="center" width="8%"><h5>TT Prix Vente </h5> </td>
                <td align="center" width="8%"><h5>Marge Brute </h5> </td>
                <td align="center" width="8%"><h5>Client </h5> </td>
			</tr>
            
<?php
$TTPV=0;
$TTPR=0;
$NbeArt=0;
$marge=0;
$couleur = "darkgray";
$i = 0;
$nbre=0;
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATESORTIESTOCK']); ?> </td>
                        <td  align="left"> <?php echo $rslt['ID_SORTIESTOCK']; ?> </td>
                        <td  align="left"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['QTESORTIE'];?> </td>
                        <td  align="center"> <?php echo number_format($rslt['PRIXREVIENT'], 0, ',', ' ');?> </td>
                        <td  align="center"> <?php echo number_format($rslt['PRIXVENTE'], 0, ',', ' ');?> </td>
                        <td  align="center"> <?php echo number_format($rslt['PRIXVENTE']-$rslt['PRIXREVIENT'], 0, ',', ' ');?> </td>
                        <td  align="center"> <?php echo utf8_decode($rslt['NOM']);?> </td>
                     </tr>
                <?php
				$i++;
				$marge=$marge+($rslt['PRIXVENTE']-$rslt['PRIXREVIENT']);
				$nbre++;
				$TTPV = ($TTPV + $rslt['PRIXVENTE']);
				$TTPR = ($TTPR + $rslt['PRIXREVIENT']);
				$NbeArt = ($NbeArt+$rslt['QTESORTIE']);
		 }
?>
<tr>
<?php

			?>
			<td align="center"><a href="Liste_Detaille_Vente_Client.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Clt=<?php echo $_GET['Clt'];?>"/><input type="button" value="Imprimer" /></a></td>


	
	<td colspan="2"><h4>Totaux :</h4></td>
    <td align="center"><h4><?php echo $NbeArt; ?> </h4></td>
    <td align="center"><h4><?php echo number_format($TTPR, 0, ',', ' ').' FCFA'; ?> </h4></td>
    <td align="center"><h4><?php echo number_format($TTPV, 0, ',', ' ').' FCFA'; ?> </h4></td>
    <td align="center"><h4><?php echo number_format($marge, 0, ',', ' ').' FCFA'; ?> </h4></td>
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