<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" )||($_SESSION['habilitation']=="Caissier")||($_SESSION['habilitation']=="Comptable"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	if ($_GET['Stat']=='Mixte')
	{	
	
	$sql='SELECT C.ID_CHARGE, C.ID_TYPECHARGE, C.DESCRIPTION, C.MONTANT, C.DATE_CHARGE, C.STATUT, TC.LIBELLE, TC.ID_TYPECHARGE FROM CHARGE C, TYPE_CHARGE TC WHERE C.ID_TYPECHARGE=TC.ID_TYPECHARGE AND C.DATE_CHARGE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY C.ID_CHARGE' ;
	
	}
	else
		if ($_GET['Stat']=='N')
		{
			$sql='SELECT C.ID_CHARGE, C.ID_TYPECHARGE, C.DESCRIPTION, C.MONTANT, C.DATE_CHARGE , C.STATUT, TC.LIBELLE, TC.ID_TYPECHARGE FROM CHARGE C, TYPE_CHARGE TC WHERE C.ID_TYPECHARGE=TC.ID_TYPECHARGE  AND C.STATUT="N" AND C.DATE_CHARGE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY C.ID_CHARGE' ;
		}
		else
			{
			$sql='SELECT C.ID_CHARGE, C.ID_TYPECHARGE, C.DESCRIPTION, C.MONTANT, C.DATE_CHARGE , C.STATUT , TC.LIBELLE, TC.ID_TYPECHARGE FROM CHARGE C, TYPE_CHARGE TC WHERE C.ID_TYPECHARGE=TC.ID_TYPECHARGE  AND C.STATUT="V" AND C.DATE_CHARGE BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY C.ID_CHARGE' ;
			}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de L'etat des charges</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h3>Etat des charges  </h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          		<td colspan=""><h5>Période  </h5></td>
                <td colspan=""><h5>Du : <?php echo dateFormatFrancais($Debut); ?> </h5></td>
          		<td colspan=""><h5>Au : <?php echo dateFormatFrancais($Fin); ?> </h5></td>
                <td colspan=""><h5>Statut : </h5></td>
                <td colspan="4" align="left"><h5><?php echo $_GET['Stat']; ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" width="10%"><h5>Date  </h5> </td>
                <td align="center" width="17%"><h5>Libelle </h5> </td>
                <td align="center" width="25%"><h5>Description </h5> </td>
                <td align="center" width="25%"><h5>Montant </h5> </td>
                <td  align="center"><h5>Statut </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$MT=0;
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_CHARGE']); ?> </td>
                        <td  align="center"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['DESCRIPTION']; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['MONTANT'], 0, ',', ' ').' FCFA'; ?> </td>
                        <td  align="left"> <?php echo $rslt['STATUT']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$MT=$MT+$rslt['MONTANT'];
		 }
?>
<tr>
	<td><a href="Etat_Charge.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Statut=<?php echo $_GET['Stat'];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="2" align="center"><h4>Nombre des charges :<?php echo $nbre; ?> </h4></td>
    <td colspan="3" align="center"><h4>Montant des Charges  :  <?php echo number_format($MT, 0, ',', ' ').' FCFA'; ?> </h4></td>
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