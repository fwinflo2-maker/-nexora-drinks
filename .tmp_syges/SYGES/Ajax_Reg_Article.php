<?php
	include("Connexion.php");
	include('fonctions.php');
?>
 <table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="6"><h5>Liste des Articles pour Regularisation des stocks</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="left" ><h5>Libelle </h5></td>
                <td  align="center"><h5>Conditionnement </h5></td>
                <td  align="center"><h5> Stock Magasin</h5></td>
                <td  align="center" ><h5>Stock Frigo(Bouteille) </h5></td>
                <td  align="center" ><h5>Modifier</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT  ID_ARTICLE, LIBELLE, MARQUE, QTESTOCK, NBREBTE, STOCKFRIGO FROM ARTICLE WHERE STATUT="Actif" AND LIBELLE LIKE "%'.$_POST['code'].'%" ORDER BY LIBELLE ' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_ARTICLE']; ?> </td>
                        <td  align="left"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['MARQUE'].' '.$rslt['NBREBTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['QTESTOCK']; ?> </td>
                        <td  align="center"> <?php echo $rslt['STOCKFRIGO']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Reg_St_Article&Id=<?php echo $rslt['ID_ARTICLE'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>

