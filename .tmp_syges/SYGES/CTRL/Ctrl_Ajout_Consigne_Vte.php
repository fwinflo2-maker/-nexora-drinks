<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codevente'])&& (isset($_POST['Emb'])))
	{

			// on verifie si cet emballage est deja dans cette vente  
			$trve= false;
			$sql = " select id_sortiestock, id_emballage from consigne where id_sortiestock='".$_POST['codevente']."' and id_emballage='".$_POST['Emb']."'";
			$reponse= $DataBase->query($sql);
			while($rslt= $reponse->fetch())
			{
				$trve=true;
			}
			if($trve==true)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cet  emballage existe deja dans cette vente. ');
					history.back();
					</script>
				<?php
				exit();	
			}
			else
			{
				//On verifie si la quantité sollicitee est en stock
				$trve3=false;
				$sql3 = " select qtestock from emballage where id_emballage='".$_POST['Emb']."'";
				$reponse3= $DataBase->query($sql3);
				while($rslt3= $reponse3->fetch())
				{
					if ($rslt3['qtestock'] < $_POST['qte'])
					{
						$trve3=true;
					}
				}
				if ($trve3==true)
				{
					?>
						<script language="javascript" type="text/javascript">
							alert('Quantité des emballages disponible en stock inférieur à la demande.');
							history.back();
						</script>
					<?php
					exit();	
				}
			
				else
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
					$statut='NV';
					$sql6="insert into consigne values (:codevente,:codeemballage,:qte,:mt,:pu,:date_consigne,:date_deconsigne,:obs_deconsigne,:statut)";
						$req = $DataBase->prepare($sql6);
						$insere6 = $req->execute(array(
													'codevente' =>$_POST['codevente'],
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
							/*alert('Modification effectue');*/
							history.back();
							</script>
						<?php
						exit();
					}
			}
		}
	}
?>