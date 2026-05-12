<?php
	include("Connexion.php");
	if(isset($_POST['Code']))
	{
		// on verifie si ce Code figure  dans la bd
		$code=htmlentities(htmlspecialchars(strtolower($_POST["Code"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		 $sql = " select id_charge from charge where id_charge='".$code."' ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		$trve=true;
		 }
		
		if($trve==true)
		{
		// suppression dans la bd
		$insere=0;
		$sql="delete from charge  where id_charge='".$code."'";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute();	
		
		
		if($insere==0)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Echec de la suppression.');
					history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
		?>
				<script language="javascript" type="text/javascript">
				alert('suppression effectue');
				window.location.replace("../index.php?formulaire=Choisir_Charge_Supp");
				</script>
			<?php
			exit();
		}
	}
	else
	{
		?>
			<script language="javascript" type="text/javascript">
			alert('Charge deja supprimee');
			window.location.replace("../index.php?formulaire=Choisir_Charge_Supp");
			</script>
		<?php
		exit();
	}
}
?>