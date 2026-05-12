<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codevente'])&& (isset($_POST['codeart'])))
	{
		
		// on verifie si cette vente n'est pas validée  
		$Codevente=htmlentities(htmlspecialchars(strtolower($_POST["codevente"])), ENT_QUOTES, 'UTF-8');
		$Codeart=htmlentities(htmlspecialchars(strtolower($_POST["codeart"])), ENT_QUOTES, 'UTF-8');
		$sql5 = " select statut from sortie_stock where id_sortiestock='".$Codevente."'";
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
			// on verifie si cet article est deja dans cette vente  
			$trve= false;
			$sql = " select id_sortiestock, id_article from articlevendu where id_sortiestock='".$Codevente."' and id_article='".$Codeart."'";
			$reponse= $DataBase->query($sql);
			while($rslt= $reponse->fetch())
			{
				$trve=true;
			 }
			if($trve==true)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cet  article existe deja dans cette vente. ');
					history.back();
					</script>
				<?php
				exit();	
			}

			//on recupere la categorie du client
			$tr=false;
			$sql = " select id_categorie from client where id_client='".$_POST['codeclient']."' ";
			$reponse= $DataBase->query($sql);
			while($rslt5= $reponse->fetch())
			{
				$categorie=$rslt5["id_categorie"];
				$tr=true;
		 	}
		 	if ($tr==false)
		 	{
			 ?>
				<script language="javascript" type="text/javascript">
				alert('La Categorie de ce Client n\'existe pas. BV Verifier!');
				history.back();
				</script>
			<?php
			exit();	
		 	}
			//on recupere le prix de revient et de vente de l'article 
			$trve1=false;
			$sql = " select t.prixvente,a.prixrevient from article a, tarifaire t where a.id_article=t.id_article and t.id_categorie='".$categorie."' and t.id_article='".$_POST["codeart"]."' ";
			$reponse= $DataBase->query($sql);
			while($rslt2= $reponse->fetch())
			{
				$prixvente=$rslt2["prixvente"];
				$prixrevient=$rslt2["prixrevient"];
				$trve1=true;
		 	}
			if($trve1==false)
			{
			?>
				<script language="javascript" type="text/javascript">
				alert('Cet Article  n\'est pas dans le tarifaire de la Categorie a laquelle appartient ce Client.');
				history.back();
				</script>
			<?php
			exit();	
			}
			// insertion de l'article dans la bd
			$insere2=0;
			$observation="RAS";
			$sql="insert into articlevendu values (:codeart,:codevente,:qtevendu,:prixrevient,:prixvente,:observationvente)";
			$req = $DataBase->prepare($sql);
			$insere2 = $req->execute(array(
											'codeart' =>$_POST['codeart'],
											'codevente' =>$_POST['codevente'],
											'qtevendu' =>$_POST['qtevendu'],
											'prixrevient' =>$_POST['qtevendu']*$prixrevient,
											'prixvente' =>$_POST['qtevendu']*$prixvente,
											'observationvente' =>$observation
											));
	
			
			if($insere2==0)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Echec de l\'enregistrement.');
					history.back();
					</script>
				<?php
				exit();	
			}
			else
			{
			?>
					<script language="javascript" type="text/javascript">
					/*alert('enregistrement effectue.');*/
					history.back();
					</script>
				<?php
				exit();
			}
	}
?>