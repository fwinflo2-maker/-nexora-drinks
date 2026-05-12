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
		
	    $sql='SELECT  ID_ARTICLE, PRIXDETAIL, PRIXREVIENT, NBREBTE, LIBELLE FROM ARTICLE WHERE LIBELLE="'.$_POST['codeart'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$art = $rslt['ID_ARTICLE'];
			$PD =  $rslt['PRIXDETAIL'];
			$PR=$rslt['PRIXREVIENT']/$rslt['NBREBTE'];
		}

		// mise à jour dans la bd
		$vente=htmlentities(htmlspecialchars(strtolower($_POST["codevente"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$sql="update articlevendu_frigo set qtesortie=:qtesortie, prixrevient=:prixrevient, prixvente=:prixvente, observation=:observation  where id_article='".$art."' and  id_sortiestock='".$vente."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'qtesortie' =>$_POST['qtevendu'],
										'prixrevient' =>$PR * $_POST['qtevendu'],
										'prixvente' =>$PD * $_POST['qtevendu'],
										'observation' =>$_POST['observationvente']

										 ));	
				
		if($insere==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de la mise à jour.');
					history.back();
				</script>
			<?php
			exit();	
		}
		else
		{

		?>
        
				<script language="javascript" type="text/javascript">
				/*alert('Modification effectue');*/
				window.location.replace("../index.php?formulaire=Modification_Vente_Frigo&Vte=<?php echo $_POST["codevente"];?>");
				</script>
			<?php
			exit();
		}
?>