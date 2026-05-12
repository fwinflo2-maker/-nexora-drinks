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
<title>Consultation de l'etat des sorties de stock cession par Ar</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="4"><h3>Etat des sorties de stock cession par article </h3></td>
          </tr>
          <tr bgcolor="#CCCCCC">
                <td colspan="4" align="center" ><h5>Période : Du : <?php echo dateFormatFrancais($Debut); ?>  Au : <?php echo dateFormatFrancais($Fin); ?></h5>
                </td>

          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" width="10%"><h5>Code </h5> </td>
                <td align="center" width="25%"><h5>Conditionnement </h5> </td>
                <td align="center" width="25%"><h5>Libellé</h5> </td>
                <td  align="center"><h5>Qté Sortie </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$TQTE=0;
//Ici on recupre la liste sans doublons des articles sorties dans la periode 
 $sql='SELECT DISTINCT  AR.ID_ARTICLE FROM ARTICLESORTIE_CESSION AR, SORTIE_STOCK_CESSION ST  WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND ST.STATUT="V" ORDER BY AR.ID_ARTICLE ';
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
//Ici on recupere les quantites de la meme article puis on somme
$qte=0;
$sql1 = 'SELECT  AR.ID_ARTICLE, AR.QTESORTIE, AR.ID_SORTIESTOCK, ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK FROM ARTICLESORTIE_CESSION AR, SORTIE_STOCK_CESSION ST WHERE AR.ID_SORTIESTOCK=ST.ID_SORTIESTOCK AND ST.DATESORTIESTOCK BETWEEN "'.$Debut.'" AND "'.$Fin.'" AND AR.ID_ARTICLE="'.$rslt['ID_ARTICLE'].'" AND ST.STATUT="V"';
$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$qte=$qte+$rslt1['QTESORTIE'];
		}
//ici on recupere le libelle et la marque de l'article
$sql2 = 'SELECT ID_ARTICLE, LIBELLE, MARQUE, NBREBTE, QTESTOCK FROM ARTICLE WHERE ID_ARTICLE="'.$rslt['ID_ARTICLE'].'"';
$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			$marque=$rslt2['MARQUE'].' '.$rslt2['NBREBTE'];
    		$libelle=$rslt2['LIBELLE'];
		}
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo $rslt['ID_ARTICLE']; ?> </td>
                        <td  align="center"> <?php echo $marque; ?> </td>
                        <td  align="center"> <?php echo $libelle; ?> </td>
                        <td  align="center"> <?php echo $qte; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
				$TQTE=$TQTE+$qte;
		 }
?>
<tr>
	<td><a href="Etat_Sortie_Cession_Ar.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="2" align="center"><h4>Nombre d'article : <?php echo $nbre; ?> </h4></td>
    <td colspan="2" align="center"><h4>Nombre Colis : <?php echo $TQTE; ?> </h4></td>
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