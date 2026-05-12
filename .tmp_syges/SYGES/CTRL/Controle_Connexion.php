<?php 
	session_start();
	include("Connexion.php");
	if(isset($_POST['Login'])&& (isset($_POST['Password'])))
	{
		
		// on verifie si ce Compte figure deja dans la bd
		$login=htmlentities(htmlspecialchars(strtolower($_POST["Login"])), ENT_QUOTES, 'UTF-8');
		$MDP=$_POST["Password"];
		$trve= false;
		$slt= "EXTRA";
		$md=crypt($MDP,$slt);
		$sql = "select login, mdp, habilitation, statut from user where login = '".$login."' and mdp = '".$md."'";
		$reponse= $DataBase->query($sql);
		$rslt= $reponse->fetch();
		if ($rslt== "")
			{
				?>
				<script language="javascript" type="text/javascript">
				alert('Login et/ou Mot de passe incorrect.');
				history.back();
				</script>
			<?php
			}
		
		else
		
			{
				if ($rslt['statut']=='Bloqué')
				{
					?>
						<script language="javascript" type="text/javascript">
                        alert('Votre Compte est Bloque. BV saisir l\'administrateur.');
                        history.back();
                        </script>
					<?php
				}
				else
				{
					$_SESSION['habilitation']=$rslt['habilitation'];
					$_SESSION['login']=$rslt['login'];
					?>
					<script language="javascript" type="text/javascript">
					window.location.replace("../index.php?formulaire=Accueil");
					</script>	
					<?php
				}
			}
	}
?>
