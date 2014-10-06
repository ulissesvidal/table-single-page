var newSelect = new Array();
//var countData = 0;
//var sizeData = 0;
var grjx_cache = [];
var cache_indices = new Array();
//var cache_options;
var selecting = new Array();
var ind_cnt=0;

function drawGraphFix(url, table, captions, moreConfig, graphConfig,
		placeHolder, whereCond, orderBy) {
//	console.log("drawGraphFix");
//	console.log("url="+url);
//	console.log("table="+table);
//	console.log("placeHolder="+placeHolder);
//	console.log("");
	//console.log("captions=".captions);
	cli_ind=ind_cnt;
	ind_cnt=ind_cnt+1;
	selecting[cli_ind]=false;
	newSelect[cli_ind]=true;
	draw(url, table, captions, moreConfig, graphConfig, placeHolder, 0,
			whereCond, orderBy, cli_ind);
} // END drawGraphFix()

function drawGraphDynamic(url, table, captions, moreConfig, graphConfig,
		placeHolder, refresh, whereCond, orderBy) {
//	console.log("drawGraphDynamic");
//	console.log("url="+url);
//	console.log("table="+table);
//	console.log("placeHolder="+placeHolder);
//	console.log("");
	var cli_ind=ind_cnt;
	ind_cnt=ind_cnt+1;
	selecting[cli_ind]=false;
	newSelect[cli_ind]=true;
	var countData=0;
//	console.log("cli_ind="+cli_ind);
	if (grjx_cache[cli_ind] != undefined){
		countData=grjx_cache[cli_ind].countData;
	}
	var step = -1;
	var sizeData = parseInt(JSON.parse(graphConfig).sizeData);
	draw(url, table, captions, moreConfig, graphConfig, placeHolder, step++,
			whereCond, orderBy,cli_ind);
	setInterval(function() {
		//console.log("url="+url);
		if ((step % countData) == 0 && step > 0) {
			newSelect[cli_ind]=true;
			step = 0;
		}
		draw(url, table, captions, moreConfig, graphConfig, placeHolder, step,
				whereCond, orderBy, cli_ind);
		step++;
	}, refresh * 1000);
} // END drawGraphDynamic()

function drawGraphFixIE(url, table, captions, moreConfig, graphConfig,
		placeHolder, whereCond, orderBy) {
	var cli_ind=ind_cnt;
	ind_cnt=ind_cnt+1;
	selecting[cli_ind]=false;
	newSelect[cli_ind]=true;
	draw(url, table, captions, moreConfig, graphConfig, placeHolder, 0,
			whereCond, orderBy, cli_ind);
	setInterval(function() {
		draw(url, table, captions, moreConfig, graphConfig, placeHolder, 0,
				whereCond, orderBy, cli_ind);
	}, 1000);
} // END drawGraphDynamic()

function draw(url, table, captions, moreConfig, graphConfig, placeHolder, step,
		whereCond, orderBy,cli_ind) {
	var sizeData = JSON.parse(graphConfig).sizeData;
//	console.log("draw");
//	console.log("url="+url);
//	console.log("table="+table);
//	console.log("placeHolder="+placeHolder);
//	console.log("cli_ind="+cli_ind);
	//alert(url);
	if (newSelect[cli_ind] && !selecting[cli_ind]) {
//		console.log("if (newSelect && !selecting)");
//		console.log("");
		// console.log(step + " newSelect");
		selecting[cli_ind] = true;
		jQuery.post(url, {
			table : table,
			captions : captions,
			moreConfig : moreConfig,
			graphConfig : graphConfig,
			step : step,
			whereCond : whereCond,
			orderBy : orderBy
		}, function(data) {
//			console.log("callback");
//			console.log(data);
//			console.log("placeHolder="+placeHolder);
//			console.log("");
			if (data[0] == "!") {
				alert(data);
			} else {
				//alert(data);
				//console.log(data);
				var tmp = data.split("|");
				while (tmp.length != 2) {
					console.log(tmp[0]);
					tmp.shift();
				}
				data = JSON.parse(tmp[0]);
				var options = JSON.parse(tmp[1]);

				if (JSON.parse(graphConfig).method == 1) {
					// Če je graf statičen ga kar takon narišem, brez spodnje
					// obdelave podatkov in končam s funkcijo
					jQuery.plot(jQuery(placeHolder), data, options);
					return;
				}

				var cache_data = data;
				//console.log(JSON.encode(data));
				var cache_options = options;

				var countData = cache_data[0].data.length;
				
				
				for (d = 0; d < cache_data.length; d++)
					for (i = 0; i < sizeData; i++)
						cache_data[d].data.push(new Array(countData + i,
								cache_data[d].data[i][1]));

				for (i = 0; i < sizeData; i++)
					cache_options.xaxis.ticks.push(new Array(countData + i,
							cache_options.xaxis.ticks[i][1]));
				
				grjx_cache[cli_ind] = {cache_data:cache_data, cache_options:cache_options, countData:countData};
				newSelect[cli_ind] = false;
				selecting[cli_ind] = false;
			}
		}); // END $.POST()
	}

	if (grjx_cache[cli_ind] != undefined) {
//		console.log("grjx_cache[cli_ind] != undefined");
		/*
		 * d = new Date(); start = d.valueOf();
		 */
		// PRIPRAVA PODATKOV ZA GRAF
		cache_data=grjx_cache[cli_ind].cache_data;
		cache_options=grjx_cache[cli_ind].cache_options;
		countData=grjx_cache[cli_ind].countData;
		
		plot_data = new Array();
		for (d = 0; d < cache_data.length; d++) {
			plot_data[d] = new Object();
			for ( var key in cache_data[d]) {
				if (key == "data") {
					plot_data[d][key] = new Array();
					for (i = 0; i < sizeData; i++) {
						plot_data[d][key][i] = new Array();
						plot_data[d][key][i][0] = cache_data[d][key][(step % countData)
								+ i][0];
						plot_data[d][key][i][1] = cache_data[d][key][(step % countData)
								+ i][1];
					}
				} else
					plot_data[d][key] = cache_data[d][key];
			}
		}

		// PRIPRAVA PODATKOV - NASTAVITVE GRAFA
		plot_options = new Object();
		for ( var key in cache_options) {
			if (key == "xaxis") {
				plot_options[key] = new Object();
				for ( var key_xaxis in cache_options[key]) {
					if (key_xaxis == "ticks") {
						plot_options[key][key_xaxis] = new Array();
						for (i = 0; i < sizeData; i++) {
							plot_options[key][key_xaxis][i] = new Array();
							plot_options[key][key_xaxis][i][0] = cache_options[key][key_xaxis][(step % countData)
									+ i][0];
							plot_options[key][key_xaxis][i][1] = cache_options[key][key_xaxis][(step % countData)
									+ i][1];
						}
					} else
						plot_options[key][key_xaxis] = cache_options[key][key_xaxis];
				}
			} else
				plot_options[key] = cache_options[key];
		}
		/*
		 * d=new Date(); stop = d.valueOf(); console.log("čas priprave podatkov:
		 * \t" + (stop-start)/1000 + "s \t("+stop+" - "+start+")");
		 * 
		 * d=new Date(); start=d.valueOf();
		 */
		jQuery.plot(jQuery(placeHolder), plot_data, plot_options);
		/*
		 * d= new Date(); stop = d.valueOf(); console.log("čas risanja: \t\t" +
		 * (stop-start)/1000 + "s \t("+stop+" - "+start+")");
		 */
	}
} // END draw()
