// JavaScript Document
 function verif_form()
 {
  if(document.getElementById('codeappro').value=="")
           {
            alert('BV saisir le code de l\'appro.');
			document.getElementById('codeappro').focus();
			return false;
           }
  if(document.getElementById('Emb').value=="")
           {
            alert('BV choisir l\'emballage.');
			document.getElementById('Emb').focus();
			return false;
           }	
		   
 if(isNaN(document.getElementById('qte').value)||(document.getElementById('qte').value=="")||(document.getElementById('qte').value==0))
           {
            alert('Le nombre est un numérique et diff de zero!');
			document.getElementById('qte').focus();
			return false;
           }

 	return true;
 }
