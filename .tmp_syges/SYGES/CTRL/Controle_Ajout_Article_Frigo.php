<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codeappro'])&& (isset($_POST['codeart'])))
	{
		
		// on verifie si cet appro est deja validé 
		$Codeappro=htmlentities(htmlspecialchars(strtolower($_POST["codeappro"])), ENT_QUOTES, 'UTF-8');
		$Codeart=htmlentities(htmlspecialchars(strtolower($_POST["codeart"])), ENT_QUOTES, 'UTF-8');
		$tr= false;
		$sql1 = " select statut from approfrigo where id_appro='".$Codeappro."'";
		$reponse1= $DataBase->query($sql1);
		while($rslt1= $reponse1->fetch())
		{
			$statut=$rslt1['statut'];
		 }
		if ($statut=='V')
		 {
			?>
				<script language="javascript" type="text/javascript">
				alert('Appro deja Validé.');
				history.back();
				</script>
			<?php
			exit(); 
		 }
		 else
		 {
		 
			// on verifie si ce article est deja dans cet appro
			$trve= false;
			$sql = " select id_appro, id_article from article_recu_frigo where id_appro='".$Codeappro."' and id_article='".$Codeart."'";
			$reponse= $DataBase->query($sql);
			while($rslt= $reponse->fetch())
			{
			$trve=true;
			 }
			if($trve==true)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cet  article existe deja dans cet approvisionnement ');
					history.back();
					</script>
				<?php
				exit();	
			}
			else
			{
				//On recupere le nbre de bouteille de l'article
				$sql = "Select nbrebte from article  where id_article='".$_POST['codeart']."'";
				$reponse= $DataBase->query($sql);
				$rslt3= $reponse->fetch();

				// insertion de l'article dans la bd
				$insere=0;
				$sql="insert into article_recu_frigo values (:codeappro,:codeart,:qterecu,:nbrebte)";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
												'codeappro' =>$_POST['codeappro'],
												'codeart' =>$_POST['codeart'],
												'qterecu' =>$_POST['qterecu'],
												'nbrebte' => ($_POST['qterecu']*$rslt3['nbrebte'])
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
	}
?>