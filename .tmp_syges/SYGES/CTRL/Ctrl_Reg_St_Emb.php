<?php
	include("Connexion.php");
	include('../fonctions.php');
	session_start();
	if(isset($_POST['code']))
	{

		// mise à jour dans la bd
		$id=htmlentities(htmlspecialchars(strtolower($_POST["code"])), ENT_QUOTES, 'UTF-8');
		$insere=0;
		$sql="update emballage set qte=:qte, qtestock=:qtestock where id_emballage='".$id."'";
		$req = $DataBase->prepare($sql);
		$insere = $req->execute(array(
										'qte' =>$_POST['stt'],
										'qtestock' =>$_POST['qtest']
										));	
		
		
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
			
			// insertion de la regul dans la bd
				$insere2=0;
				$date=date('d/m/Y');
				$heure=date('H:i');
				$sql="insert into regularisation_emb values (id_regularisation,:date,:heure,:codeemb,:stockttav,:stockttap,:qtestockav,:qtestockap,:login)";
					$req = $DataBase->prepare($sql);
					$insere2 = $req->execute(array(
												'date' => dateFormatAnglais($date),
												'heure' =>$heure,
												'codeemb' =>$_POST['code'],
												'stockttav' =>$_POST['sttav'],
												'stockttap' =>$_POST['stt'],
												'qtestockav' =>$_POST['qtestav'],
												'qtestockap' =>$_POST['qtest'],
												'login' =>$_SESSION['login'],
												));	
		
				
				if($insere2==0)
				{
					?>
						<script language="javascript" type="text/javascript">
						alert('Echec de l\'enregistrement  de la regularisation.');
						history.back();
						</script>
					<?php
					exit();	
				}
		?>
				<script language="javascript" type="text/javascript">
				alert('Modification effectue');
				window.location.replace("../index.php?formulaire=Choisir_Emb_Reg");
				</script>
			<?php
			exit();
		}
}
?>