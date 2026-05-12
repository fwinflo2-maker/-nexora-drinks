<?php
	include("Connexion.php");
	include('fonctions.php');
?>
<table id='ttarifaire' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="6"><h5>Liste des Prix de Vente par Catégorie</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td  align="left" ><h5> Libelle</h5></td>
                <td  align="</h5>"><h5>Conditionnement</h5></td>
                <td  align="left" ><h5>Catégorie </h5></td>
                <td  align="center" ><h5>Prix Vente</h5></td>
                <td  align="center" ><h5>Modifier</h5></td>
                <td  align="center" ><h5>Supprimer</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT  A.ID_ARTICLE, A.LIBELLE, A.MARQUE, A.NBREBTE, C.LIBELLE AS LIBCAT, C.ID_CATEGORIE, T.PRIXVENTE FROM ARTICLE A, CATEGORIE C, TARIFAIRE T WHERE T.ID_CATEGORIE LIKE "%'.$_POST['categorie'].'%" AND A.ID_ARTICLE=T.ID_ARTICLE AND T.ID_CATEGORIE=C.ID_CATEGORIE ORDER BY A.ID_ARTICLE' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                		<td  align="left"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="</h5>"> <?php echo $rslt['MARQUE'].'('.$rslt['NBREBTE'].')'; ?> </td>
                        <td  align="left"> <?php echo $rslt['LIBCAT']; ?> </td>
                        <td  align="center"> <?php echo $rslt['PRIXVENTE'].' F'; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modification_Tarif&Cat=<?php echo $rslt['ID_CATEGORIE'];?>&Ar=<?php echo $rslt['ID_ARTICLE'];?>&Pv=<?php echo $rslt['PRIXVENTE'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                        <td  align="center"> <a href="index.php?formulaire=Suppression_Tarif&Cat=<?php echo $rslt['ID_CATEGORIE'];?>&Ar=<?php echo $rslt['ID_ARTICLE'];?>&Pv=<?php echo $rslt['PRIXVENTE'];?> "/> <img src="IMG/Supp.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>