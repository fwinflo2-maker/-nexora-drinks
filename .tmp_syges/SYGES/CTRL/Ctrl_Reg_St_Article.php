<?php
include("Connexion.php");
include('../fonctions.php');
session_start();
if(isset($_POST['code']))
{
	// mise à jour dans la bd
	$id=htmlentities(htmlspecialchars(strtolower($_POST["code"])), ENT_QUOTES, 'UTF-8');
	$insere=0;
	$sql="update article set qtestock=:qtestock, stockfrigo=:stockfrigo where id_article='".$id."'";
	$req = $DataBase->prepare($sql);
	$insere = $req->execute(array(
										'qtestock' =>$_POST['stmag'],
										'stockfrigo' =>$_POST['stfrigo'],
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
				$sql="insert into regularisation values (id_regularisation,:date,:heure,:codeemb,:stockmagav,:stockmagap,:stfrav,:stfrigo,:login)";
					$req = $DataBase->prepare($sql);
					$insere2 = $req->execute(array(
												'date' => dateFormatAnglais($date),
												'heure' =>$heure,
												'codeemb' =>$_POST['code'],
												'stockmagav' =>$_POST['stmagav'],
												'stockmagap' =>$_POST['stmag'],
												'stfrav' =>$_POST['stfrav'],
												'stfrigo' =>$_POST['stfrigo'],
												'login' =>$_SESSION['login'],
												));	
				//on recupere l'id de la reg
				$idReg=$DataBase->lastInsertId();	
				
				//ici on enregistre dans la liste des mouvements de stocks
				$operation='REGULARISATION';
				$sql="insert into mouvementar values (id_mouv,:codeoperation,:codeart,:date,:heure,:si,:qte,:sf,:operation,:user,:detenteur,:date_ann)";
				$req = $DataBase->prepare($sql);
				$insere2 = $req->execute(array(
											'codeoperation' =>$idReg,
											'codeart' =>$_POST['code'],
											'date' =>dateFormatAnglais($date),
											'heure' =>date('H:i'),
											'si' =>$_POST['stmagav'],
											'qte' =>($_POST['stmag']-$_POST['stmagav']),
											'sf' =>$_POST['stmag'],
											'operation' =>$operation,
											'user' =>$_SESSION['login'],
											'detenteur' =>'Reg/'.$_SESSION['login'],
											'date_ann' =>date('Y/m/d')
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
				window.location.replace("../index.php?formulaire=Choisir_Ar_Reg");
				</script>
			<?php
			exit();
		}
}
?>