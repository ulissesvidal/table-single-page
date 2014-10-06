jQuery(document).ready(function(){
	jQuery.noConflict();
});
//gid => GridID
//rid => RowID
function editRecord(gid, rid, url, table, PK) {
	record=jQuery("table#"+gid+"_"+rid);
	record.find("div#imageDiv img#edit").hide();
	record.find("td.RecordLabel").attr("class", "RecordLabel-edit");
	record.find("td.RecordData").attr("class", "RecordData-edit");
	record.find("td:last-child").each(function(index, e){
		value=jQuery(e.children[0]);
		editBox=jQuery(e.children[1]);
		editBox.find("input[name=datepicker]").live('click', function(){
			if(!this.readOnly) {
				//Če je v recordu datum, moram mu dati handler, da pokaže koledar ko klikneš na inputBox
				jQuery(this).datepicker({ dateFormat: 'yy-mm-dd', showOn:'focus' }).focus();
			}
		});
		editBox.show();
		value.hide();
	});
	imgSrc=record.find("div#imageDiv img#edit").attr("src");
	imgSrc=imgSrc.substr(0,imgSrc.lastIndexOf("edit.png"));
	record.find("div#imageDiv").append("<img id=\"save\" src=\""+imgSrc+"save.png\" style=\"width:12px;\" " +
			"onclick=\"saveRecord('"+gid+"', '"+rid+"', '"+url+"', '"+table+"', '"+PK+"')\" />"+
			"<img id=\"cancel\" src=\""+imgSrc+"cancel.png\" style=\"width:12px;\" onclick=\"cancelEditing('"+gid+"', '"+rid+"')\" />");
}

function cancelEditing(gid, rid) {
	record=jQuery("table#"+gid+"_"+rid);
	record.find("div#imageDiv img#save").remove();
	record.find("div#imageDiv img#cancel").remove();
	record.find("div#imageDiv img#edit").show();
	record.find("td.RecordLabel-edit").attr("class", "RecordLabel");
	record.find("td.RecordData-edit").attr("class", "RecordData");
	record.find("td:last-child").each(function(index, e){
		value=jQuery(e.children[0]);
		editDiv=jQuery(e.children[1]);
		value.show();
		editDiv.hide();
	});
}

function saveRecord(gid, rid, url, table, PK) {
	record=jQuery("table#"+gid+"_"+rid);
	dbData= new Array();
	record.find("div#imageDiv img#save").remove();
	record.find("div#imageDiv img#cancel").remove();
	record.find("img#edit").show();
	record.find("td.RecordLabel-edit").attr("class", "RecordLabel");
	record.find("td.RecordData-edit").attr("class", "RecordData");
	record.find("td:last-child").each(function(index, e){
		editDiv=jQuery(e.children[1]);
		value=jQuery(e.children[0]);
		value.html(editDiv.children().val());
		value.show();
		editDiv.hide();
		obj=new Object();
		obj.key=editDiv.attr("id");
		obj.val=value.html();
		dbData.push(obj);
	});
	
	//console.log(dbData);
	//Update database values	
	jQuery.post(url, {dbData: dbData, id: rid, table: table, tablePK: PK}, function(data) {
		if(data[0]=="!") {
			console.log(data);
		}
	});
}