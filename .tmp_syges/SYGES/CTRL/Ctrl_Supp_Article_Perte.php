<?php
	include("Connexion.php");
	include('../fonctions.php');
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
				window.location.replace("../index.php?formulaire=Modification_Perte&Vte=<?php echo $_POST["codevente"];?>");
				</script>
			<?php
			exit();
?>