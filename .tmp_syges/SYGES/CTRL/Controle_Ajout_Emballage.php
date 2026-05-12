<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codeappro'])&& (isset($_POST['emb'])))
	{
		// ON VERIFIE  SI L'APPRO EST VALIDEE
		$sql2='SELECT STATUT FROM APPROEMB WHERE ID_APPRO="'.$_POST['codeappro'].'" ' ;
		$reponse2= $DataBase->query($sql2);
		while($rslt2= $reponse2->fetch())
		{ 
			$statut=$rslt2['STATUT'];
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

			// on verifie si cet emballage est deja dans cet appro  
			$Codeappro=htmlentities(htmlspecialchars(strtolower($_POST["codeappro"])), ENT_QUOTES, 'UTF-8');
			$CodeEmb=htmlentities(htmlspecialchars(strtolower($_POST["emb"])), ENT_QUOTES, 'UTF-8');
			$trve= false;
			$sql = " select id_appro, id_emballage from emballage_recu where id_appro='".$Codeappro."' and id_emballage='".$CodeEmb."'";
			$reponse= $DataBase->query($sql);
			while($rslt= $reponse->fetch())
			{
			$trve=true;
			 }
			if($trve==true)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cet  emballage existe deja dans cet approvisionnement ');
					history.back();
					</script>
				<?php
				exit();	
			}
			else
			{
	
				// insertion de l'appro dans la bd
				$insere=0;
				$sql="insert into emballage_recu values (:codeappro,:emb,:qterecu)";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array(
												'codeappro' =>$_POST['codeappro'],
												'emb' =>$_POST['emb'],
												'qterecu' =>$_POST['qterecu'],
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