<?php
	include("Connexion.php");
	include('fonctions.php');	
?>
 <table id='tarticle' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="7"><h5>Liste des Articles</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center"><h5>Condition... </h5></td>
                <td  align="center" ><h5>Nbre Bouteille </h5></td>
                <td  align="left" ><h5>Libelle </h5></td>
                <td  align="center"><h5>Prix de Vente </h5></td>
                <td  align="center"><h5>Prix au detail </h5></td>
                <td  align="center"><h5>Famille </h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT  ID_ARTICLE, LIBELLE, MARQUE, QTESTOCK, NBREBTE, PRIXVENTE, PRIXREVIENT, PRIXDETAIL, ID_FAMILLE   FROM ARTICLE WHERE LIBELLE LIKE "%'.$_POST['libelle'].'%" ORDER BY LIBELLE ' ;
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
                        <td  align="center"> <?php echo $rslt['MARQUE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['NBREBTE']; ?> </td>
                        <td  align="left"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['PRIXVENTE'].' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $rslt['PRIXDETAIL'].' FCFA'; ?> </td>
                        <td  align="center"> <?php echo $rslt['ID_FAMILLE']; ?> </td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>