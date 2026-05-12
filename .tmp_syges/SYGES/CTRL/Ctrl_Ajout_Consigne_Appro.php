<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codeappro'])&& (isset($_POST['Emb'])))
	{

			//on recupere le prix de consigne de l'emballage
			$sql2 = " select mt_consigne from emballage where id_emballage='".$_POST["Emb"]."' ";
			$reponse2= $DataBase->query($sql2);
			while($rslt2= $reponse2->fetch())
			{
					$pu=$rslt2["mt_consigne"];
			}
			// insertion de la consigne dans la bd
			$insere6=0;
			$statut='Consigne';
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
				//on augmente le stock de cet emballage
				$sql7= " select qtestock,qte from emballage where id_emballage='".$_POST['Emb']."'";
				$reponse7= $DataBase->query($sql7);
				while($rslt7= $reponse7->fetch())
				{
						$ST=$rslt7['qtestock'];
						$Q=$rslt7['qte'];
				}
				$insere7=0;
				$sql4="update emballage set qtestock=:qtestock, qte=:qte where id_emballage='".$_POST['Emb']."'";
				$req = $DataBase->prepare($sql4);
				$insere7 = $req->execute(array(
											'qtestock' =>$ST+$_POST['qte'],
											'qte' =>$Q+$_POST['qte']
											));	
				if($insere7==0)
				{
				?>
				<script language="javascript" type="text/javascript">
						alert('Echec de la mise à jour des stocks de l\'emballage.');
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