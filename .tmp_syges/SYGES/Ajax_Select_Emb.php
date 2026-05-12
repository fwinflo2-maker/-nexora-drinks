<?php
	include("Connexion.php");
?>
    <td colspan="2" align="right"><select name="codeart" id="codeart" style="width:200px;">
     <?php
	 $sql = " select id_emballage ,libelle,qtestock from emballage  where libelle like '%".$_POST['libemb']."%' and statut='Actif' order by libelle  ";
		$reponse= $DataBase->query($sql);
		while($rslt= $reponse->fetch())
		{
		 echo "<option value='".$rslt["id_emballage"]."' >";
		 echo $rslt["libelle"].' (Stock :'.$rslt['qtestock'].')';
		 echo '</option>';
		 }
		 ?>
    </select> </td>

