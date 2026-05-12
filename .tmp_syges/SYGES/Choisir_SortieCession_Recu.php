<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="Gerant"|| $_SESSION['habilitation']=="Comptable" ))
{

	include("Connexion.php");
	include("fonctions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Choisir une Sortie Cession.</title>
</head>

<body>

          <table id='tappro' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h5>Liste des sorties cession pour impression de l'etat</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center" ><h5>Date </h5></td>
                <td  align="center"><h5>Utilisateur </h5></td>
                <td  align="center" ><h5>Observation </h5></td>
                <td  align="center" ><h5>Selectionner</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT  ID_SORTIESTOCK, DATESORTIESTOCK, LOGIN, OBSERVATION FROM SORTIE_STOCK_CESSION WHERE STATUT="V" ORDER BY DATESORTIESTOCK DESC LIMIT 0,20' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			//on recupere le nom du fssr
		$sql2='SELECT  NOM FROM USER WHERE LOGIN="'.$rslt['LOGIN'].'"' ;
		$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			$nom=$rslt2['NOM'];
		}
		//
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_SORTIESTOCK']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATESORTIESTOCK']); ?> </td>
                        <td  align="center"> <?php echo  $nom ; ?> </td>
                        <td  align="center"> <?php echo $rslt['OBSERVATION']; ?> </td>
                        <td  align="center"> <a href="Recu_SortieCession.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>
</body>
</html>
<?php 
}
else
{
?>
				<script language="javascript" type="text/javascript">
				alert('Vous n\'etes pas habiliter a acceder a cette page.');
				history.back();
				</script>
<?php
}
?>
