// JavaScript Document

 function verif_form()
 {

  if(document.getElementById('codeart').value=="")
           {
            alert('BV choisir le code de l\'article.');
			document.getElementById('codeart').focus();
			return false;
           }	
		   
 if(isNaN(document.getElementById('qterecu').value)||(document.getElementById('qterecu').value=="")||(document.getElementById('qterecu').value==0))
           {
            alert('Le quantite recu est un numérique et diff de zero!');
			document.getElementById('qterecu').focus();
			return false;
           }
 	return true;
 }
