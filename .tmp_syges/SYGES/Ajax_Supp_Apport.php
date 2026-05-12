<?php
	include("Connexion.php");
	include('fonctions.php');
?>
 <table id='tapport' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h5>Liste des Mouvements de fonds pour suppression</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td  align="center" ><h5>Code </h5></td>
                <td  align="center"><h5>Libelle </h5></td>
                <td align="center"><h5>Montant </h5></td>
                <td align="center"><h5>Date d'enreg </h5></td>
                <td align="center"><h5>Selectionner</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT * FROM APPORT WHERE STATUT="N" AND ID_APPORT LIKE "%'.$_POST["code"].'%" ORDER BY DATE_APPORT DESC' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_APPORT']; ?> </td>
                        <td  align="center"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['MONTANT'], 0, ',', ' ').' FCFA'; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_APPORT']); ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Suppression_Apport&Id=<?php echo $rslt['ID_APPORT'];?> "/> <img src="IMG/Select.png"/> </a></td>
                <?php
				$i++;
		 }
				?>
</table>
