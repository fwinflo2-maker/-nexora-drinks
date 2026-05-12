<?php
//session_start();
	require_once('Connexion.php');
	include('../fonctions.php');
	if(isset($_POST['Code']))
	{
		// on verifie si cet code charge n'est pas déjà dans la base de données
		$code=htmlentities(htmlspecialchars(strtolower($_POST["Code"])), ENT_QUOTES, 'UTF-8');
		$trve= false;
		$sql = " select * from charge where id_charge='".$code."'";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		$trve=true;
		 }
		
		if($trve==true)
		{
			?>
				<script language="javascript" type="text/javascript">
				alert('Ce code charge existe déjà.');
				history.back();
				</script>
			<?php
			exit();	
		}
		else
		{
		// insertion dans la bd
		$insere=0;
		$statut="N";
		$sql="insert charge values  (:code, :id_typecharge, :montant, :date_charge, :description, :statut)";
			$req = $DataBase->prepare($sql);
			$insere = $req->execute(array(
										'code'=> $_POST['Code'],
										'id_typecharge' => $_POST['TypeCharge'],
										'montant' => $_POST['Montant'],
										'date_charge' => dateFormatAnglais($_POST['Date']),
										'description' => $_POST['Description'],
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