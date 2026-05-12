<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codevente'])&& (isset($_POST['Emb'])))
	{
		// ON VERIFIE  SI LA VTE EST VALIDEE
		$sql3='SELECT STATUT FROM SORTIE_STOCK WHERE ID_SORTIESTOCK="'.$_POST['codevente'].'" ' ;
		$reponse3= $DataBase->query($sql3);
		while($rslt3= $reponse3->fetch())
		{ 
			$statut=$rslt3['STATUT'];
		}
		if ($statut=='V')
		 {
			?>
				<script language="javascript" type="text/javascript">
				alert('Vente deja Validé.');
				history.back();
				</script>
			<?php
			exit(); 
		 }
			// on verifie si cet emballage est deja dans cette vte 
			$trve= false;
			$sql = " select id_sortiestock, id_emballage from rtrembvte where id_sortiestock='".$_POST['codevente']."' and id_emballage='".$_POST['Emb']."'";
			$reponse= $DataBase->query($sql);
			while($rslt= $reponse->fetch())
			{
				$trve=true;
			}
			if($trve==true)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cet  emballage existe deja dans la liste des deconsignations de cette vente. ');
					history.back();
					</script>
				<?php
				exit();	
			}

			//on recupere le prix de consigne de l'emballage
			$sql2 = " select mt_consigne from emballage where id_emballage='".$_POST["Emb"]."' ";
			$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
				$pu=$rslt2["mt_consigne"];
			}
			// insertion du retour dans la bd
			$insere6=0;
			$statut='NV';
			$sql6="insert into rtrembvte values (id_rtremb,:codeemballage,:codevente,:qte,:mt,:pu,:date_rtremb,:statut)";
			$req = $DataBase->prepare($sql6);
			$insere6 = $req->execute(array(
											
											'codeemballage' =>$_POST['Emb'],
											'codevente' =>$_POST['codevente'],
											'qte' =>$_POST['qte'],
											'mt' =>$_POST['qte']*$pu,
											'pu' =>$pu,
											'date_rtremb' =>dateFormatAnglais($_POST['Date']),
											'statut' =>$statut
											));
			
			if($insere6==0)
			{
				?>
				<script language="javascript" type="text/javascript">
						alert('Echec de l\'enregistrement de la consigne.');
						history.back();
				</script>
				<?php
				exit();	
			}

					?>
					<script language="javascript" type="text/javascript">
						/*alert('Ajout effectue');*/
						history.back();
					</script>
					<?php
					exit();
			}
?>