<?php
if (isset ($_SESSION['habilitation']) && ($_SESSION['habilitation']=="Administrateur" || $_SESSION['habilitation']=="DGA" ||$_SESSION['habilitation']=="Magasinier" || $_SESSION['habilitation']=="Superviseur"))
{
	include('Connexion.php');
	include('fonctions.php');
	$Debut=dateFormatAnglais($_GET['DateD']);
	$Fin=dateFormatAnglais($_GET['DateF']);
	$Users=$_GET['user'];
?>
<!DOCTYPE html >
<html >
<head>
        <!-- Custom styles for this template-->
<link href="css/sb-admin-2.min.css" rel="stylesheet">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultation d'une Feuille de Route</title>
</head>

<body>
<table id='liste' border="1" width="100%" align="center">
          <tr align="center" bgcolor="#CCCCCC">
                <td width="250px" colspan="3"><a href="Feuille_Route.php?DateD=<?php echo $_GET["DateD"];?>&DateF=<?php echo $_GET["DateF"];?>&user=<?php echo $_GET['user'];?>"/><input class="btn btn-primary btn-user btn-block" type="button" value="Imprimer" /></a></td>
          </tr>
          <tr align="center" bgcolor="#CCCCCC">
          	<td colspan="3"><h4>Feuille de Route des Préventes Clients </h4></td>
          </tr>
          <tr>
                <td align="center" colspan="3"><h5>Période Du : <?php echo dateFormatFrancais($Debut); ?> Au : <?php echo dateFormatFrancais($Fin); ?> </h5></td>
          </tr>
          <tr>
                <td bgcolor="#CCCCCC" align="center" colspan="3"><h5>Utilisateur : <?php echo $_GET['user']; ?></h5></td>
          </tr>
     <!--Liste des Produits-->
		 <tr>
			<td><h4>Client(s)</h4></td>
            <?php
			// Liste sans doublons des articles de la periode
		$sql='select distinct a.libelle from article a, sortie_stock st, articlevendu av where a.id_article=av.id_article and av.id_sortiestock=st.id_sortiestock and st.login="'.$Users.'" and st.datesortiestock BETWEEN "'.$Debut.'" AND "'.$Fin.'"    and st.statut="V" ORDER BY a.libelle' ;
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
	   {
		   ?>
			<!--on ecris le nom du client-->
			<td  align="left"> <h5><?php echo $rslt['libelle']; ?> </h5></td>
            <?php
	   }
	   ?>
         </tr>   
<?php

	//Ici on recupere la liste des factures concernés
		$sql2='select c.id_client, c.nom, st.id_sortiestock from client c, sortie_stock st where c.id_client=st.id_client and st.login="'.$Users.'" and st.datesortiestock BETWEEN "'.$Debut.'" AND "'.$Fin.'"   and st.statut="V"  ORDER BY c.nom' ;
		$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
	   {
		   echo "<tr>";
		   ?>
			<!--on ecris le nom du client-->
			<td  align="left"> <h5><?php echo $rslt2['nom']; ?> </h5></td>
            <?php

					// Liste sans doublons des articles de la periode
					$sql4='select distinct a.libelle, a.id_article from article a, sortie_stock st, articlevendu av where a.id_article=av.id_article and av.id_sortiestock=st.id_sortiestock and st.login="'.$Users.'" and st.datesortiestock BETWEEN "'.$Debut.'" AND "'.$Fin.'"   and st.statut="V" ORDER BY a.libelle' ;
					$reponse4= $DataBase->query($sql4);
					while($rslt4= $reponse4->fetch())
				   {
						//Pour Chaque facture et produit du client on affiche les qtes pour chaque article 
						$sql5='select av.qtesortie from sortie_stock st, articlevendu av where av.id_sortiestock=st.id_sortiestock  and av.id_article="'.$rslt4['id_article'].'"  and st.id_sortiestock="'.$rslt2['id_sortiestock'].'"' ;
						$reponse5= $DataBase->query($sql5);
						$rslt5= $reponse5->fetch();
						if ($rslt5!="")
					   {
						   ?>
							<!--on ecris la QTE-->
							<td  align="left"> <h5><?php echo $rslt5['qtesortie']; ?> </h5></td>
							<?php
					   }
					   else
					   {
						   ?>
							<!--on ecris rien-->
							<td  align="left"> </td>
							<?php
					   }
				   }
	   
	   ?>
	      </tr>
        <?php
	}


// TOTAUX
?>
<td align="center"><h4>Totaux : </h4></td>

<?php
$tcasier=0;
$tcolis=0;
$sql8='select distinct a.libelle, a.id_article, a.marque  from article a, sortie_stock st, articlevendu av where a.id_article=av.id_article and av.id_sortiestock=st.id_sortiestock and st.login="'.$Users.'" and st.datesortiestock BETWEEN "'.$Debut.'" AND "'.$Fin.'"    and st.statut="V" ORDER BY a.libelle' ;
		$reponse8= $DataBase->query($sql8);
		while($rslt8= $reponse8->fetch())
	   {
		   $qtesortie=0;
		$sql9='select a.id_article,av.qtesortie  from article a, sortie_stock st, articlevendu av where a.id_article=av.id_article and av.id_sortiestock=st.id_sortiestock and st.login="'.$Users.'" and st.datesortiestock BETWEEN "'.$Debut.'" AND "'.$Fin.'"    and st.statut="V" and a.id_article="'.$rslt8['id_article'].'" ORDER BY a.libelle' ;
			$reponse9= $DataBase->query($sql9);
			while($rslt9= $reponse9->fetch())
		   {
			   $qtesortie=$qtesortie+$rslt9['qtesortie'];
		   }
		   ?>
           
		   		<td><h5><?php echo $qtesortie;?> </h5></td>
          <?php
		  //total casier
		  if(($rslt8['marque']=="CASIER") || ($rslt8['marque']=="casier")|| ($rslt8['marque']=="CASIERS")|| ($rslt8['marque']=="casiers"))
		  {
			  $tcasier=$tcasier+$qtesortie;
		  }
		  //total colis
		  $tcolis=$tcolis+$qtesortie;	  
	   }
?>
<tr>
	<td align="center" bgcolor="#CCCCCC" colspan="3"><h3>Casiers :<?php echo $tcasier.'  -  PET : '.($tcolis-$tcasier).'  -    Colis : '.$tcolis;?> </h3></td>
</tr>
</table>
</body>
</html>
<?php 
}
else
{
?>
				<script language="javascript" type="text/javascript">
				alert('Vous n\'etes pas habiliter a  acceder a cette page.');
				history.back();
				</script>
<?php
}
?>