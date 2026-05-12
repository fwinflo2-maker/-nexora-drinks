<?php
	include("Connexion.php");
	include('fonctions.php');
?>
          <table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="12"><h5>Liste des Articles pour Modification</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center"><h5>Code </h5></td>
                <td  align="left" ><h5>Libelle </h5></td>
                <td  align="center"><h5>Conditionnement </h5></td>
                <td  align="center"><h5>Nbre Bouteille </h5></td>
                <td  align="center"><h5>Qte Stock </h5></td>
                <td  align="center" ><h5>Prix Revient </h5></td>
                <td  align="center" ><h5>Prix Vente </h5></td>
                <td  align="center" ><h5>Prix Detail </h5></td>
                <td  align="center" ><h5>Taux Remise </h5></td>
                <td  align="center" ><h5>Taux Ristourne </h5></td>
                <td  align="center" ><h5>Famille </h5></td>
                <td  align="center" ><h5>Modifier</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT  ID_ARTICLE, LIBELLE, MARQUE, NBREBTE, QTESTOCK, PRIXVENTE, PRIXDETAIL, PRIXREVIENT, TAUXREMISE,TAUXRISTOURNE, ID_FAMILLE  FROM ARTICLE WHERE LIBELLE LIKE "%'.$_POST['libelle'].'%" ORDER BY ID_FAMILLE ' ;
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
                        <td  align="center"> <?php echo $rslt['MARQUE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['NBREBTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['QTESTOCK']; ?> </td>
                        <td  align="center"> <?php echo $rslt['PRIXREVIENT']; ?> </td>
                        <td  align="center"> <?php echo $rslt['PRIXVENTE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['PRIXDETAIL']; ?> </td>
                        <td  align="center"> <?php echo $rslt['TAUXREMISE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['TAUXRISTOURNE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['ID_FAMILLE']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modification_Article&Id=<?php echo $rslt['ID_ARTICLE'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>