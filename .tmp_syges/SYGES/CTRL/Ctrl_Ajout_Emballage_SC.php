<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codesortie'])&& (isset($_POST['qte'])))
	{
	// ON VERIFIE  SI LA SORTIE EST VALIDEE
		$sql3='SELECT STATUT FROM SORTIE_STOCK_CESSION WHERE ID_SORTIESTOCK="'.$_POST['codesortie'].'" ' ;
		$reponse3= $DataBase->query($sql3);
		while($rslt3= $reponse3->fetch())
		{ 
			$statut=$rslt3['STATUT'];
		}
		if ($statut=='V')
		 {
			?>
				<script language="javascript" type="text/javascript">
				alert('Sortie Cession deja Validée.');
				history.back();
				</script>
			<?php
			exit(); 
		 }
			// on verifie si cet emballage est deja dans cette sortie 
			$trve= false;
			$sql = " select id_sortiestock, id_emballage from emballagesortiecession where id_sortiestock='".$_POST['codesortie']."' and id_emballage='".$_POST['Emb']."'";
			$reponse= $DataBase->query($sql);
			while($rslt= $reponse->fetch())
			{
				$trve=true;
			}
			if($trve==true)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cet  emballage existe deja dans cette cession. ');
					history.back();
					</script>
				<?php
				exit();	
			}
			// insertion de emballage dans la bd
			$insere6=0;
			$sql6="insert into emballagesortiecession values (id_consigne,:codecession,:codeemballage,:qte)";
			$req = $DataBase->prepare($sql6);
			$insere6 = $req->execute(array(
											'codecession' =>$_POST['codesortie'],
											'codeemballage' =>$_POST['Emb'],
											'qte' =>$_POST['qte']
											));		
		
			if($insere6==0)
			{
				?>
				<script language="javascript" type="text/javascript">
						alert('Echec de l\'enregistrement des emballages.');
						history.back();
				</script>
				<?php
				exit();	
			}
				?>
					<script language="javascript" type="text/javascript">
						/*alert('Ajout effectue');*/
						history.back();
					</script>
				<?php
				exit();
			
	
	}
	else
	{
				?>
					<script language="javascript" type="text/javascript">
						alert('BV remplir les champs vides.');
						history.back();
					</script>
				<?php
				exit();
	}
?>