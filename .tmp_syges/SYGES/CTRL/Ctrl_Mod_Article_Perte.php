<?php
	include("Connexion.php");
	include('../fonctions.php');
	    $sql='SELECT  ID_ARTICLE, PRIXDETAIL, PRIXREVIENT, NBREBTE, LIBELLE FROM ARTICLE WHERE LIBELLE="'.$_POST['codeart'].'" ' ;
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$art = $rslt['ID_ARTICLE'];
			$PD =  $rslt['PRIXDETAIL'];
			$prixrevient =  $rslt['PRIXREVIENT']/$rslt['NBREBTE'];
		}

		// mise à jour dans la bd
		$vente=htmlentities(htmlspecialchars(strtolower($_POST["codevente"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$prixvente=0;
		$sql="update articlevendu_frigo set qtesortie=:qtesortie,prixrevient=:prixrevient, prixvente=:prixvente, observation=:observation  where id_article='".$art."' and  id_sortiestock='".$vente."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'qtesortie' =>$_POST['qtevendu'],
										'prixrevient' =>$_POST['qtevendu']*$prixrevient,
										'prixvente' =>$prixvente,
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
				window.location.replace("../index.php?formulaire=Modification_Perte&Vte=<?php echo $_POST["codevente"];?>");
				</script>
			<?php
			exit();
		}
?>