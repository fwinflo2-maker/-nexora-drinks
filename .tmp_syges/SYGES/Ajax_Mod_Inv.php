<?php
	session_start();
	include("Connexion.php");
	include('fonctions.php');

?>
 <table id='tinv' border="0" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="18"><h5>Liste des Inventaires pour Modification</h5></td>
          </tr>
            <tr bgcolor="#CCCCCC">
            	
            	<td align="center"><h5>Code </h5></td>
                <td  align="center" ><h5>Date</h5></td>
                <td  align="center"><h5> Caisse </h5></td>
                <td  align="center" ><h5>Solde SABC </h5></td>
                <td  align="center" ><h5> OM</h5></td>
                <td  align="center" ><h5> MOMO</h5></td>
                <td  align="center" ><h5>Credit Client</h5></td>
                <td  align="center" ><h5>Credit emballage</h5></td>
                <td  align="center" ><h5>Solde Banque</h5></td>
                <td  align="center" ><h5>Credit SABC</h5></td>
                <td  align="center" ><h5>Credit Banque</h5></td>
                <td  align="center" ><h5>Ristournes</h5></td>
                <td  align="center" ><h5>P. Bois</h5></td>
                <td  align="center" ><h5>P. Plastique(s)</h5></td>
                <td  align="center" ><h5>Emb. pleins</h5></td>
                <td  align="center" ><h5>Emb. Vides</h5></td>
                <td  align="center" ><h5>Modifier</h5></td>
                <td  align="center" ><h5>Imprimer</h5></td>
			</tr>
			
<?php
$couleur = "darkgray";
			$i = 0;
			
	$sql='SELECT  * FROM INVENTAIRE WHERE ID_INV LIKE "%'.$_POST['code'].'%" AND STATUT="N"' ;
	$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			if ($i%2 == 0)
					$couleur = '#E0E0E0';
				else
					$couleur = "white";
				echo "<tr bgcolor=$couleur>";
				?>
                        <td  align="center"> <?php echo $rslt['ID_INV']; ?> </td>
                        <td  align="center"> <?php echo dateFormatFrancais($rslt['DATE']).' '.$rslt['HEURE']; ?> </td>
                        <td  align="center"> <?php echo   number_format($rslt['SOLDECAISSE'], 0, ',', ' ') ; ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['SOLDESABC'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['SOLDEOM'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['SOLDEMOMO'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['CREDITCLIENT'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['CREDITEMBALLAGE'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['SOLDEBANQUE'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['CREDITBRASSERIES'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['CREDITBANQUE'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['RISTOURNESCLIENTS'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['PALETTEBOIS'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['PALETTEPLASTIQUE'], 0, ',', ' '); ?> </td>
                        <td  align="center"> <?php echo number_format($rslt['EMB_PLEIN'], 0, ',', ' '); ?> </td>  
                        <td  align="center"> <?php echo number_format($rslt['EMB_VIDE'], 0, ',', ' '); ?> </td>
                         <?php
                         //ici on verifie La date de l'appro
                         if (($rslt['DATE']= date("Y-m-d")) && ($_SESSION['habilitation']!='Administrateur' && $_SESSION['habilitation']!='DGA'))
                          {
 							  ?>
							  	<td  align="center"></td>
                              <?php
						   	
                          }
						  else
						  {
							  ?>
							  <td  align="center"> <a href="index.php?formulaire=Modification_Inv&Id=<?php echo $rslt['ID_INV'];?>"/> <img src="IMG/b_edit.png"/> </a></td>
                              <?php
						   }
						  ?>
                        
                        <td  align="center"> <a href="index.php?formulaire=Validation_Inv&Id=<?php echo $rslt['ID_INV'];?>"/> <img src="IMG/Liste.png"/> </a></td>
                     </tr>
                <?php
				$i++;
		 }
				?>
</table>