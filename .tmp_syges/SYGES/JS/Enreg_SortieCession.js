// JavaScript Document
function verif_date(input)
{
var regex = new RegExp("[/-]");
var date = input.split(regex);
var nbJours = new Array('',31,28,31,30,31,30,31,31,30,31,30,31);
var result = true;

if ( date['2']%4 == 0 && date['2']%100 > 0 || date['2']%400 == 0 )
nbJours['2'] = 29;

if( isNaN(date['2']) )
result=false;

if ( isNaN(date['1']) || date['1'] > 12 || date['1'] < 1 )
result=false;

if ( isNaN(date['0']) || date['0'] > nbJours[Math.round(date['1'])] || date['0'] < 1 )
result=false;

return result;
}
 function verif_form()
 { 
	
  if(document.getElementById('codecession').value=="")
           {
            alert('BV saisir le code de la cession.');
			document.getElementById('codecession').focus();
			return false;
           }
   if(!verif_date(document.getElementById('date').value))
           {
            alert('Date de la cession  incorrecte.');
			document.getElementById('date').focus();
			return false;
           }

  if(document.getElementById('codeart').value=="")
           {
            alert('BV choisir le code de l\'article.');
			document.getElementById('codeart').focus();
			return false;
           }	
		   
 if(isNaN(document.getElementById('qtesortie').value)||(document.getElementById('qtesortie').value=="")||(document.getElementById('qtesortie').value==0))
           {
            alert('Le quantite sortie est un numérique et diff de zero!');
			document.getElementById('qtesortie').focus();
			return false;
           }
if(isNaN(document.getElementById('qtesortie2').value))
           {
            alert('Le quantite article 2 est un numérique diff de zero!');
			document.getElementById('qtesortie2').focus();
			return false;
           }
 if(isNaN(document.getElementById('qtesortie3').value))
           {
            alert('Le quantite article 3 est un numérique diff de zero!');
			document.getElementById('qtesortie3').focus();
			return false;
           }
  if((document.getElementById('codeart').value==document.getElementById('codeart2').value)&&(document.getElementById('qtesortie2').value!=""))
           {
            alert('Article 1 identique a  l\'article 2.');
			document.getElementById('codeart2').focus();
			return false;
           }
  if((document.getElementById('codeart2').value==document.getElementById('codeart3').value)&&(document.getElementById('qtesortie3').value!="")&&(document.getElementById('qtesortie2').value!=""))
           {
            alert('Article 2 identique a  l\'article 3.');
			document.getElementById('codeart3').focus();
			return false;
           }
if((document.getElementById('codeart').value==document.getElementById('codeart3').value)&&(document.getElementById('qtesortie3').value!=""))
           {
            alert('Article 1 identique a  l\'article 3.');
			document.getElementById('codeart3').focus();
			return false;
           }
 	return true;
 }
