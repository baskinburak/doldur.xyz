jQuery(document).ready(function(){
	function dump(obj) {
		  var out = '';
		  for (var i in obj) {
		      out += i + ": " + obj[i] + "\n";
		  }

			return out;
	}

	function cloneArray(arr) {
		var temp = [];
		for(idx in arr) {
			if(arr[idx].constructor === Array) {
				temp[idx] = cloneArray(arr[idx]);
			} else {
				temp[idx] = arr[idx];
			}
		}
		return temp;
	}


	var must_courses = null;
	jQuery.get("inc/musts.json", function(data) {
		must_courses = data;
		jQuery("#musts-loading-l").append("Loaded.").fadeOut(1000);
	});

	var added_course_identifier = 0;

	var possible_schedules = null;
	var schedule_page = 0;
	var course_data = null;
	var locked = true;
	var colors = ["#CC0000", "#7A00CC", "#29A329", "#CCCC00", "#2E2E00", "#00CCCC", "#00008A", "#002900", "#E62EB8", "#005C5C", "#CC3300", "#808080", "#00FF00", "#666633", "#002E2E"];
	var schedule_color_index = 0;
	var to_be_added_color_index = 0;

	var own_things = {"mon-1":[],"mon-2":[],"mon-3":[],"mon-4":[],"mon-5":[],"mon-6":[],"mon-7":[],"mon-8":[],"mon-9":[],"tue-9":[],"wed-9":[],"thu-9":[],"fri-9":[],"tue-1":[],"tue-2":[],"tue-3":[],"tue-4":[],"tue-5":[],"tue-6":[],"tue-7":[],"tue-8":[],"wed-1":[],"wed-2":[],"wed-3":[],"wed-4":[],"wed-5":[],"wed-6":[],"wed-7":[],"wed-8":[],"thu-1":[],"thu-2":[],"thu-3":[],"thu-4":[],"thu-5":[],"thu-6":[],"thu-7":[],"thu-8":[],"fri-1":[],"fri-2":[],"fri-3":[],"fri-4":[],"fri-5":[],"fri-6":[],"fri-7":[],"fri-8":[]};

	var table_status = [[true,true,true,true,true],
											[true,true,true,true,true],
											[true,true,true,true,true],
											[true,true,true,true,true],
											[true,true,true,true,true],
											[true,true,true,true,true],
											[true,true,true,true,true],
											[true,true,true,true,true],
											[true,true,true,true,true],
											[true,true,true,true,true]];

	var days_indexes = {"mon": 0, "tue": 1, "wed": 2,"thu": 3, "fri": 4};

	var added_courses = [];

	function get_added_course_by_identifier(ident) {
		for(var i = 0; i < added_courses.length;i++) {
			if(added_courses[i]['identifier'] === ident) {
				return added_courses[i];
			}
		}
		return false;
	}

	function delete_added_course(idx) {
		for(var i = 0; i < added_courses.length;i++) {
			if(added_courses[i]['idx'] === idx) {
			 	added_courses.splice(i, 1);
				break;
			}
		}
	}

	function delete_added_course_by_identifier(ident) {
		for(var i = 0; i < added_courses.length;i++) {
			if(added_courses[i]['identifier'] === ident) {
			 	added_courses.splice(i, 1);
				break;
			}
		}
	}

	function get_added_course(idx) {
		for(var i = 0; i< added_courses.length;i++) {
			if(added_courses[i]['idx'] === idx) {
				return added_courses[i];
			}
		}
		return false;
	}

	function delete_added_course_rec(arr, idx) {
		for(var i = 0; i < arr.length;i++) {
			if(arr[i]['idx'] === idx) {
				arr.splice(i, 1);
				break;
			}
		}
	}

	function delete_added_course_rec_by_identifier(arr, ident) {
		for(var i = 0; i < arr.length;i++) {
			if(arr[i]['identifier'] === ident) {
				arr.splice(i, 1);
				break;
			}
		}
	}

	function lock() {
		locked = true;
	}
	function unlock() {
		locked = false;
	}

	function add_course(idx) {
		if(idx < 0 || idx >= course_data.length)
			return false;
		var div = jQuery("<div></div>");
		div.addClass("sc-selected-course");
		var CoLoRehe = colors[to_be_added_color_index % colors.length];
		div.css("background-color", CoLoRehe);
		div.css("display", "none");
		to_be_added_color_index++;
		div.attr("data-course-idx", idx);
		div.attr("data-course-identifier", added_course_identifier);
		var cancel_div = jQuery("<div></div>");
		cancel_div.addClass("sc-selected-course-cancel");
		div.html(course_data[idx]['n']);
		div.append(cancel_div);
		jQuery("#course-name").before(div);
		div.slideDown();
		var section_idx_c = course_data[idx]["s"].length;
		var section_idxs = []
		for(var i = 0;i <section_idx_c ; i++) {
			section_idxs.push(i);
		}
		added_courses.push({'identifier': added_course_identifier, 'idx': idx, 'section_idxs': section_idxs, "surname_constraint_disabled": false, "dept_constraint_disabled": false, "bg_color": CoLoRehe});
		added_course_identifier++;
		div.click();
	}

	function add_edit_course(cc, sn) {
		var i = 0;
		for(; i < course_data.length; i++) {
			if(course_data[i]["c"] == cc) break;
		}
		if(i === course_data.length)return;
		add_course(i);
		var added = get_added_course(i);
		var j = 0;
		for(; j < course_data[i]["s"].length; j++) {
			if(course_data[i]["s"][j]["sn"] == sn) break;
		}
		if (j === course_data[i]["s"].length)return;
		added["section_idxs"] = [j];
	}

	jQuery.get("inc/data.json?"+new Date().getTime(), function(data) {
		course_data = data;
		jQuery("#course-data-loading-l").append("Loaded.").fadeOut(1000);
		if(typeof edit_data != 'undefined') {
			for(idxx in edit_data) {
				var cur = edit_data[idxx];
				var cur_cc = cur["cc"];
				var cur_sn = cur["sn"];
				add_edit_course(cur_cc, cur_sn);
			}
		}
		unlock();
	});

	function bsearch(term,start,end) {
		if(start == end && course_data[start]['n'].toLowerCase().indexOf(term.toLowerCase())==0)
			return [start];
		if(start >= end)
			return [];
		var idx = Math.floor((start+end)/2);
		if(course_data[idx]['n'].toLowerCase().indexOf(term.toLowerCase())==0) {
			while(idx>=0 && course_data[idx]['n'].toLowerCase().indexOf(term.toLowerCase())==0) idx--;
			idx++;
			var arr = [];
			while(idx < course_data.length && arr.length < 10 && course_data[idx]['n'].toLowerCase().indexOf(term.toLowerCase())==0) {
				arr.push(idx);
				idx++;
			}
			return arr;
		}
		if(term.toLowerCase() < course_data[idx]['n'].toLowerCase())
			return bsearch(term, start, idx-1);
		return bsearch(term, idx+1, end);
	}

	function bsearchsingle(term, start, end) {
		if(start>end) {
			return false;
		}
		var idx = Math.floor((start+end)/2);
		if(course_data[idx]['n'] == term) {
			return idx;
		}
		if(term < course_data[idx]['n']) {
			return bsearchsingle(term, start, idx-1);
		}
		return bsearchsingle(term, idx+1, end);
	}
	
	function normal_search(inp) {
		var result = [];
		var l = 0;
		for(var i = 0; i < course_data.length && l < 10; i++) {
			if(course_data[i]["n"].toLowerCase().indexOf(inp.toLowerCase()) !== -1) {
				result.push(i);
				l++;
			}
		}
		return result;
	}

	jQuery("#course-name").autocomplete({
		source: function(request, response) {
			if(locked) {
				response([]);
				return;
			}
			var inp = request.term;
			var result_idxs = bsearch(inp,0,course_data.length-1);
			if(result_idxs.length == 0) {
				result_idxs = normal_search(inp);
			}
			var response_array = [];
			for(var i = 0; i<result_idxs.length; i++) {
				var label = course_data[result_idxs[i]]['n'];
				response_array.push({value: label, idx:i});
			}
			response(response_array);
		},
		select: function(event, ui) {
			event.preventDefault();
			jQuery("#course-name").val('');
			var item = ui.item.value;
			var idx = bsearchsingle(item, 0, course_data.length-1);
			if(idx !== false) {
				add_course(idx);
			}
			
		}
	});

	jQuery("#schedule-table td").click(function(){
		var dis = jQuery(this);
		var id = dis.attr("id");
		if(dis.attr('filled') == 'true')
			return;
		if(own_things[id].length > 0)
			return;
		var y = days_indexes[id.split('-')[0]];
		var x = id.split('-')[1]-1;

		if(table_status[x][y]) {
			dis.addClass("sc-cell-occupied");
			dis.html("Don't fill.");
		} else {
			dis.removeClass("sc-cell-occupied");
			dis.html('');
		}
		table_status[x][y] = !table_status[x][y];
	});
	
	jQuery("div").delegate(".sc-selected-course-cancel","click", function(e){
		e.stopPropagation();
		var dis = jQuery(this);
		var parent = dis.parent(".sc-selected-course");
		var idx = parent.attr("data-course-idx");
		var ident = parseInt(parent.attr("data-course-identifier"));
		delete_added_course_by_identifier(ident);
		jQuery("#form-course").slideUp();
		parent.slideUp(function() {
			parent.remove();
		});
		return false;
	});
	
	jQuery("div").delegate(".sc-selected-course","click", function(e) {
		var dis = jQuery(this);
		var idx = parseInt(dis.attr("data-course-idx"));
		var ident = parseInt(dis.attr("data-course-identifier"));
		jQuery("#form-course").attr("data-course-idx", idx).fadeIn(500);
		jQuery("#form-course").attr("data-course-identifier", ident);
		jQuery("#form-course-name").html(course_data[idx]['n']);
		jQuery("#course-info-to-reveal").hide();
		var selector = jQuery("#section-selector");
		selector.html('');
		var content = jQuery("#course-info-content");
		content.html('');
		var a_course = get_added_course_by_identifier(ident);
		//alert(JSON.stringify(a_course));
		if(!a_course["surname_constraint_disabled"]) {
			jQuery("#surname-constraint-current-course").prop("checked", false);
		} else {
			jQuery("#surname-constraint-current-course").prop("checked", true);
		}

		if(!a_course["dept_constraint_disabled"]) {
			jQuery("#dept-constraint-current-course").prop("checked", false);
		} else {
			jQuery("#dept-constraint-current-course").prop("checked", true);
		}
		for(var i = 0;i < course_data[idx]["s"].length;i++) {
			var section = course_data[idx]["s"][i];
			var new_option = jQuery("<input>");
			new_option.attr("type","checkbox");
			new_option.addClass("section-select");
			new_option.val(i);
			if(a_course["section_idxs"].indexOf(i) !== -1) {
				new_option.prop("checked", true);
			}
			var new_option_label = jQuery("<label></label>");
			new_option_label.html(section["sn"]);
			var option_div = jQuery("<div></div>");
			option_div.addClass("select-option-div");
			option_div.append(new_option);
			option_div.append(new_option_label);
			selector.append(option_div);
			var inside = "Section: " + section["sn"] + "<br/><br/>";
			for(var j=0; j < section["i"].length; j++) {
				var instructor = section["i"][j];
				inside += "Lecturer " + (j+1) + ": " + instructor + "<br/>";
			}
			inside += "<br/>";
			for(var j = 0;j < section["t"].length; j++) {
				var time = section["t"][j];
				var exploded = time["b"].split("-");
				if(exploded[0] == 'mon') {
					inside += "Monday ";
				} else if(exploded[0] == 'tue') {
					inside += "Tuesday ";
				} else if(exploded[0] == 'wed') {
					inside += "Wednesday ";
				} else if(exploded[0] == 'thu') {
					inside += "Thursday ";
				} else if(exploded[0] == 'fri') {
					inside += "Friday ";
				} else {
					continue;
				}
				
				inside += (parseInt(exploded[1])+7) + ':40 - ' + (parseInt(exploded[1])+8) + ':40 (' + time["p"] + ')<br/>'
			}
			inside += "<br/>";
			for(var j=0; j < section["cs"].length; j++) {
				var constraint = section["cs"][j];
				inside += constraint["ss"] + " - " + constraint["es"] + "(" + constraint["gd"] + ")<br/>"; 
			}
			var section_div = jQuery("<div></div>");
			section_div.addClass("single-section");
			section_div.html(inside);
			content.append(section_div);
		}
		return false;
	});

	jQuery("#toggle-course-info").click(function(e){
		e.preventDefault();
		jQuery("#course-info-to-reveal").slideToggle(300);
	});

	jQuery("#apply-options").click(function(e) {
		e.preventDefault();
		var course_idx = parseInt(jQuery("#form-course").attr("data-course-idx"));
		var course_ident = parseInt(jQuery("#form-course").attr("data-course-identifier"));
		var section_list = [];
		jQuery(".section-select").each(function(){
			var dis = jQuery(this);
			if(dis.prop("checked")) {
				section_list.push(parseInt(dis.val()));
			}
		});
		var surname_check_disabled = jQuery("#surname-constraint-current-course").prop("checked");
		var dept_check_disabled = jQuery("#dept-constraint-current-course").prop("checked");
		var ehe_color = get_added_color_with_identifier(course_ident);
		delete_added_course_by_identifier(course_ident);
		added_courses.push({'identifier': course_ident, 'idx': course_idx, "section_idxs": section_list, "surname_constraint_disabled": surname_check_disabled, "dept_constraint_disabled": dept_check_disabled,"bg_color":ehe_color});
	});

	function timesOkay(table, times, disable_collision) {
		if(disable_collision) {
			table = table_status;
		}
		for(var i = 0; i<times.length; i++) {
			var time = times[i]["b"];
			var exploded = time.split("-");
			var y = days_indexes[exploded[0]];
			var x = exploded[1]-1;
			if(table[x][y] == false) return false;
		}
		return true;
	}
	
	function constraintsOkay(constraints, surname, dept_array, surname_ignore, dept_ignore) {
		for(var i=0; i<constraints.length;i++) {
			if(((surname.toLowerCase().localeCompare(constraints[i]["es"].toLowerCase())<=0 && surname.toLowerCase().localeCompare(constraints[i]["ss"].toLowerCase())>=0) || surname_ignore) && (constraints[i]["gd"].toUpperCase() == "ALL" || dept_array.indexOf(constraints[i]["gd"].toUpperCase())!=-1 || dept_ignore))
				return true;
		}
		return false;
	}


	function fillTable(table, times) {
		table = cloneArray(table);
		for(var i = 0; i<times.length; i++) {
			var time = times[i]["b"];
			var exploded = time.split("-");
			var y = days_indexes[exploded[0]];
			var x = exploded[1]-1;
			table[x][y] = false;
		}
		return table;
	}

	var failed_courses = [];

	function generate_all_schedules(schedule_table, course_list, surname, dept_array, disable_surname, disable_dept, disable_collision) {
		//alert(JSON.stringify(course_list));
		schedule_table = cloneArray(schedule_table);
		course_list = cloneArray(course_list);
		if(course_list.length === 0) {
			return [[]];
		}
	//	console.log(JSON.stringify(course_list));
		var course_idx = course_list[0]['idx'];
		var course_identifier = course_list[0]['identifier'];
		var return_array = [];
		var current_course = course_list[0];
		var d_surname = disable_surname || current_course["surname_constraint_disabled"];
		var d_dept = disable_dept || current_course["dept_constraint_disabled"];
		var failed = true;
		for(var i=0; i < current_course["section_idxs"].length; i++) {
			var section_idx = current_course["section_idxs"][i];
			var section = course_data[course_idx]["s"][section_idx];
			var times = section["t"];
			if(!timesOkay(schedule_table, times, disable_collision)) {
				continue;
			}
			if(!constraintsOkay(section["cs"], surname, dept_array, d_surname, d_dept)) {
				continue;
			}
			failed = false;
			var rec_course_list = cloneArray(course_list);
			//console.log(JSON.stringify(rec_course_list));
			delete_added_course_rec_by_identifier(rec_course_list, course_identifier);
			//console.log(JSON.stringify(rec_course_list));
			var rec_table = fillTable(schedule_table, times);
			var rec_result = generate_all_schedules(rec_table, rec_course_list, surname, dept_array, disable_surname, disable_dept, disable_collision);
			for(var j=0; j < rec_result.length; j++) {
				var append_to_return;
				append_to_return = cloneArray(rec_result[j]);
				var lo = {"course_idx": course_idx, "section_idx": section_idx};
				append_to_return.push(lo);
				return_array.push(append_to_return);
			}
/*			alert(i);
			alert(current_course["section_idxs"].length);
			alert(i < current_course["section_idxs"].length);*/
		}
		if(failed) {
			if(failed_courses.indexOf(course_idx) === -1)
				failed_courses.push(course_idx);
		}
		return return_array;
	}
	function get_added_color_with_idx(course_idx) {
		for(var i=0;i<added_courses.length;i++) {
			if(added_courses[i]["idx"] == course_idx)
				return added_courses[i]["bg_color"];
		}
		return "#000000";
	}
	function get_added_color_with_identifier(ident) {
		for(var i=0;i<added_courses.length;i++) {
			if(added_courses[i]['identifier'] == ident)
				return added_courses[i]["bg_color"];
		}
		return "#000000";
	}
	function apply_schedule(schedule) {
		jQuery(".lecture-block").parent("td").removeAttr("filled");
		jQuery(".lecture-block").remove();
		for(var i=0; i<schedule.length; i++) {
			var course_idx = schedule[i]["course_idx"];
			var section_idx = schedule[i]["section_idx"];
			var color_ehe = get_added_color_with_idx(course_idx);
			for(var j=0; j < course_data[course_idx]["s"][section_idx]["t"].length; j++) {
				var time = course_data[course_idx]["s"][section_idx]["t"][j];
				var new_div = jQuery("<div></div>");
				new_div.addClass("lecture-block");
				var inner = course_data[course_idx]['n'].split('-')[0].trim();
				inner += " - ";
				inner += course_data[course_idx]["s"][section_idx]["sn"];
				inner = inner + " (" + time["p"] + ")";
				new_div.html(inner);
				new_div.css("background-color", color_ehe);
				jQuery("#"+time["b"]).attr("filled", "true");
				jQuery("#"+time["b"]).append(new_div);
			}
			schedule_color_index++;
		}
	}

	function bySectionCount(a, b) {
		return a["section_idxs"].length - b["section_idxs"].length;
	}
	function byLunch(a, b) {
		var a_count = 0;
		var b_count = 0;
		var a_times = [];
		var b_times = [];

		for(var i = 0;i < a.length;i++) {
			var course_idx = a[i]["course_idx"];
			var section_idx = a[i]["section_idx"];
			var times = course_data[course_idx]["s"][section_idx]["t"];
			for(var j = 0; j < times.length; j++) {
				a_times.push(times[j]["b"]);
			}
		}
		for(var i = 0;i < b.length;i++) {
			var course_idx = b[i]["course_idx"];
			var section_idx = b[i]["section_idx"];
			var times = course_data[course_idx]["s"][section_idx]["t"];
			for(var j = 0; j < times.length; j++) {
				b_times.push(times[j]["b"]);
			}
		}
		if(a_times.indexOf("mon-4") == -1 || a_times.indexOf("mon-5") == -1 || a_times.indexOf("mon-6"))
			a_count++;
		if(a_times.indexOf("tue-4") == -1 || a_times.indexOf("tue-5") == -1 || a_times.indexOf("tue-6"))
			a_count++;
		if(a_times.indexOf("wed-4") == -1 || a_times.indexOf("wed-5") == -1 || a_times.indexOf("wed-6"))
			a_count++;
		if(a_times.indexOf("thu-4") == -1 || a_times.indexOf("thu-5") == -1 || a_times.indexOf("thu-6"))
			a_count++;
		if(a_times.indexOf("fri-4") == -1 || a_times.indexOf("fri-5") == -1 || a_times.indexOf("fri-6"))
			a_count++;

		if(b_times.indexOf("mon-4") == -1 || b_times.indexOf("mon-5") == -1 || b_times.indexOf("mon-6"))
			b_count++;
		if(b_times.indexOf("tue-4") == -1 || b_times.indexOf("tue-5") == -1 || b_times.indexOf("tue-6"))
			b_count++;
		if(b_times.indexOf("wed-4") == -1 || b_times.indexOf("wed-5") == -1 || b_times.indexOf("wed-6"))
			b_count++;
		if(b_times.indexOf("thu-4") == -1 || b_times.indexOf("thu-5") == -1 || b_times.indexOf("thu-6"))
			b_count++;
		if(b_times.indexOf("fri-4") == -1 || b_times.indexOf("fri-5") == -1 || b_times.indexOf("fri-6"))
			b_count++;

		return b_count-a_count;
	}

	function byGather(a,b) {
		var a_blocks = 0;
		var b_blocks = 0;
		
		var a_times = {"mon": [], "tue": [], "wed": [], "thu": [], "fri": []};;
		var b_times = {"mon": [], "tue": [], "wed": [], "thu": [], "fri": []};
		for(var i = 0; i < a.length; i++) {
			var course_idx = a[i]["course_idx"];
			var section_idx = a[i]["section_idx"];

			var times = course_data[course_idx]["s"][section_idx]["t"];

			for(var j = 0; j< times.length; j++) {
				var exploded = times[j]["b"].split("-");
				a_times[exploded[0]].push(parseInt(exploded[1]));
			}
		}
		for(var i = 0; i < b.length; i++) {
			var course_idx = b[i]["course_idx"];
			var section_idx = b[i]["section_idx"];

			var times = course_data[course_idx]["s"][section_idx]["t"];

			for(var j = 0; j< times.length; j++) {
				var exploded = times[j]["b"].split("-");
				b_times[exploded[0]].push(parseInt(exploded[1]));
			}
		}
		for(day in a_times) {
			a_times[day].sort();
			var cont=false;
			var last=null;
			for(var j = 0; j < a_times[day].length; j++) {
				if(!cont) {
					cont = true;
					a_blocks++;
				} else {
					if(last+1 === a_times[day][j]) {

					} else {
						a_blocks++;
					}
				}
				last = a_times[day][j];
			}
		}

		for(day in b_times) {
			b_times[day].sort();
			var cont=false;
			var last=null;
			for(var j = 0; j < b_times[day].length; j++) {
				if(!cont) {
					cont = true;
					b_blocks++;
				} else {
					if(last+1 === b_times[day][j]) {

					} else {
						b_blocks++;						
					}
				}
				last = b_times[day][j];
			}
		}/*
		alert(JSON.stringify(a_times));
		alert(a_blocks);
		alert(JSON.stringify(b_times));
		alert(b_blocks);*/
		return a_blocks-b_blocks;
	}

	var lunchImportance = 0.5;
	var gatherImportance = 0.5;

	function byLunchAndGather(a,b) {
		return byLunch(a,b)*lunchImportance + byGather(a,b)*gatherImportance;
	}

	jQuery("#initiate-schedule").click(function(e){
		e.preventDefault();
		failed_courses = [];
		added_courses.sort(bySectionCount);
		//console.log(JSON.stringify(added_courses));
		var surname = jQuery("#surname").val().substring(0,2);
		var dept = jQuery("#dept-code").val().split(",");
		var dept_array = [];
		for(d in dept) {
			dept_array.push(dept[d].trim().toUpperCase());
		}
		var disable_surname_constraints = jQuery("#surname-constraint").prop("checked");
		var disable_dept_constraints = jQuery("#dept-constraint").prop("checked");
		var disable_collision_check = jQuery("#collision-constraint").prop("checked");

		var courses = cloneArray(added_courses);
		var all_schedules = generate_all_schedules(table_status, courses, surname, dept_array, disable_surname_constraints, disable_dept_constraints, disable_collision_check);
		if(failed_courses.length != 0) {
			var ll = "";
			for(kk in failed_courses) {
				ll += course_data[failed_courses[kk]]['n'].split("-")[0].trim()+",";
			}
			ll = ll.substring(0, ll.length-1);
			//alert("Course could not be placed: " + ll + "\nStopped.");
			//return false;
		}
		var lunch = jQuery("#lunch-time").prop("checked");
		var gather = jQuery("#gather-courses").prop("checked");
		if(lunch && gather) {
			all_schedules.sort(byLunchAndGather);
		} else if (lunch) {
			all_schedules.sort(byLunch);
		} else if (gather) {
			all_schedules.sort(byGather);
		}
		possible_schedules = all_schedules;
		//alert(JSON.stringify(possible_schedules));
		if(possible_schedules.length > 0) {
			schedule_page = 0;
			apply_schedule(possible_schedules[0]);
			jQuery("#sc-possibility-prev").addClass("secondary");
			if(possible_schedules.length-1 > schedule_page) {
				jQuery("#sc-possibility-next").removeClass("secondary");
			} else {
				jQuery("#sc-possibility-next").addClass("secondary");
			}
			jQuery("#possibility-status").html('1/'+possible_schedules.length);
		} else {
			jQuery("#possibility-status").html('0/0');
			alert("No possible schedules.\nTry disabling some constraints and checks.");
		}
		//jQuery("body").append(JSON.stringify(all_schedules));
	});
	jQuery("#sc-possibility-next").click(function(){
		if(schedule_page >= possible_schedules.length-1)
			return;
		schedule_page++;
		apply_schedule(possible_schedules[schedule_page]);
		jQuery("#possibility-status").html((schedule_page+1) + '/' + possible_schedules.length);
		jQuery("#sc-possibility-prev").removeClass("secondary");
		if(schedule_page == possible_schedules-1) {
			jQuery("#sc_possibity-next").addClass("secondary");
		}
	});
	jQuery("#sc-possibility-prev").click(function(){
		if(schedule_page <= 0)
			return;
		schedule_page--;
		apply_schedule(possible_schedules[schedule_page]);
		jQuery("#possibility-status").html((schedule_page+1) + '/' + possible_schedules.length);
		jQuery("#sc-possibility-next").removeClass("secondary");
		if(schedule_page == 0) {
			jQuery("#sc_possibity-prev").addClass("secondary");
		}
	});

	jQuery("#gather-courses").click(function(){
		if(jQuery(this).prop("checked") && jQuery("#lunch-time").prop("checked")) {
			jQuery("#gather-block-importance").slideDown();
			jQuery("#lunch-time-importance").slideDown();
			$(document).foundation('reflow');
		} else {
			jQuery("#gather-block-importance").slideUp();
			jQuery("#lunch-time-importance").slideUp();
		}
	});
	jQuery("#lunch-time").click(function(){
		if(jQuery(this).prop("checked") && jQuery("#gather-courses").prop("checked")) {
			jQuery("#gather-block-importance").slideDown();
			jQuery("#lunch-time-importance").slideDown();
			$(document).foundation('reflow');
		} else {
			jQuery("#gather-block-importance").slideUp();
			jQuery("#lunch-time-importance").slideUp();
		}
	});

	$('#gather-imp').on('change.fndtn.slider', function() {
		if(!locked) {
			lock();
			var val = $(this).attr('data-slider');
			gatherImportance = val/100;
			lunchImportance = 1 - lunchImportance;
			$('#lunch-imp').foundation('slider', 'set_value', 100-val);
			unlock();
		}
		return false;
	});

	$('#lunch-imp').on('change.fndtn.slider', function(){
		if(!locked) {
			lock();
			var val = $(this).attr('data-slider');
			lunchImportance = val/100;
			gatherImportance = 1 - lunchImportance;
			$('#gather-imp').foundation('slider', 'set_value', 100-val);
			unlock();
		}
		return false;
	});

	jQuery("#add-must-courses").click(function(e){
		e.preventDefault();
		var dept = jQuery("#dept-code").val().split(",");
		var dept_array = [];
		for(d in dept) {
			dept_array.push(dept[d].trim().toUpperCase());
		}
	//	alert(JSON.stringify(dept_array));
		var semester = parseInt(jQuery("#semester").val().trim());
		for(d in dept_array) {
			if(typeof must_courses[dept_array[d]] == 'undefined') {
				alert("Department code does not exist: " + dept_array[d]);
				return false;
			}
			if(typeof must_courses[dept_array[d]][semester] == 'undefined') {
				alert("There are no must course for dept/semester: " + dept_array[d] + "/" + semester);
				return false;
			}
		}

		//added_courses = [];
		//jQuery(".sc-selected-course").remove();
		jQuery("#form-course").slideUp();
		for(var k=0;k < dept_array.length; k++) {
			var dept = dept_array[k];
			for(var i = 0; i < must_courses[dept][semester].length; i++) {
				var course_code = must_courses[dept][semester][i];
				var course_idx = 0;
				//alert(course_code);
				for(; course_idx < course_data.length; course_idx++) {
					//alert(course_data[course_idx]["c"]);
					if(course_data[course_idx]["c"] == course_code)
						break;
				}
				//alert(course_idx);
				if(get_added_course(course_idx) === false)
					add_course(course_idx);
			}
		}
	});

	function announce_error(data) {
		jQuery("#status-message").html(data);
		jQuery("#status-message").slideDown(500).delay(3000).slideUp(500);
	}

	function forgot_announce_error(data) {
		jQuery("#forgot-status-message").html(data);
		jQuery("#forgot-status-message").slideDown(500).delay(3000).slideUp(500);
	}


	function forgot_announce_success() {
		jQuery("#forgot-status-message-happy").slideDown(500).delay(3000).slideUp(500, function() {
			$('#forgot-modal').foundation('reveal', 'close');
		})
	}
	jQuery("#save-sc-button").click(function(e){
		e.preventDefault();
		if(possible_schedules == null || typeof possible_schedules[schedule_page] === 'undefined') {
			alert("Construct a schedule.");
			return false;
		}
		var email = jQuery("#em").val().trim();
		var username = jQuery("#un").val().trim();
		var password = jQuery("#pw").val().trim();
		var c_schedule = possible_schedules[schedule_page];
		var send_schedule = [];
		for(var i = 0; i < c_schedule.length; i++) {
			var course_idx = c_schedule[i]["course_idx"];
			var section_idx = c_schedule[i]["section_idx"];
			send_schedule.push({'cc': course_data[course_idx]['c'], 'sn': course_data[course_idx]['s'][section_idx]["sn"]});
		}
		var data = {
			'email' : email,
			'password' : password,
			'username' : username,
			'schedule' : send_schedule,
			'own' : own_things
		};
		jQuery.post("./save3.php", data, function(data) {
			//alert(data);
			data = JSON.parse(data);
			if(typeof data["error"] != 'undefined') {
				announce_error(data["error"]);
			} else {
				window.location = data["success"];
			}
		});
	});

	jQuery("#forgot-sc-button").click(function(e){
		e.preventDefault();
		var dis = $(this);
		dis.hide();
		$("#loading-div").show();
		var email = jQuery("#forgot_em").val().trim();
		var password = jQuery("#forgot_pw").val().trim();
		
		var data = {
			'email' : email,
			'password' : password
		};
		jQuery.post("./forgot.php", data, function(data) {
			data = JSON.parse(data);
			if(typeof data["error"] != 'undefined') {
				forgot_announce_error(data["error"]);
			} else {
				forgot_announce_success();
			}
			dis.show();
			$("#loading-div").hide();
		});
	});

	var toggleStatus = false;
	jQuery("#toggle-sections").click(function(){
		jQuery(".section-select").prop("checked", toggleStatus);
		toggleStatus = !toggleStatus;
		jQuery("#apply-options").click();
	});

	jQuery(document).on('keydown', function(e) {
		  if(e.keyCode == 37) {
				jQuery("#sc-possibility-prev").click();
			} else if (e.keyCode == 39) {
				jQuery("#sc-possibility-next").click();
			}
	});
	jQuery("#form-course").on('change', "#surname-constraint-current-course",function(){
		jQuery("#apply-options").click();
		return false;
	});
	jQuery("#form-course").on('change', "#dept-constraint-current-course",function(){
		jQuery("#apply-options").click();
		return false;
	});
	jQuery("#form-course").on('change', ".section-select", function(){
		jQuery("#apply-options").click();
		return false;
	});
	jQuery("#add-your-own").click(function(e){
		e.preventDefault();
		jQuery("#your-own-thing").slideToggle();
	});
	jQuery("#add-confirm").click(function(e){
		e.preventDefault();
		var text = jQuery("#own-text").val().trim();
//		jQuery("body").append(text.length);
		if(text.length == 0) {
			alert("You should enter text.");
			return false;
		}
		var day = jQuery("#own-day option:selected").val();
		if(typeof days_indexes[day] == 'undefined') {
			return false;
		}
		var time = jQuery("#own-time option:selected").val();
		if(parseInt(time)<1 || parseInt(time)>9) {
			return false;
		}
		time = parseInt(time).toString();
		if(table_status[parseInt(time)-1][days_indexes[day]] == false) {
			alert("There is a don't fill constraint in the cell.");
			return false;
		}
		var idx = day+"-"+time.toString();
		own_things[idx].push(text);
		//alert(JSON.stringify(own_things));
		var box = jQuery("<div></div>");
		box.addClass("own-block");
		box.html(text);
		jQuery("#"+idx).append(box);
//		jQuery("#your-own-thing").slideUp();
	});
	function remove_from_array(arr,str) {
		for(var i = 0; i < arr.length; i++) {
			if(arr[i] == str) {
				arr.splice(i,1);
				break;
			}
		}
	}
	jQuery("div").delegate(".own-block", "click", function(e){
		e.preventDefault();
		e.stopPropagation();
		var dis = jQuery(this);
		var idx = dis.parent("td").attr("id");
		remove_from_array(own_things[idx], dis.html().trim());
		dis.remove();
	});
});
