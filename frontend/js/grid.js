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

function SimpleAJAXCallback(in_text, obj) {
	div = document.getElementById(obj);
	div.innerHTML = in_text;
	
	// run scripts if there are any
	
//	scripts=div.getElementsByTagName("script");
//	for(var i in scripts){
//		//alert(eval(scripts[i].text));
//		eval(scripts[i].text);
//	}
	
//	window.fireEvent('domready');
	setStatus ("",obj+"-showimg");
}

function sortiraj(radio, select, url1, url2, id) {
  var link=url1+"&o_b="+select.value;
  for(i=0;i<radio.length;i++) {
    if(radio[i].checked) {
      link+="&o_d="+radio[i].value;
      break;
    }
  }
  link+=url2;
  SimpleAJAXCall(link,SimpleAJAXCallback, '', 'data_listings'+id);
}

/*
	TO JE ZA RECORDJX
*/

var http = getHTTPObject();

function getHTTPObject() {
    if (typeof XMLHttpRequest != 'undefined') {
        return new XMLHttpRequest();
    }
    try {
        return new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
            return new ActiveXObject("Microsoft.XMLHTTP");
        } catch (e) {}
    }
    return false;
}

function handleHttpResponseUser() {
  if (http.readyState == 4) {
    resultsuser = http.responseText;
    // evaluate javascript
    scriptTag='(<script.*?>)((\n|\r|.)*?)(<\/script>)';
    if (scripts=resultsuser.match(scriptTag)) {
      // remove slashes
      scripts[2]=scripts[2].replace(/\\/g,"");
      //run scripts
      eval(scripts[2]);
    }
  }
}

function saveRecord(path, table, tabelaA, tid) {
  var tableid = tid.split('|;');
  setStatus('1','recordInfo');
  var tabelaAtributov = tabelaA.split('|;');
  
  //var http = getHTTPObject();
  var tabelaPodatkov = new Array(tabelaAtributov.length-1);
  for(i=0;i<tabelaAtributov.length;i++) {
    if(tabelaAtributov[i]==tableid[0]) tabelaPodatkov[i]=tableid[1];
    else {
      a=document.getElementById(tabelaAtributov[i]);
      tabelaPodatkov[i]=a.value;
    }
  } 
  tabelaP=tabelaPodatkov.join("|;");
  url=path+'?table='+table+'&tabelaA='+tabelaA+'&tabelaP='+tabelaP+'&tid='+tableid[0]+'&tidv='+tableid[1];
  
  http.open('GET', url, true);
  http.onreadystatechange = handleHttpResponseUser;
  http.send(null);
}

function recordInfo(info, status) {
  setStatus('0','recordInfo');
  obj=document.getElementById('recordInfo');
  if(obj) {
    if(status=='1')
      obj.innerHTML=info;
    else obj.innerHTML=null;
  }
}
/*
	KONC RECORDJX
*/

/*
function checkAll(checkname, exby) {
var bgcolor = '$global[row_colora1]';
  for (i = 0; i < checkname.length; i++) {
  checkname[i].checked = exby.checked? true:false
  var cell = document.getElementById('row' + i);
	if (bgcolor == '$global[row_color2]') {
		var bgcolor = '$global[row_color1]';
	} else {
		var bgcolor = '$global[row_color2]';
	}
	if (checkname[i].checked) {
		cell.style.background = '#$global[row_color_selected]';
	} else {
		cell.style.background = '#' + bgcolor;
	}
  }
}
*/

function checktoggle(theId,color, nr) {
if(document.getElementById) {
  //alert(box);
	var cell = document.getElementById(theId); 
	cell.className = 'jxrow'+nr;
    cell.style.background = '#' + color;
}
}

function checktoggle_over(theId,color) {
if(document.getElementById) {
  var cell = document.getElementById(theId);  
  cell.className = 'jxonmouseover';
  cell.style.background = '#' + color;
 
}
}

//Function to set a loading status.
function setStatus (theStatus, theObj){
	obj = document.getElementById(theObj);
	var img_url = document.getElementById('img_url').value;
	if (obj) {

	if (theStatus == 1){
		obj.innerHTML = "<img src=\""+img_url+"/loading.gif\" alt=\"Loading....\" vspace=4 hspace=4 style=\"border:none;margin:0px;\">";
	} else {
		obj.innerHTML = "";
	}

	}
}


function doneloading(theframe,thefile){
	var theloc = "";
	theframe.processajax ("showimg",theloc);
}

var qsParm = new Array();

function qs(serverPage) {

	var query = serverPage;
	var parms = query.split('&');

	for (var i=0; i<parms.length; i++) {

		var pos = parms[i].indexOf('=');

		if (pos > 0) {

			var key = parms[i].substring(0,pos);
			var val = parms[i].substring(pos+1);
			qsParm[key] = val;

		}
	}
}

function searchjx(grid_url, id){
	var sf; 
	var ds=""; 
	var rpp;
	var aso;
	var url=grid_url;
	var elmid;
	
	if(document.getElementById('sf'+id)!=null){
		sf = document.getElementById('sf'+id).value;
		sf = encodeURIComponent(sf);
		url+="&s_f="+sf;
	}
	if(document.getElementById('ds'+id)!=null){
		ds = document.getElementById('ds'+id).value;
		ds = encodeURIComponent(ds);
		ds=ds.replace(/'/g, '%27'); //encode single quote
	        /*if(ds.indexOf("<script>")!=-1) alert("script");
        	else */url += "&data_search="+ds;
	}
	else{
		//advanced search
		elements = document.getElementsByName("adf."+id);
		
		var i = 0;
		var empty = true;
		while (elements[i] != undefined){
			elmid=elements[i].id.split(".");
			ds=ds+elmid[0]+"|"+elements[i].value+"|";
			if(elements[i].value!="")empty = false;
			i++;
		} 
		ds = encodeURIComponent(ds);
		ds=ds.replace(/'/g, '%27'); //encode single quote
		url += "&data_search="+ds;
	}
	if(document.getElementById('rpp'+id)!=null){
		rpp = document.getElementById('rpp'+id).value;
		url += "&rpp="+rpp;
	}
	if(document.getElementById('aso'+id)!=null){
		aso = document.getElementById('aso'+id).value;
		url += "&aso="+aso;
	}
	//url+="&ajax=1&tmpl=component";
	url+="&ajax=1"
	//alert(sf+' '+ds+' '+grid_url+' '+id);
 
	
	SimpleAJAXCall(url,SimpleAJAXCallback, '', 'data_listings'+id);
}

