<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codeappro'])&& (isset($_POST['Emb'])))
	{
		// ON VERIFIE  SI L'APPRO EST VALIDEE
		$sql3='SELECT STATUT FROM APPROVISIONNEMENT WHERE ID_APPRO="'.$_POST['codeappro'].'" ' ;
		$reponse3= $DataBase->query($sql3);
		while($rslt3= $reponse3->fetch())
		{ 
			$statut=$rslt3['STATUT'];
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
			// on verifie si cet emballage est deja dans cet appro 
			$trve= false;
			$sql = " select id_appro, id_emballage from consigneapp where id_appro='".$_POST['codeappro']."' and id_emballage='".$_POST['Emb']."'";
			$reponse= $DataBase->query($sql);
			while($rslt= $reponse->fetch())
			{
				$trve=true;
			}
			if($trve==true)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cet  emballage existe deja dans cet appro. ');
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
			// insertion de la consigne dans la bd
			$insere6=0;
			$statut='NV';
			$sql6="insert into consigneapp values (id_consigne,:codeappro,:codeemballage,:qte,:mt,:pu,:date_consigne,:date_deconsigne,:obs_deconsigne,:statut)";
			$req = $DataBase->prepare($sql6);
			$insere6 = $req->execute(array(
											'codeappro' =>$_POST['codeappro'],
											'codeemballage' =>$_POST['Emb'],
											'qte' =>$_POST['qte'],
											'mt' =>$_POST['qte']*$pu,
											'pu' =>$pu,
											'date_consigne' =>dateFormatAnglais($_POST['Date']),
											'date_deconsigne' =>date("0000-00-00"),
											'obs_deconsigne' =>'',
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