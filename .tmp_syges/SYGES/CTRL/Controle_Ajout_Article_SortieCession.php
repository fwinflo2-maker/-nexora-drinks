<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codecession'])&& (isset($_POST['codeart'])))
	{
		//on verifie le statut de la cession 
		$sql5 = " select statut from sortie_stock_cession where id_sortiestock='".$_POST["codecession"]."'";
		$reponse5= $DataBase->query($sql5);
		$rslt5= $reponse5->fetch();
		if ($rslt5["statut"]=='V')
		{
			?>
				<script language="javascript" type="text/javascript">
					alert('Cette Cession est déjà validée. ');
					window.location.replace("../index.php?formulaire=Choisir_SortieCession_Mod");
				</script>
			<?php
			exit();	
		}
		
		// on verifie si ce article est deja dans cet cession  
		$Code=htmlentities(htmlspecialchars(strtolower($_POST["codecession"])), ENT_QUOTES, 'UTF-8');
		$Codeart=htmlentities(htmlspecialchars(strtolower($_POST["codeart"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		$sql = " select id_sortiestock, id_article from articlesortie_cession where id_sortiestock='".$Code."' and id_article='".$Codeart."'";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		$trve=true;
		 }
		if($trve==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Cet  article existe deja dans cette cession ');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{

// insertion de l'appro dans la bd
		$insere=0;
		$sql="insert into articlesortie_cession values (:codeart,:codecession,:qtesortie)";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'codecession' =>$_POST['codecession'],
										'codeart' =>$_POST['codeart'],
										'qtesortie' =>$_POST['qtesortie'],
										));	

		
		if($insere==0)
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
	}
?>