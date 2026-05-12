// JavaScript Document
 function verif_form()
 {
  if(document.getElementById('codeappro').value=="")
           {
            alert('BV saisir le code de l\'appro.');
			document.getElementById('codeappro').focus();
			return false;
           }
  if(document.getElementById('emb').value=="")
           {
            alert('BV choisir l\'emballage.');
			document.getElementById('emb').focus();
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
