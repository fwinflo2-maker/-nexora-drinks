<?php
	include("Connexion.php");
	include('fonctions.php');
?>
          <table id='tfournisseur' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="8"><h5>Liste des fournisseurs pour Modification</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>Code </h5></td>
                <td  align="center" ><h5>Nom </h5></td>
                <td  align="center"><h5>Num. Tel </h5></td>
                <td  align="center" ><h5>E-Mail </h5></td>
                <td  align="center" ><h5>Modifier</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT  ID_FOURNISSEUR, NOM, NUMTEL, EMAIL, STATUT FROM FOURNISSEUR WHERE NOM LIKE "%'.$_POST['nom'].'%" ORDER BY NOM ' ;
	
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_FOURNISSEUR']; ?> </td>
                        <td  align="center"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center"> <?php echo $rslt['NUMTEL']; ?> </td>
                        <td  align="center"> <?php echo $rslt['EMAIL']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Modification_Fournisseur&Id=<?php echo $rslt['ID_FOURNISSEUR'];?> "/> <img src="IMG/b_edit.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>
