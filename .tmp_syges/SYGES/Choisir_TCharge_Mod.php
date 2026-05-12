<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur"  || $_SESSION['habilitation']=="Gerant")||($_SESSION['habilitation']=="Caissier")||($_SESSION['habilitation']=="Comptable"))
{

	include("Connexion.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Choisir un type charge.</title>
</head>

<body>

          <table id='liste' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h5>Liste des types de charge pour modification</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td  align="center" ><h5>Code </h5></td>
                <td  align="center"><h5>Libelle </h5></td>
                <td align="center"><h5>Statut </h5></td>
                <td align="center"><h5>Modifier </h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT * FROM TYPE_CHARGE  ORDER BY ID_TYPECHARGE' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_TYPECHARGE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['STATUT']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modification_TCharge&Code=<?php echo $rslt['ID_TYPECHARGE'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                <?php
				$i++;
		 }
				?>
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
