<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	//On recuere le nom et le code du fournisseur 
	$sql2='SELECT  ID_FOURNISSEUR, NOM FROM FOURNISSEUR  WHERE ID_FOURNISSEUR="'.$_GET['Fssr'].'" ' ;
    $reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			$codeF=$rslt2['ID_FOURNISSEUR'];
			$nomF=$rslt2['NOM'];
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de la Liste des Approvisionnements d'un Fournisseurs</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="4"><h3>Liste des Approvisionnements d'un Fournisseur </h3></td>
          </tr>
          <tr>
                <td align="center" width="25%"><h5> Code Fournisseur </h5> </td>
                <td colspan=""><h5><?php echo $codeF; ?> </h5></td>
                <td align="center" width="25%"><h5> Nom </h5> </td>
                <td colspan=""><h5><?php echo $nomF; ?> </h5></td>
          </tr>
          <tr align="center" >
          		<td><h5>Période d'appro </h5></td>
                <td><h5>Du : <?php echo dateFormatFrancais($Debut); ?> </h5></td>
          		<td><h5>Au : <?php echo dateFormatFrancais($Fin); ?> </h5></td>
          		<td></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" width="10%"><h5>Date d'appro </h5> </td>
                <td align="center" width="17%"><h5>Code </h5> </td>
                <td  align="center"><h5>Statut </h5></td>
				<td align="center" width="40%"><h5>Observation </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$sql='SELECT A.ID_APPRO, A.DATE_APPRO, A.ID_FOURNISSEUR, A.OBSERVATION, A.STATUT, F.ID_FOURNISSEUR, F.NOM FROM APPROVISIONNEMENT A, FOURNISSEUR F WHERE A.ID_FOURNISSEUR=F.ID_FOURNISSEUR AND F.ID_FOURNISSEUR="'.$_GET['Fssr'].'" AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" ORDER BY A.ID_APPRO' ;
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					
					$couleur = "white";
				else
					$couleur = '#CCCCCC';
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_APPRO']); ?> </td>
                        <td  align="center"> <?php echo $rslt['ID_APPRO']; ?> </td>
                        <td  align="center"> <?php echo $rslt['STATUT']; ?> </td>
                        <td  align="left"> <?php echo $rslt['OBSERVATION']; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
		 }
?>
<tr>
	<td><a href="Liste_Appro_Fournisseur.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Fssr=<?php echo $codeF;?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="8" align="right"><h4>Nombre d'approvisionnement :<?php echo $nbre; ?> </h4></td>
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