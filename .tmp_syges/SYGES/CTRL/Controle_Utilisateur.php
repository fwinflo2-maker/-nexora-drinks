<?php
//session_start();
	require_once('Connexion.php');
	if(isset($_POST['Login']))
	{
		// on verifie si cet utilisateur n'est pas déjà dans la base de données
		$login=htmlentities(htmlspecialchars(strtolower($_POST["Login"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		$sql = " select * from user where login='".$login."'";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		$trve=true;
		 }
		
		if($trve==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Ce Login existe déjà.');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{


		// insertion dans la bd
		$insere=0;
		$statut="Actif";
		$slt="EXTRA";
		$md=crypt($_POST['MDP'],$slt);
		$sql="insert user values  (:login,:nom,:prenom,:mdp,:habilitation,:statut)";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'login'=> $_POST['Login'],
										'nom' => $_POST['nom'],
										'prenom'=> $_POST['prenom'],
										'mdp'=> $md,
										'habilitation'=> $_POST['Habilitation'],
										'statut'=> $statut
									)    );	
		
		if($insere==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de l\'enregistrement');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
		?>
				<script language="javascript" type="text/javascript">
				alert('Enregistrement effectue');
				history.back();
				</script>
			<?php
			exit();
		}
	}
}
?>