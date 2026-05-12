<?php
	include("Connexion.php");
	include('fonctions.php');
?>
          <table id='tvente' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h5>Liste des pertes pour Modification</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center" ><h5>Date de la perte </h5></td>
                <td  align="center" ><h5>Observation </h5></td>
                <td  align="center" ><h5>Modifier</h5></td>
                <td  align="center" ><h5>Valider</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			$cod = 'PER';
	$sql='SELECT  * FROM SORTIE_STOCK_FRIGO WHERE STATUT="N" AND ID_SORTIESTOCK LIKE "%'.$_POST['codevte'].'%" AND  ID_SORTIESTOCK LIKE "%'.$cod.'%" ORDER BY DATESORTIESTOCK DESC ' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_SORTIESTOCK']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATESORTIESTOCK']); ?> </td>
                        <td  align="center"> <?php echo $rslt['OBSERVATION']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modification_Perte&Vte=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/b_edit.png"/> </a></td>
                        <td  align="center"> <a href="index.php?formulaire=Validation_Perte&Vte=<?php echo $rslt['ID_SORTIESTOCK'];?> "/> <img src="IMG/Liste.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>
	