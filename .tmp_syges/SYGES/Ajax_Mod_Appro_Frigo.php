<?php
	include("Connexion.php");
	include('fonctions.php');
?>
 <table id='tappro' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="6"><h5>Liste des approvisionnements pour Modification</h5></td>
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
			
$sql='SELECT  ID_APPRO, DATE_APPRO, LOGIN, OBSERVATION FROM APPROFRIGO WHERE ID_APPRO LIKE "%'.$_POST['codeappro'].'%" AND STATUT="N" ORDER BY DATE_APPRO DESC ' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		//
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_APPRO']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_APPRO']); ?> </td>
                        <td  align="center"> <?php echo $rslt['LOGIN'] ; ?> </td>
                        <td  align="center"> <?php echo $rslt['OBSERVATION']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modification_Appro_Frigo&Ap=<?php echo $rslt['ID_APPRO'];?> &Fs=<?php echo $rslt['ID_FOURNISSEUR'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                        <td  align="center"> <a href="index.php?formulaire=Validation_Appro_Frigo&Ap=<?php echo $rslt['ID_APPRO'];?> &Fs=<?php echo $rslt['ID_FOURNISSEUR'];?> "/> <img src="IMG/Liste.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>
