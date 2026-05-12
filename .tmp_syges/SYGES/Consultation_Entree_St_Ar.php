<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"  || $_SESSION['habilitation']=="Comptable"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	//ici on recupere le libelle et la marque de l'article
	if ($_GET['Art']=="Tous")
	{
		$libelle="Tous";
		$marque="Tous les Articles";
		$nbrebte="";
	}else {
			$sql2 = 'SELECT ID_ARTICLE, LIBELLE, NBREBTE,QTESTOCK, MARQUE FROM ARTICLE WHERE ID_ARTICLE="'.$_GET['Art'].'"';
		$reponse2= $DataBase->query($sql2);
				while($rslt2= $reponse2->fetch())
				{
					$marque=$rslt2['MARQUE'];
					$libelle=$rslt2['LIBELLE'];
					$nbrebte=$rslt2['NBREBTE'];
				}
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation de l'etat des entrées en stocks d'un article</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h3>Etat des entrées en stocks</h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h3>Article : <?php echo $libelle ?></h3></td>
          </tr>
          <tr bgcolor="#CCCCCC">
                <td colspan="8" align="center" ><h5>Période : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center"><h5>Date </h5> </td>
                <td align="center"><h5>Heure </h5> </td>
                <td align="left"><h5>Operation</h5> </td>
				<td align="center"><h5>Article </h5> </td>
                <td  align="center"><h5>Qte Mouv  </h5></td>
                <td  align="center"><h5>Stock Initial </h5></td>
                <td  align="center"><h5>Stock Final </h5></td>
                <td  align="center"><h5>Utilisateur </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;

//Ici on recupere les quantites de la meme article puis on somme
$qterecu=0;

if ($libelle=="Tous")
{
	$sql1 = 'select * from mouvementar m, article a where m.id_article=a.id_article and m.id_article=a.id_article and  m.date between "'.$Debut.'" AND "'.$Fin.'" and m.sf > m.si order by m.id_mouv';
}else {
	$sql1 = 'select * from mouvementar m, article a where m.id_article=a.id_article and m.id_article="'.$_GET['Art'].'" and m.id_article=a.id_article and  m.date between "'.$Debut.'" AND "'.$Fin.'" and m.sf > m.si order by m.id_mouv';
}
$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$qterecu=$qterecu+$rslt1['QTE'];
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
			else
				$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt1['DATE']); ?> </td>
                        <td  align="center"> <?php echo $rslt1['HEURE']; ?> </td>
                        <td  align="left"> <?php echo $rslt1['OPERATION'].' ('.$rslt1['ID_OPERATION'].')'; ?> </td>
						<td  align="center"> <?php echo $rslt1['LIBELLE'] ; ?> </td>
                        <td  align="center"> <?php echo $rslt1['QTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt1['SI']; ?> </td>
                        <td  align="center"> <?php echo $rslt1['SF']; ?> </td>
                        <td  align="center"> <?php echo $rslt1['USER']; ?> </td>
                     </tr>
                <?php
			$i++;
			$nbre++;
		}

		 
?>
<tr>
	<td><a href="Etat_Entree_St_Ar.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Art=<?php echo $_GET['Art'];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="3" align="center"><h4>Nombre de Mouvement :<?php echo $nbre; ?> </h4></td>
    <td colspan="4" align="center"><h4>Quantité Total :<?php echo $qterecu; ?> </h4></td>
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