/**
 * Copyright (c) 2009 Gomilsek-informatika. All rights reserved. This program
 * and the accompanying materials are made available under the terms of the GNU
 * Public License v2.0 which accompanies this distribution, and is available at
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * 
 * Contributors: Gomilsek-informatika (initial API and implementation) Contact:
 * customers@toolsjx.com
 */

// window.addEvent('load', function(){initForm();});
// TODO init form potrebno prepisati v php. Zaradi ajaxa se ne izvede.
// function initForm(){
// enableConn();
// enableNrPages();
// disableTableSettings();
// moveWhereCond();
// enableSecOrder();
// showLinksTable();
// }
function enableConn() {
	var joomlaRadio = document.getElementById("joomla");
	if (joomlaRadio.checked) {
		document.getElementById("dbType").disabled = true;
		document.getElementById("dbHost").disabled = true;
		document.getElementById("dbUser").disabled = true;
		document.getElementById("dbPass").disabled = true;
		document.getElementById("dbName").disabled = true;
	} else {
		document.getElementById("dbType").disabled = false;
		document.getElementById("dbHost").disabled = false;
		document.getElementById("dbUser").disabled = false;
		document.getElementById("dbPass").disabled = false;
		document.getElementById("dbName").disabled = false;
	}
}
/*
 * function enableNrPages(){ var radio = document.getElementById("paging"); if
 * (radio.checked){ document.getElementById("nrPages").disabled=false; } else{
 * document.getElementById("nrPages").disabled=true; } }
 */
function enableSecOrder() {
	var list = document.getElementById("secOrder");
	if (list.selectedIndex == 0) {
		document.getElementById("sod1").disabled = true;
		document.getElementById("sod2").disabled = true;
		document.getElementById("sor1").disabled = true;
		document.getElementById("sor2").disabled = true;
	} else {
		document.getElementById("sod1").disabled = false;
		document.getElementById("sod2").disabled = false;
		document.getElementById("sor1").disabled = false;
		document.getElementById("sor2").disabled = false;
	}
}

/*
 * function disableTableSettings(){
 * if(document.getElementById("selectTable").value == "null"){
 * document.getElementById("nrRows").disabled=true;
 * document.getElementById("paging").disabled=true;
 * document.getElementById("paging2").disabled=true;
 * document.getElementById("nrPages").disabled=true;
 * document.getElementById("sortField").disabled=true;
 * document.getElementById("sd1").disabled=true;
 * document.getElementById("sd2").disabled=true; } else{
 * document.getElementById("opt").disabled=true; } }
 */

function selectAll(select, listId) {
	var options = new Array();
	options = document.getElementById(listId).options;
	var i = 0;
	while (options[i] != undefined) {
		options[i].selected = select;
		i++;
	}

}

//Ker druga�e ni delalo, zgleda da obstaja �e fukncija z imenom checkAll
function checkAllCB(cbx, cbxArrayName) {
	var check;
	var cbxs = document.getElementsByName(cbxArrayName);

	check = cbx.checked;

	var i = 0;
	while (cbxs[i] != undefined) {
		cbxs[i].checked = check;
		i++;
	}
}

function addCol(jxtype) {
	var options = new Array();
	var list = document.getElementById("selectColList");
	options = list.options;
	var table = document.getElementById("colTable");
	var table2=null;
	//table2 je neka tabela filtrov, katera pa se za graph ne uporablja
	if (jxtype!="graph") table2 = document.getElementById("fltrTable");
	var row;
	var i = 0;
	while (options[i] != undefined) {
		if (options[i].selected == true) {
			var order = table.rows.length - 1;
			order = order + 1;
			column = options[i].value;
			row = table.insertRow(table.rows.length);
			row = makeRow(row, column, order, jxtype);
			if(table2!=null) {
				row2 = table2.insertRow(table2.rows.length);
				row2 = makeFltrRow(row2, column, order);
			}
		}
		i++;
	}
	if (jxtype!="graph" && jxtype!="record") makeItNice(table);
	i = options.length;
	while (i > 0) {
		i--;
		if (options[i].selected == true) {
			list.remove(i);
		}
	}
}

function makeRow(row, column, order, jxtype) {
	cell0 = row.insertCell(0);
	cell1 = row.insertCell(1);
	cell2 = row.insertCell(2);
	cell3 = row.insertCell(3);
	cell0.innerHTML = "<input type=\"checkbox\" name=\"selectColCbx[]\" value=\""
			+ column
			+ "\" />"
			+ "<input type=\"hidden\" name=\"selectCol[]\" value=\""
			+ column
			+ "\" />";
	cell1.innerHTML = column;
	cell2.innerHTML = "<input class=\"text_area\" type=\"text\" name=\"colMap["
			+ column + "]\" size=\"32\" maxlength=\"250\" value=\"" + column
			+ "\" />";
	if (jxtype == "table" || jxtype == "card") {
		cell4 = row.insertCell(4);
		cell5 = row.insertCell(5);
		cell3.innerHTML = "<input type=\"hidden\" name=\"linkTypes["
				+ column
				+ "]\" value=\"0\">"
				+ "<input type=\"hidden\" name=\"linkMap["
				+ column
				+ "]\" value=\"0\">"
				+ "<input type=\"checkbox\" name=\"linkCbx\" onclick=\"linkCbxChange(this, '"
				+ column + "')\" />";

		cell4.className = "order";
		cell5.innerHTML = "<input class=\"text_area\" style=\"text-align:right;\" type=\"text\" name=\"columnWidth["
				+ column + "]\" size=\"3\" maxlength=\"3\"/>";
	}
	if (jxtype == "card") {
		cell6 = row.insertCell(6);
		cell6.innerHTML = "<input type=\"checkbox\" value=\"1\" name=\"showLabelCbx["
				+ column + "]\" />"
	}
	if (jxtype == "graph") {
		cell4 = row.insertCell(4);
		cell3.innerHTML="<input class=\"useForX\" type=\"checkbox\" name=\"useForX\" value=\""+column+"\"/>";
		cell4.innerHTML="<a onClick=\"moreSettings(event, '"+column.replace(/ /g, '_')+"')\">More ...</a>"+
		"<input type=\"hidden\" id=\"moreConfig_"+column.replace(/ /g, '_')+"\" name=moreConfig["+column+"] value=\"Line|0|"+((1<<24)*Math.random()|0).toString(16).toUpperCase()+"|0\" />";
		appendEvents();
	}
	if(jxtype == "record") {
		cell3.innerHTML="<input type=\"checkbox\" name=\"edit["+column+"]\" />";
	}
	return row;
}

function makeItNice(table) {
	tableId = table.id;
	for (i = 1; i < table.rows.length; i++) {
		switch (i) {
		case 1: {
			if (table.rows.length > 2) {
				table.rows[i].cells[4].innerHTML = "<span>&nbsp;</span>"
						+ "<span><a class ='jgrid' href=\"javascript:move('"
						+ tableId
						+ "', 'down',"
						+ i
						+ ")\" title=\"Move Down\">  <span class='state downarrow'><span class='text'>Move Down</span></span></a></span>"
						+ "<input name=\""
						+ tableId
						+ "Order[]\" size=\"5\" value=\""
						+ i
						+ "\" class=\"text_area\" style=\"text-align: center;\" type=\"text\">";
			} else {
				table.rows[i].cells[4].innerHTML = "<span>&nbsp;</span>"
						+ "<span>&nbsp;</span>"
						+ "<input name=\""
						+ tableId
						+ "Order[]\" size=\"5\" value=\""
						+ i
						+ "\" class=\"text_area\" style=\"text-align: center;\" type=\"text\">";
			}
		}
			break;
		case table.rows.length - 1: {
			table.rows[i].cells[4].innerHTML = "<span><a class ='jgrid' href=\"javascript:move('"
					+ tableId
					+ "','up',"
					+ i
					+ ")\" title=\"Move Up\">   <span class='state uparrow'><span class='text'>Move Up</span></span></a></span>"
					+ "<span>&nbsp;</span>"
					+ "<input name=\""
					+ tableId
					+ "Order[]\" size=\"5\" value=\""
					+ i
					+ "\" class=\"text_area\" style=\"text-align: center;\" type=\"text\">";
		}
			break;
		default: {
			table.rows[i].cells[4].innerHTML = "<span><a class ='jgrid' href=\"javascript:move('"
					+ tableId
					+ "','up',"
					+ i
					+ ")\" title=\"Move Up\">   <span class='state uparrow'><span class='text'>Move Up</span></span></a></span></a></span>"
					+ "<span><a class ='jgrid' href=\"javascript:move('"
					+ tableId
					+ "','down',"
					+ i
					+ ")\" title=\"Move Down\">  <span class='state downarrow'><span class='text'>Move Down</span></span></a></span>"
					+ "<input name=\""
					+ tableId
					+ "Order[]\" size=\"5\" value=\""
					+ i
					+ "\" class=\"text_area\" style=\"text-align: center;\" type=\"text\">";
		}

		}
	}
}

function removeCol() {
	var table = document.getElementById("colTable");
	var cbxs = document.getElementsByName("selectColCbx[]");
	var list = document.getElementById("selectColList");
	var option;
	var i;

	for (i = 0; i < cbxs.length; i++) {
		if (cbxs[i].checked == true) {
			option = document.createElement('option');
			option.text = cbxs[i].value;
			option.value = cbxs[i].value;
			try {
				list.add(option, null); // standards compliant
			} catch (ex) {
				x.add(option); // IE only
			}
			//pri grafu ne kli�em tega, ker ne deluje; predvidevam da je nekj v zvezi z linki, kar graf nima
			if (table.rows[0].cells.length != 5 && table.rows[0].cells.length != 4) {
				linkZeileLoeschen(cbxs[i].value);
				removeFltr(cbxs[i].value);
			}
		}
	}

	for (i = cbxs.length - 1; i >= 0; i--) {
		if (cbxs[i].checked == true) {
			table.deleteRow(i + 1);
		}
	}
	if (table.rows[0].cells.length != 5 && table.rows[0].cells.length != 4) makeItNice(table);
}

function move(tableId, direction, order) {
	var table = document.getElementById(tableId);
	// var tmp=document.createElement('tr');
	var row;
	var row2;

	if (direction == 'up') {
		row = table.rows[order];
		row2 = table.rows[order - 1];
		row.parentNode.insertBefore(row, row2);

	} else {
		row = table.rows[order];
		row2 = table.rows[order + 1];
		row.parentNode.insertBefore(row2, row);

	}

	makeItNice(table);
}

function setOrder(tableId) {
	var table = document.getElementById(tableId);
	var txtOrder = document.getElementsByName(tableId + 'Order[]');
	var x;

	var i;
	var j;
	var row;

	for (i = 0; i < txtOrder.length; i++) {
		for (j = 0; j < txtOrder.length; j++) {
			x = trim(txtOrder[j].value);
			if (x == i + 1) {
				row = table.rows[j + 1];
				row.parentNode.appendChild(row);
				break;
			}
		}
	}

	makeItNice(table);
}

function linkCbxChange(cbx, column) {
	// alert('Cbx der Spalte '+column+' hat sich veraendert.'+cbx.checked);
	if (cbx.checked)
		linkZeileHizufuegen(column);
	else
		linkZeileLoeschen(column);
}

function linkZeileHizufuegen(spalte) {
	table = document.getElementById("linksTable");
	if (table == null)
		tabelleBauen();
	zeile = table.insertRow(table.rows.length);
	zeile = zeileBauen(zeile, spalte);
	showLinksTable();
}

function linkZeileLoeschen(column) {
	table = document.getElementById("linksTable");
	index = document.getElementsByName("linkIndex[]");
	for (i = 0; i < index.length; i++) {
		if (column == index[i].value) {
			table.deleteRow(i + 1);
		}
	}
	showLinksTable();
}

function zeileBauen(row, column) {
	cell0 = row.insertCell(0);
	cell1 = row.insertCell(1);
	cell2 = row.insertCell(2);
	cell0.innerHTML = column
			+ "<input type=\"hidden\" name=\"linkIndex[]\" value=\"" + column
			+ "\" />";
	cell1.innerHTML = Spaltenliste(column);
	cell2.innerHTML = "<input name=\"linkTypes[" + column
			+ "]\" value=\"1\" type=\"radio\" checked=\"checked\"> URL "
			+ "<input name=\"linkTypes[" + column
			+ "]\" value=\"2\" type=\"radio\" > Article ID "
			+ "<input name=\"linkTypes[" + column
			+ "]\" value=\"3\" type=\"radio\" > Custom ID: "
			+ "<input name=\"customLink[" + column
			+ "]\" class=\"text_area\" type=\"text\" size=\"60\" value=\"\" >";

	return row;
}

function Spaltenliste(column) {
	list = document.getElementById("sortField");
	ausgang = '<select name="linkMap[' + column + ']">';
	for (i = 0; i < list.options.length; i++) {
		option = list.options[i];
		ausgang += '<option value="' + option.value + '">' + option.text
				+ '</option>';
	}
	ausgang += '</select>';
	return ausgang;
}

function showLinksTable() {
	table = document.getElementById("linksTable");
	row1 = document.getElementById("linksRow1");
	row2 = document.getElementById("linksRow2");
	row3 = document.getElementById("linksRow3");
	if (table.rows.length > 1) {
		row1.className = "showRow";
		row2.className = "showRow";
		row3.className = "showRow";
	} else {
		row1.className = "hide";
		row2.className = "hide";
		row3.className = "hide";
	}
}

function trim(str, chars) {
	return ltrim(rtrim(str, chars), chars);
}

function ltrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}

function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}

function sortNumber(a, b) {
	return a - b;
}

function moveWhereCond() {
	var str = document.getElementById("whereCond2").value;
	while (str.search("<") != -1) {
		str = str.replace("<", "{");
	}
	while (str.search(">") != -1) {
		str = str.replace(">", "}");
	}
	document.getElementById("whereCond").value = str;
}