<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
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
<title>Consultation de l'etat des sorties de stock des emballages</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="3"><h3>Etat des sorties de stock des emballages </h3></td>
          </tr>
          <tr bgcolor="#CCCCCC">
                <td colspan="3" align="center" ><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>

<!--///////////////////////////////Deconsignation  Approvisionnements-->
		  <tr align="center">
          	<td colspan="3"><h4>Deconsignation Approvisionnements </h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Code </h5> </td>
                <td align="center" ><h5>Libellé</h5> </td>
                <td  align="center"><h5>Quantité </h5></td>
			</tr>
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$colis=0;
$qte=0;
//Ici on recupre la liste sans doublons des emballages RTR AU FSSR dans la periode 
 $sql='SELECT DISTINCT  ID_EMBALLAGE FROM RTREMBFSSR  WHERE DATE_RTREMB BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="OK" ';
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			//Ici on recupere les quantites du meme emballage puis on somme
			$qterecu=0;
			$sql1 = 'SELECT  QTE FROM RTREMBFSSR WHERE DATE_RTREMB BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ID_EMBALLAGE="'.$rslt['ID_EMBALLAGE'].'" AND STATUT="OK"';
			$reponse1= $DataBase->query($sql1);
			while($rslt1= $reponse1->fetch())
			{
				$qte=$qte+$rslt1['QTE'];
			}
			//ici on recupere le libelle de l'emballage
			$sql2 = 'SELECT  LIBELLE FROM EMBALLAGE WHERE ID_EMBALLAGE="'.$rslt['ID_EMBALLAGE'].'"';
			$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
    			$libelle=$rslt2['LIBELLE'];
			}
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo $rslt['ID_EMBALLAGE']; ?> </td>
                        <td  align="center"> <?php echo $libelle; ?> </td>
                        <td  align="center"> <?php echo $qte; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$colis=$colis+$qte;
		 }
?>
<tr>
	<td></td>
	<td colspan="" align="center"><h4>Nombre d'emballage(s) : <?php echo $nbre; ?> </h4></td>
	<td align="center"><h4>Total Colis : <?php echo $colis; ?> </h4></td>
</tr>
<!--///////////////////////////////Consignes Ventes-->
		  <tr align="center">
          	<td colspan="3"><h4>Consignes Ventes</h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Code </h5> </td>
                <td align="center" ><h5>Libellé</h5> </td>
                <td  align="center"><h5>Quantité </h5></td>
			</tr>
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$colis=0;
$qte=0;
//Ici on recupre la liste sans doublons des emballages consignees dans la periode 
 $sql='SELECT DISTINCT  ID_EMBALLAGE FROM CONSIGNE WHERE DATE_CONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="Consigne"';
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			//Ici on recupere les quantites du meme emballage puis on somme
			$qte=0;
			$sql1 = 'SELECT  ID_EMBALLAGE, QTE FROM CONSIGNE C WHERE DATE_CONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ID_EMBALLAGE="'.$rslt['ID_EMBALLAGE'].'" AND STATUT="Consigne"';
			$reponse1= $DataBase->query($sql1);
			while($rslt1= $reponse1->fetch())
			{
				$qte=$qte+$rslt1['QTE'];
			}
			//ici on recupere le libelle de l'emballage
			$sql2 = 'SELECT  LIBELLE FROM EMBALLAGE WHERE ID_EMBALLAGE="'.$rslt['ID_EMBALLAGE'].'"';
			$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
    			$libelle=$rslt2['LIBELLE'];
			}
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo $rslt['ID_EMBALLAGE']; ?> </td>
                        <td  align="center"> <?php echo $libelle; ?> </td>
                        <td  align="center"> <?php echo $qte; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$colis=$colis+$qte;
		 }
?>
<tr>
	<td align="center"><a href="Etat_Sortie_Emb.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="" align="center"><h4>Nombre d'emballage(s) : <?php echo $nbre; ?> </h4></td>
	<td align="center"><h4>Total Colis : <?php echo $colis; ?> </h4></td>
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