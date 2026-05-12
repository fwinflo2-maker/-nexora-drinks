<?php
	include("Connexion.php");
	include('fonctions.php');
?>
<table id='tvente' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="7"><h5>Choisir une Vente pour Reglement</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" ><h5>Vente </h5></td>
                <td  align="center" ><h5>Date</h5></td>
                <td  align="center"><h5>Client</h5></td>
                <td  align="center" ><h5>MT Total</h5></td>
                <td  align="center" ><h5>MT Restant </h5></td>
                <td  align="center" ><h5>Observation </h5></td>
                <td  align="center" ><h5>Selectionner</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
$i = 0;
$MTVENTE = 0;
$sql='SELECT  DISTINCT (ST.ID_SORTIESTOCK) , ST.DATESORTIESTOCK, C.NOM, ST.OBSERVATION FROM SORTIE_STOCK ST, CLIENT C, REGLEMENT REG WHERE  ST.ID_CLIENT =C.ID_CLIENT AND REG.ID_SORTIESTOCK LIKE "%'.$_POST['code'].'%" AND ST.ID_SORTIESTOCK=REG.ID_SORTIESTOCK ORDER BY ST.DATESORTIESTOCK DESC LIMIT 0,30' ;
	$reponse= $DataBase->query($sql);
	while($rslt= $reponse->fetch())
	{
		//on recupere le mt restant et de la vente dans le dernier paiement
		$reste=0;
		$sql5='SELECT MAX(ID_REGLEMENT) AS ID FROM REGLEMENT WHERE ID_SORTIESTOCK="'.$rslt['ID_SORTIESTOCK'].'" ' ;
		$reponse5= $DataBase->query($sql5);
		while($rslt5= $reponse5->fetch())
			{
				$sql6='SELECT MTRESTANT, MONTANT FROM REGLEMENT WHERE ID_REGLEMENT="'.$rslt5['ID'].'" ' ;
				$reponse6= $DataBase->query($sql6);
				while($rslt6= $reponse6->fetch())
				{
					$reste=$rslt6['MTRESTANT'];
					$montant=$rslt6['MONTANT'];
				}
			}
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_SORTIESTOCK']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATESORTIESTOCK']); ?> </td>
                        <td  align="center"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center"> <?php echo number_format($montant, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($reste, 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo $rslt['OBSERVATION']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Consulter_Reglement&Id=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Select.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>