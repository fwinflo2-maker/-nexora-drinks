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
<title>Consultation de la liste des régularisations du stock des articles</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h3>Etat des régularisations du stock des articles  </h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          		<td colspan="8"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center" ><h5>Date </h5> </td>
                <td align="center" ><h5>Heure</h5> </td>
                <td align="center" ><h5>Article</h5> </td>
                <td align="center"><h5>Stock Magasin Avant</h5> </td>
                <td align="center" ><h5>Nouveau Stock Magasin  </h5> </td>
                <td align="center" ><h5>Stock Frigo Avant </h5> </td>
                <td  align="center"><h5>Nouveau Stock Frigo </h5></td>
				<td align="center" ><h5>Utilisateur </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;
$sql='select * from regularisation where date_regularisation between "'.$Debut.'" AND "'.$Fin.'" order by id_regularisation' ;
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			//Ici on recupere le libelle de l'article

	 	$sql1='SELECT LIBELLE FROM ARTICLE WHERE ID_ARTICLE= "'.$rslt['ID_ARTICLE'].'" ' ;
	 	$reponse1= $DataBase->query($sql1);
  		while($rslt1= $reponse1->fetch())
		 {
 			$libelle=$rslt1['LIBELLE'];
 		 } 
			//Ici on recupere le NOM du user

	 	$sql2='SELECT NOM FROM USER WHERE LOGIN= "'.$rslt['LOGIN'].'" ' ;
	 	$reponse2= $DataBase->query($sql2);
  		while($rslt2= $reponse2->fetch())
		 {
 			$nom=$rslt2['NOM'];
 		 } 
	       //
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_REGULARISATION']); ?> </td>
                        <td  align="center"> <?php echo $rslt['HEURE_REGULARISATION']; ?> </td>
                        <td  align="left"> <?php echo $rslt['ID_ARTICLE'].'/'.$libelle; ?> </td>
                        <td  align="center"> <?php echo $rslt['STOCKMAGAV']; ?> </td>
                        <td  align="center"> <?php echo $rslt['STOCKMAGAP']; ?> </td>
                        <td  align="center"> <?php echo $rslt['STOCKFRAV']; ?> </td>
                        <td  align="center"> <?php echo $rslt['STOCKFRAP']; ?> </td>
                        <td  align="left"> <?php echo $nom.'('.$rslt['LOGIN'].')'; ?> </td>
                     </tr>
                <?php
				$i++;
				$nbre++;
		 }
?>
<tr>
	<td><a href="Etat_Reg_Ar.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Statut=<?php echo $_GET['Stat'];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="8" align="center"><h4>Nombre de regularisations :  <?php echo $nbre; ?> </h4></td>
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