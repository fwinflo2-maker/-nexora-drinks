<?php
	include("Connexion.php");
	include('../fonctions.php');
	//on verifie le statut de la vente 
		$sql5 = " select statut from sortie_stock where id_sortiestock='".$_POST["codevente"]."'";
		$reponse5= $DataBase->query($sql5);
		$rslt5= $reponse5->fetch();
		if ($rslt5["statut"]=='V')
		{
			?>
				<script language="javascript" type="text/javascript">
					alert('Cette vente est déjà validée. ');
					window.location.replace("../index.php?formulaire=Choisir_Vente_Mod");
				</script>
			<?php
			exit();	
		}
	
	    $sql='SELECT  ID_ARTICLE FROM ARTICLE WHERE LIBELLE="'.$_POST['codeart'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt1= $reponse->fetch())
		{
			$art = $rslt1['ID_ARTICLE']; 
			}

		// mise à jour dans la bd
	
		$sql="delete from articlevendu where id_article='".$art."' and  id_sortiestock='".$_POST["codevente"]."'";
        $req = $DataBase->prepare($sql);
		$insere = $req->execute();	
		
	    $sql='SELECT  ID_CLIENT FROM SORTIE_STOCK WHERE ID_SORTIESTOCK="'.$_POST['codevente'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt2= $reponse->fetch())
		{
			$Clt = $rslt2['ID_CLIENT']; 
		}
		?>
        
				<script language="javascript" type="text/javascript">
				/*alert('Suppression effectue');*/
				window.location.replace("../index.php?formulaire=Modification_Vente&Vte=<?php echo $_POST["codevente"];?>&Clt=<?php echo $Clt;?>");
				</script>
			<?php
			exit();
?>