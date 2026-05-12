<?php
	include("Connexion.php");
	include("../fonctions.php");
	if(isset($_POST['codevente'])&& (isset($_POST['codeart'])))
	{
		// on verifie si cette vente n'est pas validée  
			$Codevente=htmlentities(htmlspecialchars(strtolower($_POST["codevente"])), ENT_QUOTES, 'UTF-8');
			$Codeart=htmlentities(htmlspecialchars(strtolower($_POST["codeart"])), ENT_QUOTES, 'UTF-8');
			$sql5 = " select statut from sortie_stock_frigo where id_sortiestock='".$Codevente."'";
			$reponse5= $DataBase->query($sql5);
			$rslt5= $reponse5->fetch();
			if ($rslt5["statut"]=='V')
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cette vente est déjà validée. ');
					window.location.replace("../index.php?formulaire=Choisir_Vente_Frigo_Mod");
					</script>
				<?php
				exit();	
			}
		
		
			// on verifie si cet article est deja dans cette vente  
			$trve= false;
			$sql = " select id_sortiestock, id_article from articlevendu_frigo where id_sortiestock='".$Codevente."' and id_article='".$Codeart."'";
			$reponse= $DataBase->query($sql);
			while($rslt= $reponse->fetch())
			{
			$trve=true;
			 }
			if($trve==true)
			{
				?>
					<script language="javascript" type="text/javascript">
					alert('Cet  article existe deja dans cette vente. ');
					history.back();
					</script>
				<?php
				exit();	
			}
			else
			{
				//on recupere le prix de revient et au detail de vente de l'article
				$sql = " select prixdetail,prixrevient, nbrebte from article where id_article='".$_POST["codeart"]."' ";
				$reponse= $DataBase->query($sql);
				while($rslt2= $reponse->fetch())
				{
					$prixdetail=$rslt2["prixdetail"];
					$prixrevient=$rslt2["prixrevient"]/$rslt2["nbrebte"];
				 }

				// insertion de l'article dans la bd
				$insere2=0;
				$observation="RAS";
				$sql="insert into articlevendu_frigo values (:codeart,:codevente,:qtevendu,:prixrevient,:prixvente,:observationvente)";
				$req = $DataBase->prepare($sql);
				$insere2 = $req->execute(array(
												'codeart' =>$_POST['codeart'],
												'codevente' =>$_POST['codevente'],
												'qtevendu' =>$_POST['qtevendu'],
												'prixrevient' =>$_POST['qtevendu']*$prixrevient,
												'prixvente' =>$_POST['qtevendu']*$prixdetail,
												'observationvente' =>$observation="RAS",
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
	}
?>