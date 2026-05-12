<?php
	include("Connexion.php");
		// on cree la sauvegarde

			// insertion dans la bd
			$insere=0;
			$sql="insert into sauv_stock values (id_sauv,:date_sauv,:heure_sauv)";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
											'date_sauv' =>date("Y/m/d"),
											'heure_sauv' =>date('H:i'),
											));	

//on recupere l'id de la sauvegarde
$id_sauv=$DataBase->lastInsertId();
		// on recupere tous les articles

		$sql = " select id_article, libelle, marque, nbrebte, qtestock, stockfrigo, statut from article";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			// insertion dans la bd
			$insere=0;
			$sql="insert into article_sauv values (:id_sauv,:codeart,:libelle,:marque,:qtestock,:nbrebte,:stockfrigo,:statut)";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
											'id_sauv' =>$id_sauv,
											'codeart' =>$rslt['id_article'],
											'libelle' =>$rslt['libelle'],
											'marque' =>$rslt['marque'],
											'nbrebte' =>$rslt['nbrebte'],
											'qtestock' =>$rslt['qtestock'],
											'stockfrigo' =>$rslt['stockfrigo'],
											'statut' =>$rslt['statut'],
											));	
			
		}
			if($insere==0)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Un probleme est survenu lors de la sauvegarde de  l\'Etat du Stock.');
					history.back();
					</script>
				<?php
				exit();	
			}
			else
			{
			?>
					<script language="javascript" type="text/javascript">
						alert('Sauvegarde Effectuee.');
						history.back();
					</script>
				<?php
				exit();
			}

?>