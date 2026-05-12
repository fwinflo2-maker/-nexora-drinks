<?php
	    include("Connexion.php");
	    // on verifie si l'ancien mot de passe introduite est correcte
		$login=$_POST["Login"];
		$ANCMDP=$_POST["AncPassword"];
		$slt= "EXTRA";
		$ancmd=crypt($ANCMDP,$slt);
		$sql = "select mdp from user where login = '".$login."'";
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		if ($rslt['mdp']!=$ancmd)
			{
				?>
				<script language="javascript" type="text/javascript">
				alert('Ancien mot de passe incorrect.');
				history.back();
				</script>
			<?php
			}
		else
			{
				// mise à jour dans la bd
				$insere=0;
				$MDP=$_POST['NvoPassword'];
				$md=crypt($MDP,$slt);
				$sql="update user set  mdp=:mdp where login='".$login."'";
				$req = $DataBase->prepare($sql);
				$insere = $req->execute(array('mdp' =>$md));
	
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
						window.location.replace("../index.php?formulaire=Accueil");
						</script>
					<?php
					exit();
				}
			}

		
?>