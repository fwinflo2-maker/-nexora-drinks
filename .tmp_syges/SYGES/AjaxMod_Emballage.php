<?php
	include("Connexion.php");
	include('fonctions.php');
?>
          <table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h5>Liste des Emballages pour Modification</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center" ><h5>Libelle </h5></td>
                <td  align="center"><h5>Mt Consigne </h5></td>
                <td  align="center"><h5>Statut </h5></td>
                <td  align="center" ><h5>Modifier</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
$i = 0;	
$sql='SELECT  ID_EMBALLAGE, LIBELLE, MT_CONSIGNE, STATUT FROM EMBALLAGE WHERE LIBELLE LIKE "%'.$_POST['libelle'].'%" ORDER BY LIBELLE ' ;
$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_EMBALLAGE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['MT_CONSIGNE'].' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $rslt['STATUT']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modification_Emb&Id=<?php echo $rslt['ID_EMBALLAGE'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>
