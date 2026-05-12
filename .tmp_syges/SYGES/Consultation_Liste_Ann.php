<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation des Annulations</title>
</head>

<body>
<table id='liste' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="7"><h3>Etat des Annulations</h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="7"><h3>Operations  : <?php echo $_GET['Ope'] ?></h3></td>
          </tr>
          <tr bgcolor="#CCCCCC">
                <td colspan="7" align="center" ><h5>Période : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center"><h5>Date </h5> </td>
                <td align="center"><h5>Heure </h5> </td>
                <td align="left"><h5>Reference</h5> </td>
                <td  align="left"><h5>Detenteur </h5></td>
                <td  align="center"><h5>Operateur </h5></td>
                <td  align="center"><h5>Consulter </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;

			if($_GET['Ope']=='Toutes')
			{
				$sql1 = 'select distinct id_operation, date, heure, user, date_ann, detenteur from mouvementar where operation like "AN_%" and date_ann between "'.$Debut.'" AND "'.$Fin.'" order by id_mouv';
			}
			if($_GET['Ope']=='Ventes')
			{
				$sql1 = 'select distinct id_operation, date, heure, user, date_ann, detenteur from mouvementar where operation="AN_VAL_VENTE" and date_ann between "'.$Debut.'" AND "'.$Fin.'" order by id_mouv';
			}
			if	($_GET['Ope']=='Sorties Cessions')
			{
				$sql1 = 'select distinct id_operation, date, heure, user, date_ann, detenteur from mouvementar where operation="AN_VAL_SORTIE_CESSION" and date_ann between "'.$Debut.'" AND "'.$Fin.'" order by id_mouv';
			}
			if	($_GET['Ope']=='Entrées Cessions')
			{
				$sql1 = 'select distinct id_operation, date, heure, user, date_ann, detenteur from mouvementar where operation="AN_VAL_APPRO_CESSION" and date_ann between "'.$Debut.'" AND "'.$Fin.'" order by id_mouv';
			}
			if	($_GET['Ope']=='Approvisionnements')
			{
				$sql1 = 'select distinct id_operation, date, heure, user, date_ann, detenteur from mouvementar where operation="AN_VAL_APPRO" and date_ann between "'.$Debut.'" AND "'.$Fin.'" order by id_mouv';
			}

$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$heure=substr($rslt1['heure'],0,5);
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
			else
				$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt1['date_ann']); ?> </td>
                        <td  align="center"> <?php echo $rslt1['heure']; ?> </td>
                        <td  align="left"> <?php echo $rslt1['id_operation']; ?> </td>
                        <td  align="left"> <?php echo $rslt1['detenteur']; ?> </td>
                        <td  align="center"> <?php echo $rslt1['user']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Annulation&Vte=<?php echo $rslt1['id_operation'];?>&Hre=<?php echo $heure;?>"/> <img src="IMG/Select.png"/> </a></td>
                     </tr>
                <?php
			$i++;
			$nbre++;
		}

		 
?>
<tr>
	<td align="center"><a href="Liste_Ann.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Ope=<?php echo $_GET['Ope'];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="7" align="center"><h4>Nombre d'opération(s) :<?php echo $nbre; ?> </h4></td>
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