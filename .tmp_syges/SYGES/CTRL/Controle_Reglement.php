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
		while($rslt5= $reponse->fetch())
		{
			$tr=true;
		 }
		 if ($tr==true)
		 {
			 ?>
				<script language="javascript" type="text/javascript">
				alert('La Vente est deja dans la liste des Reglements Suivis.');
				history.back();
				</script>
			<?php
			exit();	
		 }
		
		//on recupere le montant totale de la vente
			$MTVENTE=0;
			$sql2='SELECT MTFACTURE FROM SORTIE_STOCK  WHERE ID_SORTIESTOCK="'.$_POST['codevente'].'" ' ;
			$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
				$MTVENTE=$rslt2['MTFACTURE'];
			}
			//on verifie l'avance
			if ($_POST['avance']>$MTVENTE)
			{
				?>
				<script language="javascript" type="text/javascript">
					alert('L\'Avance est superieure au montant de la facture.');
					history.back();
				</script>
				<?php
				exit();	
			}
			if ($_POST['avance']==$MTVENTE)
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
											'montant' =>$MTVENTE,
											'date' =>dateFormatAnglais($_POST['date']),
											'mtrestant' =>$MTVENTE-$_POST['avance'],
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
				/*alert('enregistrement effectue.');*/
				window.location.replace("../index.php?formulaire=Choisir_Vente_Reglement");
				</script>
			<?php
			exit();
   }
?>