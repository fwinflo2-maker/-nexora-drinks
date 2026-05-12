// JavaScript Document

 function verif_form()
 {
	
 if(isNaN(document.getElementById('stmag').value)||(document.getElementById('stmag').value==""))
           {
            alert('Le quantité en stock au magasin est un numérique!');
			document.getElementById('stmag').focus();
			return false;
           }
 if(isNaN(document.getElementById('stfrigo').value)||(document.getElementById('stfrigo').value==""))
           {
            alert('Le quantité en stock au frigo est un numérique!');
			document.getElementById('stfrigo').focus();
			return false;
           }
	return true;
 }
