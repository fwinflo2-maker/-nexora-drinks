<?php
	include("Connexion.php");
	include('fonctions.php');
?>
        <table id='tvente' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h5>Choisir une vente pour impression du reçu</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center" ><h5>Date de la vente </h5></td>
                <td  align="center"><h5>Client</h5></td>
                <td  align="center" ><h5>Observation </h5></td>
                <td  align="center" ><h5>Double</h5></td>
                <td  align="center" ><h5>Format A4</h5></td>
                <td  align="center" ><h5>Matricielle</h5></td>
				<td  align="center" ><h5>Ticket</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT  ST.ID_SORTIESTOCK, ST.DATESORTIESTOCK, ST.ID_CLIENT, C.ID_CLIENT, C.NOM, ST.OBSERVATION, ST.STATUT FROM SORTIE_STOCK ST, CLIENT C WHERE  ST.ID_CLIENT =C.ID_CLIENT AND  ID_SORTIESTOCK LIKE "%'.$_POST['code'].'%" ORDER BY ST.DATESORTIESTOCK DESC ' ;
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
                        <td  align="center"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center"> <?php echo $rslt['OBSERVATION']; ?> </td>
                        <?php
						if ($rslt['STATUT']=="V")
						{
							?>
                        	<td  align="center"> <a href="RecuDble.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						else
						{
							?>
                        	<td  align="center"> <a href="Recu_ProformaDble.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						if ($rslt['STATUT']=="V")
						{
							?>
                        	<td  align="center"> <a href="Recu.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						else
						{
							?>
                        	<td  align="center"> <a href="Recu_Proforma.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						if ($rslt['STATUT']=="V")
						{
							?>
                        	<td  align="center"> <a href="RecuMat.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						else
						{
							?>
                        	<td  align="center"> <a href="Recu_ProformaMat.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						if ($rslt['STATUT']=="V")
						{
							?>
                        	<td  align="center"> <a href="RecuTck.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						else
						{
							?>
                        	<td  align="center"> <a href="Recu_ProformaTck.php?Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                            <?php
						}
						?>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>