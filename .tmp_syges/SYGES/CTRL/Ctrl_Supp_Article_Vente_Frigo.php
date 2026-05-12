<?php
	include("Connexion.php");
	include('../fonctions.php');
		//on verifie le statut de la vente
		$sql5 = " select statut from sortie_stock_frigo where id_sortiestock='".$_POST["codevente"]."'";
		$reponse5= $DataBase->query($sql5);
		$rslt5= $reponse5->fetch();
		if ($rslt5["statut"]=='V')
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cette vente est déjà validée. ');
					window.location.replace("../index.php?formulaire=Choisir_Vente_Frigo_Mod");
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
	
		$sql="delete from articlevendu_frigo where id_article='".$art."' and  id_sortiestock='".$_POST["codevente"]."'";
        $req = $DataBase->prepare($sql);
		$insere = $req->execute();	
		?>
        
				<script language="javascript" type="text/javascript">
				/*alert('Suppression effectue');*/
				window.location.replace("../index.php?formulaire=Modification_Vente_Frigo&Vte=<?php echo $_POST["codevente"];?>");
				</script>
			<?php
			exit();
?>