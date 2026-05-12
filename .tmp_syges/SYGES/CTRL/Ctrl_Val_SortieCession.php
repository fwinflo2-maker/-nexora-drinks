<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['code']))
	{
		//ici on verifie si l'appro est deja  valide
		$valider=false;
		$sql4=" select statut from sortie_stock_cession where id_sortiestock='".$_POST['code']."'";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
			if ($rslt4['statut']=='V')
			{
				$valider=true;
			}
		}
		if ($valider==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Cession deja pris en compte.');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
			//On recupere ts les emballages puis on verifie si la quantité sollicitee est en stock
			$trve5=false;
			$sql5 = " select e.id_emballage, e.qtestock, e.qte as qteemb, c.id_emballage, c.id_sortiestock, c.qte  from emballage e, emballagesortiecession c where c.id_sortiestock='".$_POST['code']."' and e.id_emballage=c.id_emballage";
			$reponse5= $DataBase->query($sql5);
			while($rslt5= $reponse5->fetch())
			{
				if (($rslt5['qtestock'] < $rslt5['qte'])||($rslt5['qteemb'] < $rslt5['qte']))
					{
						$trve5=true;
					}
			}
			if ($trve5==true)
			{
				?>
					<script language="javascript" type="text/javascript">
						alert('Stock des emballages non disponible.BV verifier les quantitès à sortir');
						history.back();
					</script>
				<?php
				exit();	
			}
			//ici on recupere tous les articles de la vente on verifie la quantite en stock
			$id=htmlentities(htmlspecialchars(strtolower($_POST["code"])), ENT_QUOTES, 'UTF-8');
			$trve3=false;
			$sql3=" select article.id_article, article.qtestock, articlesortie_cession.id_article, articlesortie_cession.qtesortie, articlesortie_cession.id_sortiestock from article, articlesortie_cession where articlesortie_cession.id_sortiestock='".$id."' and article.id_article=articlesortie_cession.id_article";
			$reponse3= $DataBase->query($sql3);
			while($rslt3= $reponse3->fetch())
			{
				if ($rslt3['qtestock'] < $rslt3['qtesortie'])
					{
						$trve3=true;
				
					}
			}
			if ($trve3==true)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Stock des articles non disponible.BV verifier les quantitès à sortir');
					history.back();
					</script>
				<?php
				exit();	
			}		
			    // mise à jour dans la bd		
				//ici on recupere tous les emballages
				$sql6=" select e.id_emballage, e.qtestock, e.qte as qteemb, c.id_emballage, c.qte, c.id_sortiestock from emballage e, emballagesortiecession c where c.id_sortiestock='".$id."' and e.id_emballage=c.id_emballage";
				$reponse6= $DataBase->query($sql6);
				while($rslt6= $reponse6->fetch())
				{
					//ici pour chaque enregistrement on fait la mise a jour du stock 
					$sql6="update emballage set qtestock=:qtestock, qte=:qte where id_emballage='".$rslt6["id_emballage"]."'";
					$req6 = $DataBase->prepare($sql6);
					$insere6 = $req6->execute(array(
												'qtestock' => $rslt6['qtestock']-$rslt6['qte'],
												'qte' => $rslt6['qteemb']-$rslt6['qte']
												 ));
					//ici on enregistre dans la liste des mouvements de stocks
					$operation='CONSIGNE_SORTIE_CESSION';
					$sql="insert into mouvementemb values (id_mouv,:codeoperation,:codeemb,:date,:heure,:qte,:siqte,:sfqte,:sistock,:sfstock,:operation,:user)";
					$req = $DataBase->prepare($sql);
					$insere7 = $req->execute(array(
												'codeoperation' =>$_POST['code'],
												'codeemb' =>$rslt6["id_emballage"],
												'date' =>dateFormatAnglais($_POST['date_appro']),
												'heure' =>date('H:i'),
												'qte' =>$rslt6['qte'],
												'siqte' =>$rslt6['qteemb'],
												'sfqte' =>$rslt6['qteemb'],
												'sistock' =>$rslt6['qtestock'],
												'sfstock' => $rslt6['qtestock']-$rslt6['qte'],
												'operation' =>$operation,
												'user' =>$_SESSION['login']
												));
				
				if($insere6==0)
					{
						?>
						<script language="javascript" type="text/javascript">
							alert('Echec de la mise à jour des stocks des emballages.');
							history.back();
						</script>
						<?php
					    exit();	
					}
				}
				$id=htmlentities(htmlspecialchars(strtolower($_POST["code"])), ENT_QUOTES, 'UTF-8');
				$insere=0;
				$statut='V';
				$sql="update sortie_stock_cession set statut=:statut where id_sortiestock='".$id."'";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
											'statut' => $statut,
											 ));	
				//ici on recupere tous les articles de la cession 
				$sql2=" select article.id_article, article.qtestock, articlesortie_cession.id_article, articlesortie_cession.qtesortie, articlesortie_cession.id_sortiestock from article, articlesortie_cession where articlesortie_cession.id_sortiestock='".$id."' and article.id_article=articlesortie_cession.id_article";
				$reponse2= $DataBase->query($sql2);
				while($rslt2= $reponse2->fetch())
				{
					//ici pour chaque enregistrement on fait la mise a jour du stock
					$sql="update article set qtestock=:qtestock where id_article='".$rslt2["id_article"]."'";
					$req = $DataBase->prepare($sql);
					$insere = $req->execute(array(
											'qtestock' => $rslt2['qtestock']-$rslt2['qtesortie'],
											 ));	
					//ici on enregistre dans la liste des mouvements de stocks
					$operation='SORTIE_CESSION';
					$sql="insert into mouvementar values (id_mouv,:codeoperation,:codeart,:date,:heure,:si,:qte,:sf,:operation,:user,:detenteur,:date_ann)";
					$req = $DataBase->prepare($sql);
					$insere2 = $req->execute(array(
												'codeoperation' =>$_POST['code'],
												'codeart' =>$rslt2["id_article"],
												'date' =>dateFormatAnglais($_POST['date_appro']),
												'heure' =>date('H:i'),
												'si' =>$rslt2['qtestock'],
												'qte' =>$rslt2['qtesortie'],
												'sf' =>$rslt2['qtestock']-$rslt2['qtesortie'],
												'operation' =>$operation,
												'user' =>$_SESSION['login'],
												'detenteur' =>$operation,
												'date_ann' =>date('Y/m/d')
												));	
				}
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
							alert('Cessions pris en compte');
							window.location.replace("../Recu_SortieCession.php?Id=<?php echo $id;?>");
						</script>
					<?php
				exit();
				}
		}
}
?>
	