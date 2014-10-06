/**
*Copyright (c) 2009 Gomilsek-informatika.
*All rights reserved. This program and the accompanying materials
*are made available under the terms of the GNU Public License v2.0
*which accompanies this distribution, and is available at
*http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
*Contributors:
*	Gomilsek-informatika  (initial API and implementation)
*Contact:
*	customers@toolsjx.com
 */

//window.addEvent('load', function(){initFltr()});

//function initFltr(){
//	
//}

function showFltr(){
	var radio = document.getElementById("advFltrRadio");
	var block = document.getElementById("advFltrBlok");
	//alert(radio.checked);
	
	if (radio.checked){
		block.className="col100 showBlock";
		
	}
	else{
		block.className="hide";
	}
}

/*
function addFltr(){
	var options = new Array();
	var list = document.getElementById("selectColList");
	options = list.options;
	var table = document.getElementById("fltrTable");
	var row;
	var i = 0;
	while (options[i] != undefined){
		if (options[i].selected == true){
			var order = table.rows.length-1;
			order = order+1;
			column = options[i].value;
			row = table.insertRow(table.rows.length);
			row = makeFltrRow(row, column, order);
					}
		i++;
	}
	makeItNice(table);
	i = options.length;
	while (i > 0 ){
		i--;
		if (options[i].selected == true){
			list.remove(i);
		}
	}	
}
*/
function makeFltrRow(row, column, order){
	cell0 = row.insertCell(0);
	cell1 = row.insertCell(1);
	//cell2 = row.insertCell(2);
	cell3 = row.insertCell(2);
	//cell4 = row.insertCell(4);
	//cell5 = row.insertCell(5);
	cell0.innerHTML="<input type=\"checkbox\" checked=\"checked\" name=\"selectFltrCbx[]\" value=\""+column+ "\" />";
	cell1.innerHTML=column+"<input type=\"hidden\" name=\"filterParams["+order+"_hash]\" value=\""+column+"\" />"+
							"<input type=\"hidden\" name=\"filterOrder[]\" value=\""+order+"\" />";
	//cell2.innerHTML="<input class=\"text_area\" type=\"text\" name=\"filterParams["+order+"_label]\" size=\"32\" maxlength=\"250\" value=\""+column+ "\" />";
	cell3.innerHTML="<select name=\"filterParams["+order+"_type]\"><option value = \"textbox\">Textbox</option><option value = \"checkbox\">Checkbox</option><option value = \"radio\">Radio</option><option value = \"list\">List</option></select>";
	//cell4.className = "order";
	//cell5.innerHTML = "<a href=\"#\">configure</a>";
	return row;
}

function removeFltr(column){
	var table = document.getElementById("fltrTable");
	//var cbxs = document.getElementsByName("selectFltrCbx[]");
	//var list = document.getElementById("selectFltrList");
	index = document.getElementsByName("fltrIndex[]");
	for(i=0; i<index.length; i++)
	{
		if(column == index[i].value){
			table.deleteRow(i+1);
		}
	}
	//var option; 
	//var i;
	/*for(i = 0; i<cbxs.length; i++ ){
		if (cbxs[i].checked == true){
			option = document.createElement('option');
			option.text=cbxs[i].value;
			option.value=cbxs[i].value;
			try
			  {
			  list.add(option,null); // standards compliant
			  }
			catch(ex)
			  {
			  x.add(option); // IE only
			  }		
		}
	}
	
	for(i = cbxs.length-1; i>=0 ; i-- ){
		if (cbxs[i].checked == true){
			table.deleteRow(i+1);
		}
	}*/
	//makeItNice(table);	
}

