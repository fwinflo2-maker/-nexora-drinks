<?php
	include("Connexion.php");
	include('fonctions.php');
?>
 <table id='liste' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="5"><h5>Liste de type de charge pour enregistrement d'une charge</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
                <td  align="center" ><h5>Code </h5></td>
                <td  align="center"><h5>Libelle </h5></td>
                <td align="center"><h5>Statut </h5></td>
                <td align="center"><h5>Selectionner </h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT * FROM TYPE_CHARGE WHERE LIBELLE LIKE "%'.$_POST["libelle"].'%" AND STATUT="Actif" ORDER BY LIBELLE' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_TYPECHARGE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['LIBELLE']; ?> </td>
                        <td  align="center"> <?php echo $rslt['STATUT']; ?> </td>
                        <td  align="center"> <a href="index.php?formulaire=Enreg_Charge&TC=<?php echo $rslt['ID_TYPECHARGE'];?> "/> <img src="IMG/Select.png"/> </a></td>
                <?php
				$i++;
		 }
				?>
</table>


