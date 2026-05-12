<?php
	include("Connexion.php");
	include('fonctions.php');
?>
<table id='tcharge' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="6"><h5>Liste des charges pour suppression</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td  align="center" ><h5>Code </h5></td>
                <td  align="center"><h5>Libelle </h5></td>
                <td align="center"><h5>Description </h5></td>
                <td align="center"><h5>Montant </h5></td>
                <td align="center"><h5>Date d'enreg </h5></td>
                <td align="center"><h5>Supprimer</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT C.ID_CHARGE, TC.LIBELLE, TC.ID_TYPECHARGE, C.ID_TYPECHARGE, C.DESCRIPTION, C.MONTANT, C.DATE_CHARGE FROM CHARGE C, TYPE_CHARGE TC WHERE C.ID_TYPECHARGE=TC.ID_TYPECHARGE  AND C.STATUT="N" AND C.ID_CHARGE LIKE "%'.$_POST['code'].'%" ORDER BY C.DATE_CHARGE DESC' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_CHARGE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="left"> <?php echo $rslt['DESCRIPTION']; ?> </td>
                        <td  align="center"> <?php echo $rslt['MONTANT']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_CHARGE']); ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Suppression_Charge&Id=<?php echo $rslt['ID_CHARGE'];?> "/> <img src="IMG/Supp.png"/> </a></td>
                <?php
				$i++;
		 }
				?>
</table>