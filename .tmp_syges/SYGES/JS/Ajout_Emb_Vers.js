// JavaScript Document

 function verif_form()
 {
  if(document.getElementById('num_vers').value=="")
           {
            alert('BV definir le numero du versement.');
			document.getElementById('num_vers').focus();
			return false;
           }
  if(document.getElementById('EMB').value=="")
           {
            alert('BV definir l\'emballage.');
			document.getElementById('EMB').focus();
			return false;
           }
 if(isNaN(document.getElementById('qte').value)||(document.getElementById('qte').value=="")||(document.getElementById('qte').value==0))
           {
            alert('Le quantite est un numérique et diff de zero!');
			document.getElementById('qte').focus();
			return false;
           }
 	return true;
 }
