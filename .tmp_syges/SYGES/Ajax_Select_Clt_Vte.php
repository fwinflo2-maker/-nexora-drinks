<?php
	include("Connexion.php");
?>
    <td colspan="2" align="right"><select name="codeclient" id="codeclient" style="width:200px;">
     <?php
	 $sql = " select id_client ,nom from client  where nom like '%".$_POST['nomclt']."%' and statut='Actif' order by nom  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_client"]."' >";
		 echo $rslt["nom"];
		 echo '</option>';
		 }
		 ?>
    </select> </td>

