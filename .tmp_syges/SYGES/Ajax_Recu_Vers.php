<?php
	include("Connexion.php");
	include('fonctions.php');
?>
         <table id='tvers' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="6"><h5>Choisir un Versements pour Reimpression</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center" width="20%"><h5>N° Versement </h5></td>
                <td  align="center" ><h5>Date  </h5></td>
                <td  align="center"><h5>Vendeur</h5></td>
                <td  align="center"><h5>Montant</h5></td>
                <td  align="center" ><h5>Observation </h5></td>
                <td  align="center" ><h5>Selectionner</h5></td>
			</tr>
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT  V.NUM_VERS, V.DATE_VERS, V.VENDEUR,VD.LOGIN, VD.NOM, V.OBSERVATION, V.MONTANT FROM VERSEMENT V, USER VD WHERE  V.VENDEUR=VD.LOGIN AND  V.STATUT="V" AND V.NUM_VERS LIKE "%'.$_POST['codevers'].'%" ORDER BY V.DATE_VERS DESC LIMIT 0,20' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['NUM_VERS']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATE_VERS']); ?> </td>
                        <td  align="center"> <?php echo $rslt['NOM']; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['MONTANT'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo $rslt['OBSERVATION']; ?> </td>
                        <td  align="center"> <a href="RecuVers.php?Vers=<?php echo $rslt['NUM_VERS'];?> &VD=<?php echo $rslt['LOGIN'];?> "/> <img src="IMG/Select.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>