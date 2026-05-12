<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codeappro']))
	{
		//ici on verifie si l'appro frigo est encore en instance
		$valider=false;
		$sql4=" select statut from approfrigo where id_appro='".$_POST['codeappro']."'";
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{
			if ($rslt4['statut']=='N')
			{
				$valider=true;
			}
		}
		if ($valider==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Appro Frigo est encore en instance.');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
			// Appro non validee
		
				//ici on recupere tous les articles de l'appro on verifie la quantite en stock
			$id=htmlentities(htmlspecialchars(strtolower($_POST["codeappro"])), ENT_QUOTES, 'UTF-8');
			$trve3=false;
			//ici on recupere tous les articles de l'appro 
			$sql3=" select a.id_article, a.stockfrigo, a.qtestock, ar.id_article, ar.nbrebte,ar.qterecu, ar.id_appro from article a, article_recu_frigo ar where ar.id_appro='".$id."' and a.id_article=ar.id_article";
			$reponse3= $DataBase->query($sql3);
			while($rslt3= $reponse3->fetch())
			{
				if ($rslt3['stockfrigo'] < $rslt3['nbrebte'])
				{
					$trve3=true;
				}
			}
			if ($trve3==true)
			{
				?>
					<script language="javascript" type="text/javascript">
						alert('Stock des articles dans le frigo non disponible.BV verifier les quantitès à réduire');
						history.back();
					</script>
				<?php
				exit();	
			}
		
		else
		// article disponible
		{
			// mise à jour dans la bd
			$id=htmlentities(htmlspecialchars(strtolower($_POST["codeappro"])), ENT_QUOTES, 'UTF-8');
			$insere=0;
			$statut='N';
			$sql="update approfrigo set statut=:statut where id_appro='".$id."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'statut' => $statut,
										 ));	
			//ici on recupere tous les articles de l'appro 
			$sql2=" select article.id_article, article.stockfrigo,article.qtestock, article_recu_frigo.id_article, article_recu_frigo.nbrebte,article_recu_frigo.qterecu, article_recu_frigo.id_appro from article, article_recu_frigo where article_recu_frigo.id_appro='".$id."' and article.id_article=article_recu_frigo.id_article";
			$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
				//ici pour chaque enregistrement on fait la mise a jour du stock
				$sql="update article set stockfrigo=:stockfrigo,qtestock=:qtestock where id_article='".$rslt2["id_article"]."'";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
										'stockfrigo' => $rslt2['stockfrigo']-$rslt2['nbrebte'],
										'qtestock' => $rslt2['qtestock']+$rslt2['qterecu'],
										 ));	
				//ici on enregistre dans la liste des mouvements de stocks magasin
				$operation='APPRO_FRIGO';
				$sql="insert into mouvementar values (id_mouv,:codeoperation,:codeart,:date,:heure,:si,:qte,:sf,:operation,:user)";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
										'codeoperation' =>$_POST['codeappro'],
										'codeart' =>$rslt2["id_article"],
										'date' =>dateFormatAnglais($_POST['date_appro']),
										'heure' =>date('H:i'),
										'si' =>$rslt2['qtestock'],
										'qte' =>$rslt2['qterecu'],
										'sf' =>$rslt2['qtestock']+$rslt2['qterecu'],
										'operation' =>$operation,
										'user' =>$_SESSION['login']
										));
				//ici on enregistre dans la liste des mouvements de stocks frigo
				$operation='APPRO_FRIGO';
				$sql="insert into mouvementar_frigo values (id_mouv,:codeoperation,:codeart,:date,:heure,:si,:qte,:sf,:operation,:user)";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
										'codeoperation' =>$_POST['codeappro'],
										'codeart' =>$rslt2["id_article"],
										'date' =>dateFormatAnglais($_POST['date_appro']),
										'heure' =>date('H:i'),
										'si' =>$rslt2['stockfrigo'],
										'qte' =>$rslt2['nbrebte'],
										'sf' =>$rslt2['stockfrigo']-$rslt2['nbrebte'],
										'operation' =>$operation,
										'user' =>$_SESSION['login']
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
						alert('Approvisionnement remis en instance');
						window.location.replace("../index.php?formulaire=Choisir_Appro_Frigo_An_Val");
					</script>
				<?php
				exit();
			}
		}
	}
}
?>
	