<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codevente']))
	{
		//on se rassure de ce que la vente n'est pas validée 
		// on recupere aussi le code client actuel de cette vente
			$sql5 = " select statut,id_client from sortie_stock where id_sortiestock='".$_POST["codevente"]."'";
			$reponse5= $DataBase->query($sql5);
			$rslt5= $reponse5->fetch();
			if ($rslt5["statut"]=='V')
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cette vente est déjà validée. ');
					window.location.replace("../index.php?formulaire=Choisir_Vente_Mod");
					</script>
				<?php
				exit();	
			}
		//on verifie si le client est différent 
		if ($_POST['codeclient']!=$rslt5['id_client'])
		{
			//On recupere les categories des deux clients et on compare
			//Nouveau client
			$sql4 = " select id_categorie from client where id_client='".$_POST['codeclient']."'";
			$reponse4= $DataBase->query($sql4);
			$rslt4= $reponse4->fetch();
			//ancien client
			$sql3 = " select id_categorie from client where id_client='".$rslt5['id_client']."'";
			$reponse3= $DataBase->query($sql3);
			$rslt3= $reponse3->fetch();
			if ($rslt4['id_categorie']!=$rslt3['id_categorie'])
			{
				//on recupere tous les articles de la vente
				$sql6 = " select id_article, qtesortie from articlevendu where id_sortiestock='".$_POST['codevente']."'";
				$reponse6= $DataBase->query($sql6);
				while ($rslt6= $reponse6->fetch())
				{
					//on recupere le prix de revient et de vente de l'article 
					$trve1=false;
					$sql = " select t.prixvente,a.prixrevient from article a, tarifaire t where a.id_article=t.id_article and t.id_categorie='".$rslt4['id_categorie']."' and t.id_article='".$rslt6['id_article']."' ";
					$reponse= $DataBase->query($sql);
					while($rslt2= $reponse->fetch())
					{
						$PV=$rslt2["prixvente"];
						$PR=$rslt2["prixrevient"];
						$trve1=true;
		 			}
					if($trve1==false)
					{
						?>
						<script language="javascript" type="text/javascript">
							alert('Un Article  n\'est pas dans le tarifaire de la Categorie a laquelle appartient ce Client. Il sera supprime de la vente.');
						</script>
						<?php
						//suppression de l'article
						$sql="delete from articlevendu where id_article='".$rslt6['id_article']."' and  id_sortiestock='".$_POST["codevente"]."'";
        				$req = $DataBase->prepare($sql);
						$insere = $req->execute();
					}
					else
					{
						//mise a jour des nouveaux PR et PV
						$insere=0;
						$sql="update articlevendu set prixrevient=:prixrevient, prixvente=:prixvente where id_article='".$rslt6['id_article']."' and  id_sortiestock='".$_POST["codevente"]."'";
						$req = $DataBase->prepare($sql);
						$insere2 = $req->execute(array(
										'prixrevient' =>$PR * $rslt6['qtesortie'],
										'prixvente' =>$PV *  $rslt6['qtesortie']
										 ));	
						if($insere2==0)
						{
							?>
								<script language="javascript" type="text/javascript">
								alert('Echec de la mise à jour des prix des articles.');
								history.back();
								</script>
							<?php
							exit();	
						}
					}
				}
			}
			
		}
		//Ici on recupere la categorie du client
		$sql7 = " select id_categorie from client where id_client='".$_POST['codeclient']."'";
		$reponse7= $DataBase->query($sql7);
		while ($rslt7= $reponse7->fetch())
		{
			$categorie=$rslt7['id_categorie'];
		}
		   //ici on recupere la Ret Fisc Pro et la TVA
		 $sql8='SELECT  * FROM CATEGORIE WHERE ID_CATEGORIE="'.$categorie.'" ' ;
		 $reponse8= $DataBase->query($sql8);
		 while($rslt8= $reponse8->fetch())
				{
					
					$tauxretfiscpro=$rslt8['TAUXRETFISCPRO'];
					$tva=$rslt8['TAUXTVA'];
				}
		// mise à jour de la vente dans la bd
		$insere=0;
		$sql="update sortie_stock set datesortiestock=:date_vente, observation=:observationsortie ,id_client=:id_client , login=:login,creditristourne=:creditristourne,heuresortiestock=:heuresortiestock,tauxretfiscpro=:tauxretfiscpro,tauxtva=:tauxtva where id_sortiestock='".$_POST["codevente"]."'";
		$req = $DataBase->prepare($sql);
		$insere = $req->execute(array(
										'date_vente' =>  dateFormatAnglais($_POST['date_vente']),
										'observationsortie' =>$_POST['observationsortie'],
										'login' =>$_SESSION['login'],
										'id_client' =>$_POST['codeclient'],
										'heuresortiestock' =>date('H:i'),
										'creditristourne' =>$_POST['ristourne'],
										'tauxretfiscpro' =>$tauxretfiscpro,
										'tauxtva' =>$tva
										 ));	
		
		
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
				alert('Modification effectue');
				window.location.replace("../index.php?formulaire=Modification_Vente&Clt=<?php echo $_POST["codeclient"]; ?>&Vte=<?php echo $_POST["codevente"]; ?>");
				</script>
			<?php
			exit();
		}
}
?>
	