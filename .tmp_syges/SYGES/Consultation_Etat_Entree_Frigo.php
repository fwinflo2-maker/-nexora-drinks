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
<title>Consultation de l'etat des entrées en stocks frigo</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="6"><h3>Etat des entrées en stocks frigo </h3></td>
          </tr>
          <tr bgcolor="#CCCCCC">
                <td align="center" colspan="2"><h5>Période </h5></td>
                <td colspan="4"><h5>Du : <?php echo dateFormatFrancais($Debut).'  Au : '.dateFormatFrancais($Fin); ?> </h5></td>
          </tr>
          <tr bgcolor="#CCCCCC">
                <td align="center" colspan="4"><h5>Sortie du Magasin/Boutique pour le Frigo</h5></td>
          		<td colspan="2" align="center"><h5> Entrée en Stock Frigo (Bouteilles)</h5></td>
          </tr>
          <tr bgcolor="#CCCCCC">
                <td align="center" width="10%"><h5>Code </h5> </td>
                <td align="center" width="25%"><h5>Conditionnement</h5> </td>
                <td align="center" width="25%"><h5>Libellé</h5> </td>
                <td align="center" width="25%"><h5>Qte</h5> </td>
                <td align="center" width="25%"><h5>Libellé</h5> </td>
                <td  align="center"><h5>Quantité </h5></td>
		  </tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;

//Ici on recupre la liste sans doublons des articles livres dans la periode 
 $sql='SELECT DISTINCT  AR.ID_ARTICLE FROM ARTICLE_RECU_FRIGO AR, APPROFRIGO A  WHERE AR.ID_APPRO=A.ID_APPRO AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND A.STATUT="V" ORDER BY AR.ID_ARTICLE ';
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
		{
//Ici on recupere les quantites de la meme article puis on somme
$nbrebte=0;
$qte=0;
$sql1 = 'SELECT  AR.ID_ARTICLE, AR.QTERECU, AR.NBREBTE, AR.ID_APPRO, A.ID_APPRO, A.DATE_APPRO FROM ARTICLE_RECU_FRIGO AR, APPROFRIGO A WHERE AR.ID_APPRO=A.ID_APPRO AND A.STATUT="V" AND A.DATE_APPRO BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AR.ID_ARTICLE="'.$rslt['ID_ARTICLE'].'"';
$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$nbrebte=$nbrebte+$rslt1['NBREBTE'];
			$qte=$qte+$rslt1['QTERECU'];
		}
//ici on recupere le libelle et la marque de l'article
$sql2 = 'SELECT ID_ARTICLE, LIBELLE, NBREBTE, MARQUE FROM ARTICLE WHERE ID_ARTICLE="'.$rslt['ID_ARTICLE'].'"';
$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
    		$libelle=$rslt2['LIBELLE'];
			$marque=$rslt2['MARQUE'];
			$nbreb=$rslt2['NBREBTE'];
		}
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo $rslt['ID_ARTICLE']; ?> </td>
                        <td  align="center"> <?php echo $marque.' '.$nbreb; ?> </td>
                        <td  align="center"> <?php echo $libelle; ?> </td>
                        <td  align="center"> <?php echo $qte; ?> </td>
                        <td  align="center"> <?php echo $libelle; ?> </td>
                        <td  align="center"> <?php echo $nbrebte; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
		 }
?>
<tr>
	<td><a href="Etat_Entree_Frigo.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="5" align="right"><h4>Nombre d'article:<?php echo $nbre; ?> </h4></td>
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