<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	if ($_GET['Stat']=='Mixte')
	{	
	$sql='SELECT R.ID_REGLEMENT, R.DATEAVANCE, R.ID_SORTIESTOCK, R.STATUT, R.MONTANT, R.MTAVANCE, R.MTRESTANT, R.USER, ST.DATESORTIESTOCK, C.NOM FROM CLIENT C, SORTIE_STOCK ST, REGLEMENT R WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.ID_SORTIESTOCK=R.ID_SORTIESTOCK AND R.DATEAVANCE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY R.ID_REGLEMENT' ;
	}
	else
		if ($_GET['Stat']=='Avance')
		{
		$sql='SELECT R.ID_REGLEMENT, R.DATEAVANCE, R.ID_SORTIESTOCK, R.STATUT, R.MONTANT, R.MTAVANCE, R.MTRESTANT, R.USER, ST.DATESORTIESTOCK, C.NOM FROM CLIENT C, SORTIE_STOCK ST, REGLEMENT R WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.ID_SORTIESTOCK=R.ID_SORTIESTOCK AND R.STATUT="Avance" AND R.DATEAVANCE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY R.ID_REGLEMENT' ;
		}
		else
		{
		$sql='SELECT R.ID_REGLEMENT, R.DATEAVANCE, R.ID_SORTIESTOCK, R.STATUT, R.MONTANT, R.MTAVANCE, R.MTRESTANT, R.USER, ST.DATESORTIESTOCK, C.NOM FROM CLIENT C, SORTIE_STOCK ST, REGLEMENT R WHERE C.ID_CLIENT=ST.ID_CLIENT AND ST.ID_SORTIESTOCK=R.ID_SORTIESTOCK AND R.STATUT="Paye" AND R.DATEAVANCE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY R.ID_REGLEMENT' ;
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de l'Etat des réglements de facture</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h3>Etat des réglements des factures  </h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          		<td colspan="5"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
                <td colspan="3" align="center"><h5>Statut : <?php echo $_GET['Stat']; ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>N° </h5> </td>
                <td align="center" ><h5>Date </h5> </td>
                <td align="center" ><h5>Facture </h5> </td>
                <td align="left"><h5>Client </h5> </td>
                <td align="center" ><h5>Montant </h5> </td>
                <td align="center" ><h5>Avance </h5> </td>
                <td  align="center"><h5>Reste </h5></td>
				<td align="center" ><h5>Statut </h5></td>
			</tr>
            
		<?php
        $couleur = "darkgray";
        $i = 0;
        $nbre=0;
		$montanttt=0;
		$avancett=0;
 		$restett=0;
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
                		<td  align="center"> <?php echo $rslt['ID_REGLEMENT']; ?> </td>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATEAVANCE']); ?> </td>
                        <td  align="center"> <?php echo $rslt['ID_SORTIESTOCK']; ?> </td>
                        <td  align="left"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['MONTANT'], 0, ',', ' ').' F'; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['MTAVANCE'], 0, ',', ' ').' F'; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['MTRESTANT'], 0, ',', ' ').' F'; ?> </td>
                        <td  align="center"> <?php echo $rslt['STATUT']; ?> </td>
                     </tr>
                <?php
			  $i++;
			  $nbre++;
			  $montanttt=$montanttt+$rslt['MONTANT'];
			  $avancett=$avancett+$rslt['MTAVANCE'];
			  $restett=$restett+$rslt['MTRESTANT'];
		 }
?>
<tr>
	<td><a href="Etat_Reglement.php?DateD=<?php echo $_GET["DateD"];?>&DateF=<?php echo $_GET["DateF"];?>&Statut=<?php echo $_GET['Stat'];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="3" align="center"><h4>Nombre de Reglement(s) :  <?php echo $nbre; ?> </h4></td>
    <td align="center" ><h4><?php echo number_format($montanttt, 0, ',', ' ').' F'; ?></h4></td>
    <td align="center" ><h4><?php echo number_format($avancett, 0, ',', ' ').' F'; ?></h4></td>
    <td align="center" ><h4><?php echo number_format($restett, 0, ',', ' ').' F'; ?></h4></td>
    <td align="center" ><h4>//</h4></td>
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