<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="OPS" || $_SESSION['habilitation']=="Comptable"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	if ($_GET['Stat']=='Mixte')
	{	
	$sql='SELECT V.ID_SORTIESTOCK, V.DATESORTIESTOCK, V.MTFACTURE, V.FRAISENLEVEMENT, V.ID_CLIENT, V.OBSERVATION, V.STATUT, C.ID_CLIENT, C.NOM FROM SORTIE_STOCK V, CLIENT C WHERE V.ID_CLIENT=C.ID_CLIENT AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
	}
	else
		if ($_GET['Stat']=='N')
		{
		$sql='SELECT V.ID_SORTIESTOCK, V.DATESORTIESTOCK, V.MTFACTURE, V.FRAISENLEVEMENT, V.ID_CLIENT, V.OBSERVATION, V.STATUT, C.ID_CLIENT, C.NOM FROM SORTIE_STOCK V, CLIENT C WHERE V.STATUT="N" AND V.ID_CLIENT=C.ID_CLIENT AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
		}
		else
		{
		$sql='SELECT V.ID_SORTIESTOCK, V.DATESORTIESTOCK, V.MTFACTURE, V.FRAISENLEVEMENT, V.ID_CLIENT, V.OBSERVATION, V.STATUT, C.ID_CLIENT, C.NOM FROM SORTIE_STOCK V, CLIENT C WHERE V.STATUT="V" AND V.ID_CLIENT=C.ID_CLIENT AND V.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY V.ID_SORTIESTOCK' ;
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de la Liste des Ventes</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="10"><h3>Etat des ventes  </h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          		<td colspan="5"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
                <td colspan="5" align="center"><h5>Statut : <?php echo $_GET['Stat']; ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Date de vente </h5> </td>
                <td align="left" ><h5>Code </h5> </td>
                <td align="left" ><h5>Client </h5> </td>
                <td align="center"><h5>TT PR Art. </h5> </td>
                <td align="center" ><h5>TT PV Art. </h5> </td>
                <td align="center" ><h5>Marge Brute </h5> </td>
                <td align="center" ><h5>Frais Enlev. </h5> </td>
                <td align="center" ><h5>Mt Facture</h5> </td>
                <td  align="center"><h5>Statut </h5></td>
				<td align="center" ><h5>Observation </h5></td>
			</tr>
            
<?php
$TTPA=0;
$TTPR=0;
$TTPV=0;
$TTB=0;
$TTFE=0;
$TTMTF=0;
$couleur = "darkgray";
$i = 0;
$nbre=0;
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
	//Ici on recupere les articles de chacune des ventes puis on somme les prix et benef
	 $PRIXACHAT=0;
	 $PRIXREVIENT=0;
	 $PRIXVENTE=0;
	 $BENEF=0;
	 $sql1='SELECT ID_SORTIESTOCK, PRIXREVIENT, PRIXVENTE FROM  ARTICLEVENDU WHERE ID_SORTIESTOCK= "'.$rslt['ID_SORTIESTOCK'].'" ' ;
	 $reponse1= $DataBase->query($sql1);
  while($rslt1= $reponse1->fetch())
 {
	 $PRIXREVIENT= ($PRIXREVIENT + $rslt1['PRIXREVIENT']);
	 $PRIXVENTE= ($PRIXVENTE + $rslt1['PRIXVENTE']);	
 	 $BENEF= ($BENEF + ($rslt1['PRIXVENTE']-$rslt1['PRIXREVIENT']));	 
 } 
	 //
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATESORTIESTOCK']); ?> </td>
                        <td  align="left"> <?php echo $rslt['ID_SORTIESTOCK']; ?> </td>
                        <td  align="left"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center"> <?php echo $PRIXREVIENT; ?> </td>
                        <td  align="center"> <?php echo $PRIXVENTE; ?> </td>
                        <td  align="center"> <?php echo $BENEF; ?> </td>
                        <td  align="center"> <?php echo $rslt['FRAISENLEVEMENT']; ?> </td>
                        <td  align="center"> <?php echo $rslt['MTFACTURE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['STATUT']; ?> </td>
                        <td  align="left"> <?php echo $rslt['OBSERVATION']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$TTPR = ($TTPR + $PRIXREVIENT);
				$TTPV = ($TTPV + $PRIXVENTE);
				$TTB = ($TTB + $BENEF);
				$TTFE=$TTFE+$rslt['FRAISENLEVEMENT'];
				$TTMTF=$TTMTF+$rslt['MTFACTURE'];
		 }
?>
<tr>
	<td><a href="Liste_Vente.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Statut=<?php echo $_GET['Stat'];?>"/><input type="button" value="Imprimer" /></td></a>
	<td><h4>Nombre de vente :  <?php echo $nbre; ?> </h4></td>
    <td align="center"><h4>Totaux : </h4></td>
    <td align="center"><h4><?php echo number_format($TTPR, 0, ',', ' '); ?> </h4></td>
    <td align="center"><h4><?php echo number_format($TTPV, 0, ',', ' '); ?> </h4></td>
    <td align="center"><h4><?php echo number_format($TTB, 0, ',', ' '); ?> </h4></td>
    <td align="center"><h4><?php echo number_format($TTFE, 0, ',', ' '); ?> </h4></td>
    <td align="center"><h4><?php echo number_format($TTMTF, 0, ',', ' '); ?> </h4></td>
    <td colspan="2"></td>
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