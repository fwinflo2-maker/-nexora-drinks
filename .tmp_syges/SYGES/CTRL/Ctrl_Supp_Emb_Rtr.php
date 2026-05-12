<?php
	include("Connexion.php");
	include('../fonctions.php');
	if(isset($_POST['codertr']))
	{
		$tr=false;
		$sql4='SELECT * FROM RTREMBFSSR WHERE ID_RTREMB="'.$_POST['codertr'].'" ' ;
		$reponse4= $DataBase->query($sql4);
		while($rslt4= $reponse4->fetch())
		{ 
			$tr=true;
		}
		if ($tr==false)
		{
				?>
				<script language="javascript" type="text/javascript">
					alert('Retour Emb. inexistante.');
					history.back();
				</script>
      			<?php
				exit();
   		}
		else
		{
			// ON VERIFIE  SI L'APPRO EST VALIDEE
			$sql3='SELECT STATUT FROM APPROVISIONNEMENT WHERE ID_APPRO="'.$_POST['codeappro'].'" ' ;
			$reponse3= $DataBase->query($sql3);
			while($rslt3= $reponse3->fetch())
			{ 
				$statut=$rslt3['STATUT'];
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
            $sql='SELECT  ID_EMBALLAGE, QTESTOCK FROM EMBALLAGE WHERE LIBELLE="'.$_POST['emb'].'" ' ;
            $reponse= $DataBase->query($sql);
            while($rslt1= $reponse->fetch())
            {
                $emb = $rslt1['ID_EMBALLAGE']; 
                $st = $rslt1['QTESTOCK'];
            }
    
            // mise à jour dans la bd
        
            $sql="delete from rtrembfssr where id_rtremb='".$_POST['codertr']."'";
            $req = $DataBase->prepare($sql);
            $insere = $req->execute();	
            //
            $sql='SELECT  ID_FOURNISSEUR FROM APPROVISIONNEMENT WHERE ID_APPRO="'.$_POST['codeappro'].'" ' ;
            $reponse= $DataBase->query($sql);
            while($rslt2= $reponse->fetch())
            {
                $Fs = $rslt2['ID_FOURNISSEUR']; 
            }
            ?>
            
                    <script language="javascript" type="text/javascript">
                    alert('Suppression effectue');
                    window.location.replace("../index.php?formulaire=Modification_Appro&Ap=<?php echo $_POST["codeappro"];?>&Fs=<?php echo $Fs;?>");
                    </script>
                <?php
                exit();
        }
		exit;
}
?>