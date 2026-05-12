<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codevente'])&& isset($_POST['avance']))
	{
		//on verifie si cette vente est ds les reglements
		$tr=false;
		$sql = " select id_sortiestock from reglement where id_sortiestock='".$_POST["codevente"]."' ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
			$tr=true;
		 }
		 if ($tr==false)
		 {
			 ?>
				<script language="javascript" type="text/javascript">
				alert('La Vente n\'est pas encore dans la liste des ventes à Reglements Multiples. BV Cliquer sur Nouveau');
				history.back();
				</script>
			<?php
			exit();	
		 }
		
		//on recupere le mt restant dans le dernier paiement
		$reste=0;
		$sql5='SELECT MAX(ID_REGLEMENT) AS ID FROM REGLEMENT WHERE ID_SORTIESTOCK="'.$_POST["codevente"].'" ' ;
		$reponse5= $DataBase->query($sql5);
		while($rslt5= $reponse5->fetch())
			{
				$sql6='SELECT MTRESTANT, MONTANT FROM REGLEMENT WHERE ID_REGLEMENT="'.$rslt5['ID'].'" ' ;
				$reponse6= $DataBase->query($sql6);
				while($rslt6= $reponse6->fetch())
				{
					$reste=$rslt6['MTRESTANT'];
					$montant=$rslt6['MONTANT'];
				}
			}
		
			//on verifie l'avance
			if ($_POST['avance']>$reste)
			{
				?>
				<script language="javascript" type="text/javascript">
					alert('L\'Avance est superieure au montant restant a payer.');
					history.back();
				</script>
				<?php
				exit();	
			}
			if (($_POST['avance']-$reste)==0)
			{
				$statut="Paye";
			}
			else
			{
				$statut='Avance';
			}
			// insertion de la vente dans les reglements
			$insere=0;
			$sql="insert into reglement values (id_reglement,:codevente,:montant,:date,:mtrestant,:mtavance,:statut,:user)";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
											'codevente' =>$_POST['codevente'],
											'montant' =>$montant,
											'date' =>dateFormatAnglais($_POST['date']),
											'mtrestant' =>$reste-$_POST['avance'],
											'mtavance' =>$_POST['avance'],
											'statut' =>$statut,
											'user' =>$_SESSION['login']
											));	
			if($insere==0)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Echec de l\'enregistrement du Reglement.');
					history.back();
					</script>
				<?php
				exit();	
			}
			?>
				<script language="javascript" type="text/javascript">
				alert('enregistrement effectue avec succes.');
				window.location.replace("../index.php?formulaire=Choisir_Vente_Reglement");
				</script>
			<?php
			exit();
   }
?>