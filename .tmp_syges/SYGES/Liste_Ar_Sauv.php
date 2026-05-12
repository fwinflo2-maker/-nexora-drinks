<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
{
	include('Connexion.php');
	include('fonctions.php');
	
	$sql1 = "SELECT * FROM SAUV_STOCK WHERE ID_SAUV='".$_GET['Id']."' ";
	$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$date=dateFormatFrancais($rslt1['DATE_SAUV']);
			$heure=$rslt1['HEURE_SAUV'];
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Etat du Stock d'une sauvegarde</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="9"><h3>Etat des stocks Au :  <?php echo $date; ?>  A :  <?php echo $heure ;?></h3></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Code </h5> </td>
                <td align="center" ><h5>Conditionnement</h5> </td>
                <td align="center" ><h5>Libellé </h5> </td>
                <td  align="center"><h5> Sotck Magasin </h5></td>
                <td  align="center"><h5>Stock Frigo(Bouteille) </h5></td>
				<td align="center" ><h5>Statut </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$sql = "SELECT * FROM ARTICLE_SAUV WHERE ID_SAUV='".$_GET['Id']."' ";
$reponse= $DataBase->query($sql);
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
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
                        <td  align="center"> <?php echo $rslt['STOCKFRIGO']; ?> </td>
                        <td  align="center"> <?php echo $rslt['STATUT']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
		 }
?>
<tr>
	<td><a href="Etat_Ar_Sauv.php?Id=<?php echo $_GET['Id']; ?>" /><input type="button" value="Imprimer" /></td></a>
	<td colspan="8" align="right"><h4>Nombre d'article:<?php echo $nbre; ?> </h4></td>
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