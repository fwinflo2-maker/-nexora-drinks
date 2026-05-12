<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
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
<title>Consultation de l'etat des entrées en stocks des emballages</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="3"><h3>Etat des entrées en stocks des emballages </h3></td>
          </tr>
          <tr bgcolor="#CCCCCC">
                <td colspan="3" align="center" ><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>
<!--///////////////////////////////APPRO EMBALLAGE-->
		  <tr align="center">
          	<td colspan="3"><h4>Approvisionnements Emballages</h4></td>
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
//Ici on recupre la liste sans doublons des emballages livres dans la periode 
 $sql='SELECT DISTINCT  ER.ID_EMBALLAGE FROM EMBALLAGE_RECU ER, APPROEMB AE  WHERE ER.ID_APPRO=AE.ID_APPRO AND AE.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AE.STATUT="V" ORDER BY ER.ID_EMBALLAGE  ';
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
//Ici on recupere les quantites du meme emballage puis on somme
$qterecu=0;
$sql1 = 'SELECT  ER.ID_EMBALLAGE, ER.QTERECU, ER.ID_APPRO, AE.ID_APPRO, AE.DATE_APPRO FROM EMBALLAGE_RECU ER, APPROEMB AE WHERE ER.ID_APPRO=AE.ID_APPRO AND AE.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ER.ID_EMBALLAGE="'.$rslt['ID_EMBALLAGE'].'" AND AE.STATUT="V"';
$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$qterecu=$qterecu+$rslt1['QTERECU'];
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
                        <td  align="center"> <?php echo $qterecu; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$colis=$colis+$qterecu;
		 }
?>
<tr>
	<td></td>
	<td colspan="" align="center"><h4>Nombre d'emballage(s) : <?php echo $nbre; ?> </h4></td>
	<td align="center"><h4>Total Colis : <?php echo $colis; ?> </h4></td>
</tr>
<!--///////////////////////////////Consignes Approvisionnements-->
		  <tr align="center">
          	<td colspan="3"><h4>Consignes Approvisionnements </h4></td>
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
//Ici on recupre la liste sans doublons des emballages consignees dans la periode 
 $sql='SELECT DISTINCT  CA.ID_EMBALLAGE FROM CONSIGNEAPP CA, APPROVISIONNEMENT A  WHERE CA.ID_APPRO=A.ID_APPRO AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND A.STATUT="V" ORDER BY CA.ID_EMBALLAGE  ';
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			//Ici on recupere les quantites du meme emballage puis on somme
			$qte=0;
			$sql1 = 'SELECT  CA.ID_EMBALLAGE, CA.QTE, CA.ID_APPRO, A.ID_APPRO, A.DATE_APPRO FROM CONSIGNEAPP CA, APPROVISIONNEMENT A WHERE CA.ID_APPRO=A.ID_APPRO AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND CA.ID_EMBALLAGE="'.$rslt['ID_EMBALLAGE'].'" AND A.STATUT="V"';
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

<!--///////////////////////////////Déconsignation Clients-->
		  <tr align="center">
          	<td colspan="3"><h4>Déconsignation Clients </h4></td>
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
//Ici on recupre la liste sans doublons des emballages deconsignees dans la periode 
 $sql='SELECT DISTINCT  ID_EMBALLAGE FROM CONSIGNE WHERE DATE_DECONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND STATUT="Deconsigne"';
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			//Ici on recupere les quantites du meme emballage puis on somme
			$qterecu=0;
			$sql1 = 'SELECT  ID_EMBALLAGE, QTE FROM CONSIGNE C WHERE DATE_DECONSIGNE BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ID_EMBALLAGE="'.$rslt['ID_EMBALLAGE'].'" AND STATUT="Deconsigne"';
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
	<td align="center"><a href="Etat_Entree_Emb.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>"/><input type="button" value="Imprimer" /></td></a>
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