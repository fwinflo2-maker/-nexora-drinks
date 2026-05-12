<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant" ))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	//ici on recupere les info sur l'emb
$sql2 = 'SELECT ID_EMBALLAGE, LIBELLE, QTE,QTESTOCK FROM EMBALLAGE WHERE ID_EMBALLAGE="'.$_GET['Emb'].'"';
$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			$stock=$rslt2['QTESTOCK'];
    		$libelle=$rslt2['LIBELLE'];
			$qte=$rslt2['QTE'];
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation des mouvements en stocks d'un emballage</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="10"><h3>Etat des Mouvements en stock</h3></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="10"><h4>Emballage : <?php echo $libelle ?></h4></td>
          </tr>
          <tr bgcolor="#CCCCCC">
                <td colspan="10" align="center" ><h5>Période : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?></h5></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="3"><h4>OPERATION</h4></td>
            <td colspan="3"><h4>STOCK TOTAL</h4></td>
            <td colspan="3"><h4>STOCK DISPONIBLE</h4></td>
            <td colspan=""><h4>UTILISATEUR</h4></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td align="center"><h5>Date </h5> </td>
                <td align="center"><h5>Heure </h5> </td>
                <td align="left"><h5>Operation</h5> </td>
                <td  align="center"><h5>Qte Mouv </h5></td>
                <td  align="center"><h5>Stock Initial </h5></td>
                <td  align="center"><h5>Stock Final </h5></td>
                <td  align="center"><h5>Qte Mouv </h5></td>
                <td  align="center"><h5>Stock Initial </h5></td>
                <td  align="center"><h5>Stock Final </h5></td>
                <td  align="center"><h5>Login </h5></td>
			</tr>
            
<?php
$couleur = "darkgray";
$i = 0;
$nbre=0;

//Ici on recupere les quantites de la meme article puis on somme
$sql1 = 'select * from mouvementemb where id_emballage="'.$_GET['Emb'].'" and date between "'.$Debut.'" AND "'.$Fin.'" order by id_mouv';
$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#CCCCCC';
			else
				$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="center"> <?php echo dateFormatFrancais($rslt1['DATE']); ?> </td>
                        <td  align="center"> <?php echo $rslt1['HEURE']; ?> </td>
                        <td  align="left"> <?php echo $rslt1['OPERATION'].' ('.$rslt1['ID_OPERATION'].')'; ?> </td>
                        <td  align="center"> <?php echo ($rslt1['SFQTE']-$rslt1['SIQTE']); ?> </td>
                        <td  align="center"> <?php echo $rslt1['SIQTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt1['SFQTE']; ?> </td>
                        <td  align="center"> <?php echo ($rslt1['SFSTOCK']-$rslt1['SISTOCK']); ?> </td>
                        <td  align="center"> <?php echo $rslt1['SISTOCK']; ?> </td>
                        <td  align="center"> <?php echo $rslt1['SFSTOCK']; ?> </td>
                        <td  align="center"> <?php echo $rslt1['USER']; ?> </td>
                     </tr>
                <?php
			$i++;
			$nbre++;
		}

		 
?>
<tr>
	<td align="center"><a href="Etat_Mouv_St_Emb.php?debut=<?php echo $_GET["DateD"];?>&fin=<?php echo $_GET["DateF"];?>&Emb=<?php echo $_GET['Emb'];?>"/><input type="button" value="Imprimer" /></td></a>
	<td colspan="10" align="center"><h4>Nombre de Mouvement : <?php echo $nbre; ?> </h4></td>
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