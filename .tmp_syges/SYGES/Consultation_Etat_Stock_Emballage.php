<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" || $_SESSION['habilitation']=="Caissier"  || $_SESSION['habilitation']=="Comptable"))
{
	include('Connexion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de l'etat des stocks des emballages</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="6"><h3>Etat des stocks des emballages </h3></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center"><h5>Code </h5> </td>
                <td align="center"><h5>Libellé</h5> </td>
                <td align="center"><h5> Frais Consigne</h5> </td>
                <td  align="center"><h5>Qte Total</h5></td>
                <td  align="center"><h5>Qte en Stock </h5></td>
                <td  align="center"><h5>Qte Consignée </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$Qtet=0;
$Qtetst=0;
$Qtecg=0;
$sql = "SELECT * FROM EMBALLAGE ORDER BY LIBELLE";
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
                		<td  align="center"> <?php echo $rslt['ID_EMBALLAGE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['MT_CONSIGNE'].' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $rslt['QTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['QTESTOCK']; ?> </td>
                        <td  align="center"> <?php echo ($rslt['QTE']-$rslt['QTESTOCK']); ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$Qtet=$Qtet+$rslt['QTE'];
				$Qtetst=$Qtetst+$rslt['QTESTOCK'];
				$Qtecg=$Qtecg+($rslt['QTE']-$rslt['QTESTOCK']);
		 }
?>
<tr>
	<td><a href="Etat_Emballage.php"/><input type="button" value="Imprimer" /></td></a>
	<td align="center" colspan=""><h4>Totaux :</h4></td>
    <td  align="center"><h4>Emballages :<?php echo $nbre; ?> </h4></td>
    <td  align="center"><h4><?php echo $Qtet; ?> </h4></td>
    <td  align="center"><h4><?php echo $Qtetst; ?> </h4></td>
    <td  align="center"><h4><?php echo $Qtecg; ?> </h4></td>
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