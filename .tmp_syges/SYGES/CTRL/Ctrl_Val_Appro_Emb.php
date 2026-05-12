<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codeappro']))
	{
		//ici on verifie si l'appro est deja  valide
		$valider=false;
		$sql4=" select statut from approemb where id_appro='".$_POST['codeappro']."'";
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
				alert('Appro deja pris en compte.');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
		// mise à jour dans la bd
		$id=htmlentities(htmlspecialchars(strtolower($_POST["codeappro"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$statut='V';
		$sql="update approemb set statut=:statut where id_appro='".$id."'";
		$req = $DataBase->prepare($sql);
		$insere = $req->execute(array(
										'statut' => $statut,
										 ));	
		//ici on recupere tous les emballages de l'appro 
		$sql2=" select E.id_emballage, E.qte, E.qtestock, ER.id_emballage, ER.qterecu, ER.id_appro from emballage E, emballage_recu ER where ER.id_appro='".$id."' and E.id_emballage=ER.id_emballage";
		$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{
			//ici pour chaque enregistrement on fait la mise a jour du stock
			$sql="update emballage set qtestock=:qtestock, qte=:qte where id_emballage='".$rslt2["id_emballage"]."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'qtestock' => $rslt2['qterecu']+$rslt2['qtestock'],
										'qte' => $rslt2['qterecu']+$rslt2['qte'],
										 ));	
			//ici on enregistre dans la liste des mouvements de stocks
			$operation='APPRO';
			$sql="insert into mouvementemb values (id_mouv,:codeoperation,:codeemb,:date,:heure,:qte,:siqte,:sfqte,:sistock,:sfstock,:operation,:user)";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'codeoperation' =>$_POST['codeappro'],
										'codeemb' =>$rslt2["id_emballage"],
										'date' =>dateFormatAnglais($_POST['date_appro']),
										'heure' =>date('H:i'),
										'qte' =>$rslt2['qterecu'],
										'siqte' =>$rslt2['qte'],
										'sfqte' =>$rslt2['qterecu']+$rslt2['qte'],
										'sistock' =>$rslt2['qtestock'],
										'sfstock' =>$rslt2['qterecu']+$rslt2['qtestock'],
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
					alert('Approvisionnement pris en compte');
					history.back();
				</script>
			<?php
			exit();
		}
}
}
?>
	