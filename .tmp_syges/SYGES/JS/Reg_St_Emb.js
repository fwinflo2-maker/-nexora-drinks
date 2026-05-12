// JavaScript Document

 function verif_form()
 {

 if(isNaN(document.getElementById('stt').value)||(document.getElementById('stt').value==""))
           {
            alert('La quantité totale est un numérique!');
			document.getElementById('stt').focus();
			return false;
           }
 if(isNaN(document.getElementById('qtest').value)||(document.getElementById('qtest').value==""))
           {
            alert('La quantité en stock est un numérique!');
			document.getElementById('qtest').focus();
			return false;
           }
	return true;
 }
