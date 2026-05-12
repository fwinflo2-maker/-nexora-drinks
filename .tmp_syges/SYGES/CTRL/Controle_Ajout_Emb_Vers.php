<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['num_vers'])&& (isset($_POST['EMB'])))
	{
		
		// on verifie si ce versm n'est pas validée  
		$sql5 = " select statut from versement where num_vers='".$_POST['num_vers']."'";
		$reponse5= $DataBase->query($sql5);
		$rslt5= $reponse5->fetch();
		if ($rslt5["statut"]=='V')
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Ce Versement est déjà valide. ');
				window.location.replace("../index.php?formulaire=Choisir_Vers_Mod");
				</script>
			<?php
			exit();	
		}
			// on verifie si cet emballage est deja dans ce versm  
			$trve= false;
			$sql = " select num_vers, id_emballage from emballage_vers where num_vers='".$_POST['num_vers']."' and id_emballage='".$_POST['EMB']."'";
			$reponse= $DataBase->query($sql);
			while($rslt= $reponse->fetch())
			{
				$trve=true;
			 }
			if($trve==true)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cet  emballage existe deja dans ce versement. ');
					history.back();
					</script>
				<?php
				exit();	
			}

			// insertion de l'emballage dans la bd
			$insere2=0;
			$observation="RAS";
			$sql="insert into emballage_vers values (:num_vers,:emb,:qte)";
			$req = $DataBase->prepare($sql);
			$insere2 = $req->execute(array(
											'num_vers' =>$_POST['num_vers'],
											'emb' =>$_POST['EMB'],
											'qte' =>$_POST['qte']
											));
	
			
			if($insere2==0)
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
?>