<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['code'])&& (isset($_POST['caisse'])))
	{
		//on recupere la categorie du client
		$tr=false;
		$sql5 = " select id_categorie, fraisenlevement from client where nom='INVENTAIRE' ";
		$reponse= $DataBase->query($sql5);
		while($rslt5= $reponse->fetch())
		{
			$categorie=$rslt5["id_categorie"];
			$fraisenlevement=$rslt5["fraisenlevement"];
			$tr=true;
		 }
		 if ($tr==false)
		 {
			 ?>
				<script language="javascript" type="text/javascript">
				alert('BV Creer le Client INVENTAIRE et bien choisir la Categorie pour les Prix a appliquer!');
				history.back();
				</script>
			<?php
			exit();	
		 }


		// on verifie si ce code inv  figure deja dans la bd
		$Code=htmlentities(htmlspecialchars(strtolower($_POST["code"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		$sql = " select id_inv from inventaire where id_inv='".$Code."' ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$trve=true;
		 }
		if($trve==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Ce code Inventaire  existe deja');
				history.back();
				</script>
			<?php
			exit();	
		}

			// insertion de l'inv dans la bd
			$insere8=0;
			$statut="N";
			$sql="insert into inventaire values (:code,:date,:heure,:soldecaisse,:soldesabc,:soldeom,:soldemomo,:creditclient,:creditemballage,:soldebanque,:autrecredit,:creditbrasseries,:creditbanque,:ristournesclients,:autresdebit,:palettebois,:pupalettebois,:paletteplastique,:pupaletteplastique,:emb_plein,:pu_emb_plein,:emb_vide,:pu_emb_vide,:val_produit,:val_global,:statut,:user,:id_categorie,:fraisenlevement)";
			$req = $DataBase->prepare($sql);
			$insere8 = $req->execute(array(
											'code' =>$_POST['code'],
											'date' =>date('Y-m-d'),
											'heure' =>date('H:i'),
											'soldecaisse' =>$_POST['caisse'],
											'soldesabc' =>$_POST['soldesabc'],
											'soldeom' =>$_POST['soldeom'],
											'soldemomo' =>$_POST['soldemomo'],
											'creditclient' =>$_POST['creditclient'],
											'creditemballage' =>$_POST['creditemballage'],
											'soldebanque' =>$_POST['soldebanque'],
											'autrecredit' =>$_POST['autrecredit'],
											'creditbrasseries' =>$_POST['creditsabc'],
											'creditbanque' =>$_POST['creditbanque'],
											'ristournesclients' =>$_POST['ristournes'],
											'autresdebit' =>$_POST['autredebit'],
											'palettebois' =>$_POST['palettebois'],
											'pupalettebois' =>$_POST['pupalettebois'],
											'paletteplastique' =>$_POST['paletteplastique'],
											'pupaletteplastique' =>$_POST['pupaletteplastique'],
											'emb_plein' =>$_POST['emb_plein'],
											'pu_emb_plein' =>$_POST['pu_emb_plein'],
											'emb_vide' =>$_POST['emb_vide'],
											'pu_emb_vide' =>$_POST['pu_emb_vide'],
											'val_produit' =>0,
											'val_global' =>0,
											'statut' =>$statut,
											'user' =>$_SESSION['login'],
											'id_categorie' =>$categorie,
											'fraisenlevement' =>$fraisenlevement
											));	
			if($insere8==0)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Echec de l\'enregistrement de l\'inventaire.');
					history.back();
					</script>
				<?php
				exit();	
			}
  $val_produit_revient=0;
  $val_produit=0;
  $totalcredit=0;
  $totaldebit=0;
  $totalemballage=0;
  $val_global=0;
			
		// on recupere tous les articles et on ajoute à l'inventaire

		$sql6 = " select a.id_article, a.libelle, a.marque, a.nbrebte, a.qtestock, a.stockfrigo, a.id_famille, a.prixrevient, t.prixvente from article a, tarifaire t where a.id_article=t.id_article  and t.id_categorie='".$categorie."' and a.qtestock!=0";
		$reponse6= $DataBase->query($sql6);
		$reponse= $DataBase->query($sql6);
		while($rslt6= $reponse->fetch())
		{
			// insertion dans la bd
			$insere=0;
			$sql3="insert into article_inv values (:id_inv,:codeart,:libelle,:marque,:qtestock,:nbrebte,:stockfrigo,:id_famille,:prixrevient,:prixvente)";
				$req = $DataBase->prepare($sql3);
				$insere = $req->execute(array(
											'id_inv' =>$_POST['code'],
											'codeart' =>$rslt6['id_article'],
											'libelle' =>$rslt6['libelle'],
											'marque' =>$rslt6['marque'],
											'qtestock' =>$rslt6['qtestock'],
											'nbrebte' =>$rslt6['nbrebte'],
											'stockfrigo' =>$rslt6['stockfrigo'],
											'id_famille' =>$rslt6['id_famille'],
											'prixrevient' =>$rslt6['prixrevient'],
											'prixvente' =>$rslt6['prixvente']
											));	
			
			if($insere==0)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Un probleme est survenu lors de la recuperation du stock des articles.');
					history.back();
					</script>
				<?php
				exit();	
			}
			$val_produit_revient=$val_produit_revient+($rslt6['prixrevient']*$rslt6['qtestock']);
			$val_produit=$val_produit+($rslt6['prixvente']*$rslt6['qtestock']);
		}
		//totaux
		$totalcredit=$_POST['caisse']+$_POST['soldesabc']+$_POST['soldeom']+$_POST['soldemomo']+$_POST['creditclient']+$_POST['creditemballage']+$_POST['soldebanque']+$_POST['autrecredit'];  
		$totaldebit=$_POST['creditsabc']+$_POST['creditbanque']+$_POST['ristournes']+$_POST['autredebit'];
		$totalemballage=($_POST['palettebois']*$_POST['pupalettebois'])+($_POST['paletteplastique']*$_POST['pupaletteplastique'])+($_POST['emb_plein']*$_POST['pu_emb_plein'])+($_POST['emb_vide']*$_POST['pu_emb_vide']);;
		
		//Valeur Global
		$val_global=$val_produit+$totalcredit+$totalemballage-$totaldebit;

		//$val_global=$val_produit+$_POST['especes']+$_POST['creditespeces']+$_POST['creditemballage']+($_POST['palette']*$_POST['pupalette'])+($_POST['emb_plein']*$_POST['pu_emb_plein'])+($_POST['emb_vide']*$_POST['pu_emb_vide']);
		
		$sql="update inventaire set  val_produit=:val_produit, val_global=:val_global where id_inv='".$_POST['code']."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'val_produit' =>$val_produit,
										'val_global' =>$val_global,
	
										));	
		?>
				<script language="javascript" type="text/javascript">
				/*alert('enregistrement effectue.');*/
				window.location.replace("../index.php?formulaire=Validation_Inv&Id=<?php echo $_POST['code'];?>");
				</script>
			<?php
			exit();
	}

?>