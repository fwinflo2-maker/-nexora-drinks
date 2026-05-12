<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include('Connexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de l'etat des Fournisseurs</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h3>Liste des Fournisseurs  </h3></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" width="10%"><h5>Code </h5> </td>
                <td align="left" width="30%"><h5>Nom et prenom</h5> </td>
                <td align="center" width="20%"><h5>N° Tel </h5> </td>
                <td  align="center" width="20%"><h5>E-mail </h5></td>
				<td align="center" width="20%"><h5>Statut </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$sql = "SELECT * FROM FOURNISSEUR";
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
                		<td  align="center"> <?php echo $rslt['ID_FOURNISSEUR']; ?> </td>
                        <td  align="left"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center"> <?php echo $rslt['NUMTEL']; ?> </td>
                        <td  align="center"> <?php echo $rslt['EMAIL']; ?> </td>
                        <td  align="center"> <?php echo $rslt['STATUT']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
		 }
?>
<tr>
	<td><a href="Liste_Fournisseur.php"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="5" align="right"><h4>Nombre de fournisseur:<?php echo $nbre; ?> </h4></td>
</tr>
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