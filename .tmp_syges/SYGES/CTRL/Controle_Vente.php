<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codevente'])&& (isset($_POST['codeart']))&& (isset($_POST['codeclient'])))
	{
		//on recupere la categorie du client
		$tr=false;
		$sql = " select id_categorie from client where id_client='".$_POST["codeclient"]."' ";
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
		   //ici on recupere la Ret Fisc Pro
		 $sql7='SELECT  * FROM CATEGORIE WHERE ID_CATEGORIE="'.$categorie.'" ' ;
		 $reponse7= $DataBase->query($sql7);
		 while($rslt7= $reponse7->fetch())
				{
					
					$tauxretfiscpro=$rslt7['TAUXRETFISCPRO'];
					$tva=$rslt7['TAUXTVA'];
				}
		//on recupere le prix de revient et de vente de l'article 1
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
				alert('L\'Article 1 n\'est pas dans le tarifaire de la Categorie a laquelle appartient ce Client.');
				history.back();
				</script>
			<?php
			exit();	
		}
		 //on recupere le prix de revient et de vente de l'article 2
		if($_POST['qtevendu2']!="")
		{
			$trve2=false;
			$sql = " select t.prixvente,a.prixrevient from article a, tarifaire t where a.id_article=t.id_article and t.id_categorie='".$categorie."' and t.id_article='".$_POST["codeart2"]."' ";
			$reponse= $DataBase->query($sql);
			while($rslt3= $reponse->fetch())
			{
				$prixvente2=$rslt3["prixvente"];
				$prixrevient2=$rslt3["prixrevient"];
				$trve2=true;
		 	}
			if($trve2==false)
			{
			?>
				<script language="javascript" type="text/javascript">
				alert('L\'Article 2 n\'est pas dans le tarifaire de la Categorie a laquelle appartient ce Client.');
				history.back();
				</script>
			<?php
			exit();	
			}
		}
		//on recupere le prix de revient et de vente de l'article 3
		if($_POST['qtevendu3']!="")
		{
			$trve3=false;
			$sql = " select t.prixvente,a.prixrevient from article a, tarifaire t where a.id_article=t.id_article and t.id_categorie='".$categorie."' and t.id_article='".$_POST["codeart3"]."' ";
			$reponse= $DataBase->query($sql);
			while($rslt4= $reponse->fetch())
			{
				$prixvente3=$rslt4["prixvente"];
				$prixrevient3=$rslt4["prixrevient"];
				$trve3=true;
		 	}
			if($trve3==false)
			{
			?>
				<script language="javascript" type="text/javascript">
				alert('L\'Article 3 n\'est pas dans le tarifaire de la Categorie a laquelle appartient ce Client.');
				history.back();
				</script>
			<?php
			exit();	
			}
		}
		// on verifie si ce code vente  figure deja dans la bd
		$Code=htmlentities(htmlspecialchars(strtolower($_POST["codevente"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		$sql = " select id_sortiestock from sortie_stock where id_sortiestock='".$Code."' ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		$trve=true;
		 }
		if($trve==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Le code de cette vente existe deja');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{

			// insertion de la vente dans la bd
			$insere=0;
			$statut="N";
			$sql="insert into sortie_stock values (:codevente,:date_vente,:codeclient,:observationsortie,:login,:statut,:heure,:creditristourne,:fraisenlevement,:mtfacture,:tauxtva,:tauxretfiscpro)";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
											'codevente' =>$_POST['codevente'],
											'date_vente' =>dateFormatAnglais($_POST['date_vente']),
											'codeclient' =>$_POST['codeclient'],
											'observationsortie' =>$_POST['observationsortie'],
											'login' =>$_SESSION['login'],
											'statut' =>$statut,
											'heure' =>date('H:i'),
											'creditristourne' =>$_POST['ristourne'],
											'fraisenlevement' =>0,
											'mtfacture' =>0, 
											'tauxtva' =>$tva,
											'tauxretfiscpro' =>$tauxretfiscpro
											));	
			if($insere==0)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Echec de l\'enregistrement de la vente.');
					history.back();
					</script>
				<?php
				exit();	
			}
			//on recupere le code de la vente pour l'affichage du recu
			$lastid=$DataBase->lastInsertId();
			
			// insertion de l'article 1 dans la bd
			$insere1=0;
			$observation="RAS";
			$sql="insert into articlevendu values (:codeart,:codevente,:qtevendu,:prixrevient,:prixvente,:observation)";
			$req = $DataBase->prepare($sql);
			$insere1 = $req->execute(array(
											'codeart' =>$_POST['codeart'],
											'codevente' =>$_POST['codevente'],
											'qtevendu' =>$_POST['qtevendu'],
											'prixvente' =>$_POST['qtevendu']*$prixvente,
											'prixrevient' =>$_POST['qtevendu']*$prixrevient,
											'observation' =>$observation,
											));
			
			if($insere1==0)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Echec de l\'enregistrement de l\'article 1.');
					history.back();
					</script>
				<?php
				exit();	
			}
			// insertion de l'article 2 dans la bd
			if($_POST['qtevendu2']!="")
			{
				$insere2=0;
				$observation="RAS";
				$sql="insert into articlevendu values (:codeart,:codevente,:qtevendu,:prixrevient,:prixvente,:observation)";
				$req = $DataBase->prepare($sql);
				$insere2 = $req->execute(array(
												'codeart' =>$_POST['codeart2'],
												'codevente' =>$_POST['codevente'],
												'qtevendu' =>$_POST['qtevendu2'],
												'prixvente' =>$_POST['qtevendu2']*$prixvente2,
												'prixrevient' =>$_POST['qtevendu2']*$prixrevient2,
												'observation' =>$observation,
												));
				
				if($insere2==0)
				{
					?>
						<script language="javascript" type="text/javascript">
						alert('Echec de l\'enregistrement de l\'article 2.');
						history.back();
						</script>
					<?php
					exit();	
				}
			}
		// insertion de l'article 3 dans la bd
		if($_POST['qtevendu3']!="")
		{
			$insere3=0;
			$observation="RAS";
			$sql="insert into articlevendu values (:codeart,:codevente,:qtevendu,:prixrevient,:prixvente,:observation)";
			$req = $DataBase->prepare($sql);
			$insere3 = $req->execute(array(
											'codeart' =>$_POST['codeart3'],
											'codevente' =>$_POST['codevente'],
											'qtevendu' =>$_POST['qtevendu3'],
											'prixvente' =>$_POST['qtevendu3']*$prixvente3,
											'prixrevient' =>$_POST['qtevendu3']*$prixrevient3,
											'observation' =>$observation,
											));
			
				if($insere3==0)
				{
					?>
						<script language="javascript" type="text/javascript">
						alert('Echec de l\'enregistrement de l\'article 3.');
						history.back();
						</script>
					<?php
					exit();	
				}
		}
		?>
				<script language="javascript" type="text/javascript">
				/*alert('enregistrement effectue.');*/
				window.location.replace("../index.php?formulaire=Modification_Vente&Vte=<?php echo $_POST['codevente'];?>");
				</script>
			<?php
			exit();
	}
}
?>