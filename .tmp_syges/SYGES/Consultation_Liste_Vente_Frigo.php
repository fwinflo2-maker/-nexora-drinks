<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	if ($_GET['Stat']=='Mixte')
	{	
		if ($_GET['user']=='TOUS')
		{
			$sql='SELECT ID_SORTIESTOCK, DATESORTIESTOCK, LOGIN, OBSERVATION, STATUT FROM SORTIE_STOCK_FRIGO  WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY ID_SORTIESTOCK' ;
		}
		else
		{
			$sql='SELECT ID_SORTIESTOCK, DATESORTIESTOCK, LOGIN, OBSERVATION, STATUT FROM SORTIE_STOCK_FRIGO  WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND LOGIN="'.$_GET['user'].'" ORDER BY ID_SORTIESTOCK' ;
		
		}	
	}
	else 
		if ($_GET['Stat']=='N')
		{
			if ($_GET['user']=='TOUS')
			{
				$sql='SELECT ID_SORTIESTOCK, DATESORTIESTOCK, LOGIN, OBSERVATION, STATUT FROM SORTIE_STOCK_FRIGO  WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="N" ORDER BY ID_SORTIESTOCK' ;
			}
			else
			{
			$sql='SELECT ID_SORTIESTOCK, DATESORTIESTOCK, LOGIN, OBSERVATION, STATUT FROM SORTIE_STOCK_FRIGO  WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND LOGIN="'.$_GET['user'].'" AND STATUT="N" ORDER BY ID_SORTIESTOCK' ;
			}
		}
		else
		{
		if ($_GET['user']=='TOUS')
			{
				$sql='SELECT ID_SORTIESTOCK, DATESORTIESTOCK, LOGIN, OBSERVATION, STATUT FROM SORTIE_STOCK_FRIGO  WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="V" ORDER BY ID_SORTIESTOCK' ;
			}
			else
			{
			$sql='SELECT ID_SORTIESTOCK, DATESORTIESTOCK, LOGIN, OBSERVATION, STATUT FROM SORTIE_STOCK_FRIGO  WHERE DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND LOGIN="'.$_GET['user'].'" AND STATUT="V" ORDER BY ID_SORTIESTOCK' ;
			}
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de la Liste des Ventes Frigo</title>
</head>

<body>
<table id='liste' border="1" width="100%"  align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="10"><h3>Etat des ventes Frigo d'un utilisateur</h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
                <td colspan="3"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?> </h5></td>
                <td colspan="2"><h5>Statut : <?php echo $_GET['Stat']; ?></h5></td>
                <td colspan="3"><h4>Utilisateur : <?php echo $_GET['user']; ?></h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Date de vente </h5> </td>
                <td align="left" ><h5>Code </h5> </td>
                <td align="left" ><h5>Utilisateur </h5> </td>
                <td align="center"><h5>TT Prix Revient </h5> </td>
                <td align="center" ><h5>TT Prix Vente </h5> </td>
                <td align="center" ><h5>TT Benef </h5> </td>
                <td  align="center"><h5>Statut </h5></td>
				<td align="center" ><h5>Observation </h5></td>
			</tr>
            
<?php
$TTPV=0;
$TTPR=0;
$TTB=0;
$couleur = "darkgray";
$i = 0;
$nbre=0;
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
		{
	//Ici on recupere les articles de chacune des ventes puis on somme les prix et benef
	 $PRIXVENTE=0;
	 $PRIXREVIENT=0;
	 $BENEF=0;
	 $sql1='SELECT ID_SORTIESTOCK, PRIXVENTE, PRIXREVIENT FROM  ARTICLEVENDU_FRIGO WHERE ID_SORTIESTOCK= "'.$rslt['ID_SORTIESTOCK'].'" ' ;
	 $reponse1= $DataBase->query($sql1);
  while($rslt1= $reponse1->fetch())
 {
	 $PRIXVENTE= ($PRIXVENTE + $rslt1['PRIXVENTE']);
	 $PRIXREVIENT= ($PRIXREVIENT + $rslt1['PRIXREVIENT']);
	 $BENEF= ($PRIXVENTE-$PRIXREVIENT);
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
                        <td  align="left"> <?php echo $rslt['LOGIN']; ?> </td>
                        <td  align="center"> <?php echo $PRIXREVIENT.' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $PRIXVENTE.' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $BENEF.' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $rslt['STATUT']; ?> </td>
                        <td  align="left"> <?php echo $rslt['OBSERVATION']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$TTPV = ($TTPV + $PRIXVENTE);
				$TTPR = ($TTPR + $PRIXREVIENT);
				$TTB = ($TTB + $BENEF);
		 }
?>
<tr>
	<td><a href="Liste_Vente_Frigo.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Stat=<?php echo $_GET['Stat'];?>&user=<?php echo $_GET['user'];?>"/><input type="button" value="Imprimer" /></td></a>
	<td><h4>Nombre de vente :<?php echo $nbre; ?> </h4></td>
    <td align="center"><h4>Totaux : </h4></td>
    <td align="center"><h4><?php echo $TTPV.' FCFA'; ?> </h4></td>
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