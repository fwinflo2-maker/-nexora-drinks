<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include('Connexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de la liste des utilisateurs</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="4"><h3>Liste des Utiliateurs  </h3></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" width="10%"><h5>Login </h5> </td>
                <td align="center" width="25%"><h5>Nom et prenom</h5> </td>
                <td align="center" width="25%"><h5>Habilitation </h5> </td>
				<td align="center" width="10%"><h5>Statut </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$sql = "SELECT * FROM USER";
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
                		<td  align="center"> <?php echo $rslt['LOGIN']; ?> </td>
                        <td  align="center"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center"> <?php echo $rslt['HABILITATION']; ?> </td>
                        <td  align="center"> <?php echo $rslt['STATUT']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
		 }
?>
<tr>
	<td><a href="Liste_Utilisateur.php"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="8" align="right"><h4>Nombre d'utilisateur:<?php echo $nbre; ?> </h4></td>
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