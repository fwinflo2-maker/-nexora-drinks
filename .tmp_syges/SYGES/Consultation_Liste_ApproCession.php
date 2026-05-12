<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	if ($_GET['Stat']=='Mixte')
	{	
	$sql='SELECT A.ID_APPRO, A.DATE_APPRO, A.LOGIN, A.OBSERVATION, A.STATUT, U.LOGIN, U.NOM FROM APPROCESSION A, USER U WHERE A.LOGIN=U.LOGIN AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY A.ID_APPRO' ;
	}
	else
		if ($_GET['Stat']=='N')
		{
		$sql='SELECT A.ID_APPRO, A.DATE_APPRO, A.LOGIN, A.OBSERVATION, A.STATUT, U.LOGIN, U.NOM FROM APPROCESSION A, USER U WHERE A.LOGIN=U.LOGIN AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'"  AND A.STATUT ="N" ORDER BY A.ID_APPRO' ;
		}
		else
			{
			$sql='SELECT A.ID_APPRO, A.DATE_APPRO, A.LOGIN, A.OBSERVATION, A.STATUT, U.LOGIN, U.NOM FROM APPROCESSION A, USER U WHERE A.LOGIN=U.LOGIN AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'"  AND A.STATUT ="V" ORDER BY A.ID_APPRO' ;
			}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de la Liste des Appro. Cession</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h3>Liste des Appro. Cession </h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          		<td colspan=""><h5>Période </h5></td>
                <td colspan=""><h5>Du : <?php echo dateFormatFrancais($Debut); ?> </h5></td>
          		<td colspan=""><h5>Au : <?php echo dateFormatFrancais($Fin); ?> </h5></td>
                <td colspan=""><h5>Statut : </h5></td>
                <td colspan="4" align="left"><h5><?php echo $_GET['Stat']; ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" width="10%"><h5>Date </h5> </td>
                <td align="center" width="17%"><h5>Code </h5> </td>
                <td align="center" width="25%"><h5>Utilisateur </h5> </td>
                <td  align="center"><h5>Statut </h5></td>
				<td align="center" width="40%"><h5>Observation </h5></td>
			</tr>
            
<?php
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
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_APPRO']); ?> </td>
                        <td  align="center"> <?php echo $rslt['ID_APPRO']; ?> </td>
                        <td  align="center"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center"> <?php echo $rslt['STATUT']; ?> </td>
                        <td  align="left"> <?php echo $rslt['OBSERVATION']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
		 }
?>
<tr>
	<td><a href="Liste_ApproCession.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Statut=<?php echo $_GET['Stat'];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="8" align="right"><h4>Nombre d'appro Cession :<?php echo $nbre; ?> </h4></td>
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