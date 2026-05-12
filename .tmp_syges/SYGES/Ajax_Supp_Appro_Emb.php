<?php
	include("Connexion.php");
	include('fonctions.php');
?>

       <table id='tappro' border="0" width="100%" align="center">
       <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h5>Liste des Appro. Emballage pour Suppression</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center" ><h5>Date de l'appro </h5></td>
                <td  align="center"><h5>Utilisateur </h5></td>
                <td  align="center" ><h5>Observation </h5></td>
                <td  align="center" ><h5>Selectionner</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
$i = 0;	
$sql='SELECT  ID_APPRO, DATE_APPRO, LOGIN, OBSERVATION FROM APPROEMB WHERE ID_APPRO LIKE "%'.$_POST['codeappro'].'%" AND STATUT="N" ORDER BY DATE_APPRO DESC ' ;
$reponse= $DataBase->query($sql);
while($rslt= $reponse->fetch())
		{
			//on recupere le nom du user
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
                        <td  align="center"> <?php echo $rslt['ID_APPRO']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_APPRO']); ?> </td>
                        <td  align="center"> <?php echo  $nom ; ?> </td>
                        <td  align="center"> <?php echo $rslt['OBSERVATION']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Supp_Appro_Emb&Ap=<?php echo $rslt['ID_APPRO'];?>"/> <img src="IMG/Select.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>