<?php
if (isset ($_SESSION['habilitation']) && (($_SESSION['habilitation']=="Administrateur")||($_SESSION['habilitation']=="Gerant")  || $_SESSION['habilitation']=="Comptable"))
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
<title>Liste des sauvegardes</title>
</head>

<body>
<table id='liste'  width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="4"><h3>Liste des sauvegardes du stocck des articles  </h3></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	<td align="center" ><h5>N° </h5> </td>
                <td align="center" ><h5>Date </h5> </td>
                <td align="center" ><h5>Heure </h5> </td>
                <td  align="center" ><h5>Selectionner</h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$sql='SELECT ID_SAUV, DATE_SAUV, HEURE_SAUV FROM SAUV_STOCK WHERE DATE_SAUV BETWEEN "'.$Debut.'" AND "'.$Fin.'"';
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
{
 
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo $rslt['ID_SAUV']; ?> </td>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_SAUV']); ?> </td>
                        <td  align="center"> <?php echo $rslt['HEURE_SAUV']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Liste_Ar_Sauv&Id=<?php echo $rslt['ID_SAUV'];?>"/> <img src="IMG/Select.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
?>
<tr>
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