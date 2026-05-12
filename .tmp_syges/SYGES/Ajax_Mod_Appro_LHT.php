<?php
	include("Connexion.php");
	include('fonctions.php');
?>
          <table id='tappro' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="7"><h5>Liste des approvisionnements pour Modification Liquide HT</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center" ><h5>Date de l'appro </h5></td>
                <td  align="center"><h5>Fournisseur </h5></td>
                <td  align="center"><h5>Liquide HT </h5></td>
                <td  align="center"><h5>Nbre de Colis </h5></td>
                <td  align="center" ><h5>Observation </h5></td>
                <td  align="center" ><h5>Modifier</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
$sql='SELECT  ID_APPRO, DATE_APPRO, ID_FOURNISSEUR, OBSERVATION, LIQUIDEHT, NBRECOLIS FROM APPROVISIONNEMENT WHERE ID_APPRO LIKE "%'.$_POST['codeappro'].'%" ORDER BY DATE_APPRO DESC' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			//on recupere le nom du fssr
		$sql2='SELECT  NOM, ID_FOURNISSEUR FROM FOURNISSEUR WHERE ID_FOURNISSEUR="'.$rslt['ID_FOURNISSEUR'].'"' ;
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
                        <td  align="center"> <?php echo $rslt['ID_APPRO']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_APPRO']); ?> </td>
                        <td  align="center"> <?php echo  $nom ; ?> </td>
                        <td  align="center"> <?php echo  number_format($rslt['LIQUIDEHT'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo $rslt['NBRECOLIS']; ?> </td>
                        <td  align="center"> <?php echo $rslt['OBSERVATION']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modification_Appro_LHT&Ap=<?php echo $rslt['ID_APPRO'];?> &Fs=<?php echo $rslt['ID_FOURNISSEUR'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>