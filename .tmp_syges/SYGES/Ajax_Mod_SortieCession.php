<?php
	include("Connexion.php");
	include('fonctions.php');
?>
<table id='tappro' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="6"><h5>Liste des sorties cession pour Modification </h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center" ><h5>Date de l'appro </h5></td>
                <td  align="center"><h5>Utilisateur </h5></td>
                <td  align="center" ><h5>Observation </h5></td>
                <td  align="center" ><h5>Modifier</h5></td>
                <td  align="center" ><h5>Valider</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
$sql='SELECT  ID_SORTIESTOCK, DATESORTIESTOCK, LOGIN, OBSERVATION FROM SORTIE_STOCK_CESSION WHERE ID_SORTIESTOCK LIKE "%'.$_POST['codeappro'].'%" AND STATUT="N" ORDER BY DATESORTIESTOCK DESC ' ;
	$reponse= $DataBase->query($sql);
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			//on recupere le nom du USER
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
                        <td  align="center"> <a href="index.php?formulaire=Modification_SortieCession&SC=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/b_edit.png"/> </a></td>
                        <td  align="center"> <a href="index.php?formulaire=Validation_SortieCession&SC=<?php echo $rslt['ID_SORTIESTOCK'];?>"/> <img src="IMG/Liste.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>
