<?php
	session_start();
	include("Connexion.php");
	include("../fonctions.php");
	if((isset($_POST['codeemb']))&& (isset($_POST['vendeur'])))
	{


		
//		// on verifie si le vendeur a deja une vente à cette date
//		$trve= false;
//		$num_vers=0;
//		$datevers=dateFormatAnglais($_POST['date']);
//		$sql = " select num_vers from versement where vendeur='".$_POST['vendeur']."' and date_vers='".$datevers."' ";
//		$reponse= $DataBase->query($sql);
//		while($rslt= $reponse->fetch())
//		{
//			$trve=true;
//			$num_vers=$rslt['num_vers'];
//		 }
//		if($trve==true)
//		{
//			?>
//				<script language="javascript" type="text/javascript">
//				alert('Ce Vendeur a un Versement enregistre a cette date (Voir Versement :<?php echo $num_vers;?>)');
//				history.back();
//				</script>
//			<?php
//			exit();	
//		}
//		else
//		{

			// insertion du versement dans la bd
			$insere=0;
			$statut="N";
			$sql="insert into versement values (num_vers,:date_vers,:vendeur,:montant,:observation,:user,:date,:heure,:statut)";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
											'date_vers' =>dateFormatAnglais($_POST['date']),
											'vendeur' =>$_POST['vendeur'],
											'montant' =>$_POST['Montant'],
											'observation' =>$_POST['observation'],
											'user' =>$_SESSION['login'],
											'date' =>date('Y-m-d'),
											'heure' =>date('H:i'),
											'statut' =>$statut
											));	
			if($insere==0)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Echec de l\'enregistrement du versement.');
					history.back();
					</script>
				<?php
				exit();	
			}
			//on recupere le code du versm 
			$lastid=$DataBase->lastInsertId();
			
			// insertion de l'emballage 1 dans la bd
			$insere1=0;
			$sql="insert into emballage_vers values (:num_vers,:codeemb,:qte)";
			$req = $DataBase->prepare($sql);
			$insere1 = $req->execute(array(
											'num_vers' =>$lastid,
											'codeemb' =>$_POST['codeemb'],
											'qte' =>$_POST['qte']
											));
			
			if($insere1==0)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Echec de l\'enregistrement de l\'emballage 1.');
					history.back();
					</script>
				<?php
				exit();	
			}
			// insertion de l'emballage 2 dans la bd
			if($_POST['qte2']!="")
			{
				$insere2=0;
				$sql="insert into emballage_vers values (:num_vers,:codeemb,:qte)";
				$req = $DataBase->prepare($sql);
				$insere2 = $req->execute(array(
												'num_vers' =>$lastid,
												'codeemb' =>$_POST['codeemb2'],
												'qte' =>$_POST['qte2']
												));
				
				if($insere2==0)
				{
					?>
						<script language="javascript" type="text/javascript">
						alert('Echec de l\'enregistrement de l\'emballage 2.');
						history.back();
						</script>
					<?php
					exit();	
				}
			}
		// insertion de l'emballage 3 dans la bd
		if($_POST['qte3']!="")
		{
			$insere3=0;
			$sql="insert into emballage_vers values (:num_vers,:codeemb,:qte)";
			$req = $DataBase->prepare($sql);
			$insere3 = $req->execute(array(
											'num_vers' =>$lastid,
											'codeemb' =>$_POST['codeemb3'],
											'qte' =>$_POST['qte3']
											));
			
				if($insere3==0)
				{
					?>
						<script language="javascript" type="text/javascript">
						alert('Echec de l\'enregistrement de l\'emballage 3.');
						history.back();
						</script>
					<?php
					exit();	
				}
		}
		?>
				<script language="javascript" type="text/javascript">
				/*alert('enregistrement effectue.');*/
				window.location.replace("../index.php?formulaire=Modification_Vers&Vers=<?php echo $lastid;?>&VD=<?php echo $_POST['vendeur'];?>");
				</script>
			<?php
			exit();
	}
//}
?>