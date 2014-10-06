function moreSettings(col) {
    $("div#moreSettings span#title").text("Settings for ");
    loadData(col);
    $("div#moreSettings div#buttons").html("<input type=\"button\" value=\"OK\" onclick=\"saveData('" + col + "')\" /><input type=\"button\" value=\"Cancel\" onclick=\"closeMoreSettings()\" />");
    $("div#moreSettings").css({ "top": $(window).scrollTop()+2+"px" });
    $("div#moreSettings").show();   
}

function moreSettingsAjax(row, id, idGrid, url) {
    $("div#moreSettings span#title").text("Settings for " + row);
    loadDataAjax(id, idGrid, url);
    $("div#moreSettings div#buttons").html("<input type=\"button\" value=\"OK\" onclick=\"saveDataAjax('" + id + "','" + idGrid + "', '" + url + "')\" /><input type=\"button\" value=\"Cancel\" onclick=\"closeMoreSettings()\" />");
    $("div#moreSettings").css({ "top": $(window).scrollTop()+2+"px" });
    $("div#moreSettings").show(); 
}

function closeMoreSettings() {
    $("div#moreSettings").hide();
}

function loadDataAjax(id, idGrid, url) {
    $.post(url+'components/com_grid/config/ajax.php', {
        option: "getMoreConfig",
        id: id,
        idGrid: idGrid
    }, function (data) {
        data = data.split("|");
    	i=0;
        $("div#moreSettings table#configArray tr td.secondColumn").each(
        function () {
            row = $(this);
            //alert(row.children().attr("type"));
            if (row.find("select").html() != null) {
                // SELECT
                row.find("select").val(data[0]);
            } else if (row.find("input") != null) {
                // INPUT
            	switch(row.children().attr("type")) {
            	case "radio": {
            		//RADIO
            		if (data[i] == 1) row.find("input[type=radio]")[0].checked = true;
                    else row.find("input[type=radio]")[1].checked = true;
            	} break;
            	case "text": {
            		//TEXT
            		if(row.children().attr('class')=="color") {
            			row.children().val(data[2]);
            			row.children().css({"background-color":"#"+data[2]});
            		}
            	}
            	}
                
            } 
            i++;
        });
    });
}

function loadData(col) {
    data=$("input#moreConfig_"+col).val();
    data = data.split("|");

	i=0;
    $("div#moreSettings table#configArray tr td.secondColumn").each(
    function () {
        row = $(this);

        if (row.find("select").html() != null) {
            // SELECT
            row.find("select").val(data[0]);
        } else if (row.find("input") != null) {
            // INPUT
        	switch(row.children().attr("type")) {
        	case "radio": {
        		//RADIO
        		if (data[i] == 1) row.find("input[type=radio]")[0].checked = true;
                else row.find("input[type=radio]")[1].checked = true;
        	} break;
        	case "text": {
        		//TEXT
        		if(row.children().attr('class')=="color") {
        			row.children().val(data[2]);
        			row.children().css({"background-color":"#"+data[2]});
        		}
        	}
        	}                
        } 
        i++;
    });
}

function saveDataAjax(id, idGrid, url) {
    moreConfig = "";
    $("div#moreSettings table#configArray tr td.secondColumn").each(function () {
        row = $(this);
        if (row.children().attr("type") == "radio") {
            if (row.children()[0].checked) moreConfig += "1";
            else moreConfig += "0";
        } else moreConfig += row.children().val();
        moreConfig += "|";
    });
    moreConfig = moreConfig.substr(0, moreConfig.length - 1);
    

    $.post(url+'components/com_grid/config/ajax.php', {
        option: "saveMoreConfig",
        id: id,
        idGrid: idGrid,
        moreConfig: moreConfig
    }, function (data) {
        if (data == "OK") closeMoreSettings();
        else $("div#moreSettings").html(data);
    });

}

function saveData(col) {
    moreConfig = "";
    $("div#moreSettings table#configArray tr td.secondColumn").each(function () {
        row = $(this);
        console.log(row);
        if (row.children().attr("type") == "radio") {
            if (row.children()[0].checked) moreConfig += "1";
            else moreConfig += "0";
        } else moreConfig += row.children().val();
        moreConfig += "|";
    });
    
    moreConfig = moreConfig.substr(0, moreConfig.length - 1);
    
    $("input#moreConfig_"+col).val(moreConfig);
    closeMoreSettings();

}

function showAlert(data, type) {
	data=data.substring(1);
	$("div#system-message-container").show();
	$("div#system-message-container").html("<dl id=\"system-message\"><dd class=\""+type+ " "+type+"\"><ul><li>"+data+"</li></ul></dd></dl>");
}