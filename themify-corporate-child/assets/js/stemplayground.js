jQuery(document).ready(function($) {
	$('.uc-collapsible').click(function() {

		$(this).toggleClass('collapsed')
		.siblings('.uc-collapsible').addClass('collapsed').next('.uc-collapsible-content').slideUp(350);

		$(this).next('.uc-collapsible-content').slideToggle(350)

	});
	$('#signup-submit').click( function() {
		$('.alert-container').hide();
	});

	$('#sccountry').change(function() {
		//console.log($(this).val());
		$('#sccountry2').val($(this).val());
		if ($(this).val() == 'united-states') {
			$('.school-search').slideDown();
		} else {
			$('.school-info').slideDown();
			$('.school-search-container').slideUp();
		}
	});

	$('#school-search-submit').click(function(e) {
		var zip = $('#school-search-zip').val();
		var country = $('#sccountry').val();

		var wpnonce = $('#update_school_noncename').val();

		var data = {
				'action':'get_schools',
				'zip' : zip,
				'country' : country,
				'_wpnonce' : wpnonce,
		};
		$.post(ajaxurl, data, function(response) {
			var parsed_response = $.parseJSON( response );

			var output = '';

			if ( parsed_response['status'] == 'OK' ) {

				output = '<p>' + parsed_response['response'] + '</p>';

				if ( parsed_response['count'] ) {

					for (var i = 0; i < parsed_response['results'].length; i++) {
						output += '	<div class="input">\
											<label><input class="uc-input" type="radio" name="scschool" id="scschool" value="'+ parsed_response['results'][i]['value'] +'">'+ parsed_response['results'][i]['label'] +'</label>\
									</div>';
					}	
					$('#school-search-accept').show();
					$('.school-search-save').show();
				} else {
					$('#school-search-accept').hide();
					$('.school-search-save').show();
				}

				if ( typeof parsed_response['admin_level_1'] != 'undefined' ) {
					state_html = '<div class="uc-field uc-dropdown uc-meta-single" id="">\
									<div class="label">\
										<label>'+parsed_response['admin_level_1']+'</label>\
									</div>\
									<div class="field"><div class="input">\
											<select name="scstate" id="scstate" class="uc-input select">\
												<option value="" disabled selected></option>';

					for (var key in parsed_response['admin_level_1_values']) {
						state_html += '<option value="'+key+'" >'+parsed_response['admin_level_1_values'][key]+'</option>';
					}

					state_html += '</select></div></div></div>';

					$('.state-placeholder').html(state_html);
				} else {
					$('.state-placeholder').html('');
				}
			} else {
				if ( parsed_response['status'] == 'REQUEST_DENIED' ) {
					output = '<p>' + parsed_response['reason'] + '</p>';
				} else if ( parsed_response['status'] == 'INVALID_REQUEST' ) {
					output = '<p>Invalid request</p>';
				} else {
					output = '<p>Error: Unknown error</p>';
				}
				$('.school-search-save').hide();
			}
			$('.school-search-results').empty().append( output );
		});
	});

	$('.school-not-listed').click(function(e) {
		e.preventDefault();
		$('.school-info').slideDown();
		$('.school-search-container').slideUp();
	});

	$('.student-edit').bind("click", edit_student_post);

	$('#class-add').click(function(e) {
		e.preventDefault();

		var classcount = $('#class-table tbody tr').length;
		var classname = $('#uc-class-title-new').val();
		var classgrade = $('#uc-class-grade-new').val();
		var wpnonce = $('#update_classes_noncename').val();
		var alertcontainer = $('.class-new-container');
		var readytosubmit = true;


		var data = {
				'action':'update_class',
				'classname' : classname,
				'classgrade' : classgrade,
				'_wpnonce' : wpnonce,
		};

		if ( !classname ) {
			display_alert( class_no_name_error, 'warning', alertcontainer );
		} else{
			if ( !classgrade ) {
				if ( confirm( class_verify_various ))
					readytosubmit = true;
				else
					readytosubmit = false;
			}

			if ( readytosubmit ) {
				$.post(ajaxurl, data, function(response) {
					var parsed_response = $.parseJSON( response );

					if ( parsed_response['status'] == 'OK' ) {
						var newclassname = parsed_response['meta']['classname'].replace(/\\/g, "");
						var newrow = $('<tr class="new-row" data-ref="'+parsed_response['new']+'"><td class="jedit textleft" data-jedit-update="classname" data-jedit-action="update_class" data-jedit-id="classid" data-jedit-id-val="'+parsed_response['new']+'">'+newclassname+'</td><td>'+parsed_response['meta']['classgrade']+'</td><td><input type="submit" class="class-edit" value="Edit Row"></td></tr>');
						var tablebody = $('#class-table tbody');
						$(newrow).find('.class-edit').bind("click", edit_class_post);
						$(newrow)
							.prependTo( tablebody )
							.find('td')
							.animate({ paddingTop: 8, paddingBottom: 8 } )
							.wrapInner('<div style="display: none;" />')
							.parent()
							.find('td > div')
							.slideDown(400, function(){
								var $set = $(this);
								$set.replaceWith($set.contents());
						});
						$('#uc-class-title-new').val('');
						display_alert( 'Class added', 'success', alertcontainer );

						if ( parsed_response.hasOwnProperty('proceed') ) {
							$('.page-content .proceed-container').empty().html('<div><a href="'+parsed_response['proceed']['url']+'" >'+parsed_response['proceed']['text']+'</a></div>');

						}

					} else if ( parsed_response['status'] == 'REQUEST_DENIED' ) {
						display_alert( 'Error: ' + parsed_response['reason'], 'caution', alertcontainer );
					} else if ( parsed_response['status'] == 'INVALID_REQUEST' ) {
						display_alert( 'Error: Invalid request', 'caution', alertcontainer );
					} else {
						display_alert( 'Error: Unknown error', 'caution', alertcontainer );
					}
				});
			}
		}
	});
	
	$('.class-edit').bind("click", edit_class_post);

	$( ".uc-groups-container-editable" ).each( function() {
		var collapsible_content = this;
		$(collapsible_content).find('.uc-drag').draggable( {
			containment: collapsible_content,
			zIndex: 1000,
			cursor: 'grabbing',
			revert: "invalid",
			revertDuration: 200
		});
	});
	$('.uc-drag-container-editable').droppable( {
		accept: '.uc-drag',
		hoverClass: 'hovered',
		drop: handleStudentDrop
	});
	$('.group-add').click( function() {
		var groups = $(this).siblings('.uc-groups-container').find('.groups');
		var groupnum = $(groups).attr('data-count');
		groupnum++;
		$(groups).attr('data-count', groupnum);

		$(groups).append('<div class="uc-drag-container group-container uc-drag-container-editable"><div class="uc-drag-container-label">Team '+groupnum+'</div><div class="uc-drag-draggable-container"><div class="clear"></div></div></div>');
		$(groups).find('.uc-drag-container').droppable( {
			accept: '.uc-drag',
			hoverClass: 'hovered',
			drop: handleStudentDrop
		});
	});
	$('.group-save').click( function() {
		var thiscontainer = $(this).closest('.group-modify-container');
		var remainingstudents = $(thiscontainer).find('.student-container .uc-drag').length;
		$(thiscontainer).find('.alert-container').hide();

		var wpnonce = $('#update_groups_noncename').val();

		if ( $(thiscontainer).attr('data-activity') ) {
			var activity = $(thiscontainer).attr('data-activity');
			var def = 0;
		} else {
			var def = 1;
			var activity = 0;
		}

		if ( def && remainingstudents > 0 ) 
			display_alert( group_missing_student_error, 'warning', thiscontainer );
		else {

			var data = {
				'action': 'update_groups',
				'_wpnonce' : wpnonce,
				'def' : def,
				'act' : activity,
				'class' : $(thiscontainer).attr('data-ref'),
			};

			var groupnum = 0;
			$(thiscontainer).find('.group-container').each(function( index ) {
				var groupstudents = $(this).find('.uc-drag');
				if ( $(groupstudents).length > 0 ) {
					data['group' + groupnum] = $(groupstudents).length;
					var studentnum = 0;
					$(groupstudents).each(function(){
						data['g' + groupnum + '_' +studentnum ] = $(this).attr('data-sid');
						studentnum++;
					});
					groupnum++;
				}
			});
			data['groups'] = groupnum;
			$.post(ajaxurl, data, function(response) {
				var parsed_response = $.parseJSON( response );
				var updatedgroups = response;
				if ( parsed_response['status'] == 'OK' ) {
					display_alert( 'Teams saved', 'success', thiscontainer );
					$(thiscontainer).attr('data-changed', '0');
					$(thiscontainer).find('.group-container').each(function( index ) {
						$(this).attr('data-ref', parsed_response['new'][index] );
					});
					if ( parsed_response.hasOwnProperty('proceed') ) {
						$(thiscontainer).append('<div><a href="'+parsed_response['proceed']['url']+'" >'+parsed_response['proceed']['text']+'</a></div>');

					}
				} else if ( parsed_response['status'] == 'REQUEST_DENIED' ) {
					display_alert( 'Error: ' + parsed_response['reason'], 'caution', thiscontainer );
				} else if ( parsed_response['status'] == 'INVALID_REQUEST' ) {
					display_alert( 'Error: Invalid request', 'caution', thiscontainer );
				} else {
					display_alert( 'Error: Unknown error', 'caution', thiscontainer );
				}
			});
		}
	});
	$('.group-submit').click( function() {
		var thiscontainer = $(this).closest('.group-modify-container');
		$(thiscontainer).find('.alert-container').hide();

		var wpnonce = jQuery('#update_groups_noncename').val();

		var groups = $(thiscontainer).find('.group-container');
		var scoresdonotpass = false;
		var teamwork5count = 0;
		var scores;
		var groupcount = 0;
		var activity = $('article.uc_activity').attr('id');
		var data = {
			'action':'submit_score',
			'_wpnonce' : wpnonce,
			'ac' : activity.replace('post-', ''),
			'cl' : $(thiscontainer).attr('data-ref'),
		};

		$(groups).each( function() {
			if ( $(this).find('.uc-drag').length != 0 ) {
				var scorecount = 0;
				data['g'+groupcount] =  $(this).attr('data-ref');	

				scores = $(this).find('.uc-activity-score');

				$(scores).each( function() {
					if ( !$(this).val() || !$.isNumeric( $(this).val() ) ) {
						scoresdonotpass = true;
					} else {
						if ( $(this).hasClass('teamwork-score') ) {
							data['g'+groupcount+'-t'] = $(this).val();
							if ( data['g'+groupcount+'-t'] == 5 )
								teamwork5count++;
						} else {
							data['g'+groupcount+'-'+scorecount] = $(this).val();
							scorecount++;
						}
					}
				});
				groupcount++;
			}
		});
		data['groups'] = groupcount;
		var maxteamwork5num = Math.ceil( max_teamwork_5 * $(scores).length / 100 );

		if ( $(thiscontainer).attr('data-changed') == 1 ) {
			display_alert( a_changed_teams, 'warning', thiscontainer );
		} else if ( scoresdonotpass ) {
			display_alert( a_missing_scores, 'warning', thiscontainer );
		} else if ( teamwork5count > maxteamwork5num ) {
			display_alert( a_teamwork_5, 'warning', thiscontainer );
		}  else {
			if ( confirm('Are you sure you want to submit your scores? This will complete the activity and cannot be reversed.')) {
				$.post(ajaxurl, data, function(response) {
					var parsed_response = $.parseJSON( response );
					//console.log(parsed_response);
					if ( parsed_response['status'] == 'OK' ) {
						display_alert( 'Scores submitted', 'success', thiscontainer );
						setTimeout(function(){
						   window.location.reload(1);
						}, 3000);
					} else if ( parsed_response['status'] == 'REQUEST_DENIED' ) {
						display_alert( 'Error: ' + parsed_response['reason'], 'caution', thiscontainer );
					} else if ( parsed_response['status'] == 'INVALID_REQUEST' ) {
						display_alert( 'Error: Invalid request', 'caution', thiscontainer );
					} else {
						display_alert( 'Error: Unknown error', 'caution', thiscontainer );
					}
				});
			}
		}
	});
	$( ".activeTeam" ).change(function() {
		if ( $( this ).val() == 'class' ) {
			chart_prototype['title']['text'] = unescapeHTML(class_chart_parts['title']);
			chart_prototype['subtitles']['text'] = unescapeHTML(class_chart_parts['subtitle']);
			chart_prototype['data'][0]['dataPoints'] = class_datapoints;

			var chart_average = new CanvasJS.Chart("chart-average-container", chart_prototype);
			chart_average.render();
		} else {
			chart_prototype['title']['text'] = unescapeHTML(team_chart_parts['title']);
			chart_prototype['subtitles']['text'] = unescapeHTML(team_chart_parts['subtitle']);

			$group_id = $( this ).val();

			if (typeof teams_datapoints[$group_id] !== 'undefined') {
				chart_prototype['data'][0]['dataPoints'] = teams_datapoints[$group_id];
				var chart_average = new CanvasJS.Chart("chart-average-container", chart_prototype);
				chart_average.render();
			} else {
				var wpnonce = $('#update_results_noncename').val();
				var data = {
						'action':'get_team_datapoints',
						'gid' : $group_id,
						'_wpnonce' : wpnonce,
				};
				$.post(ajaxurl, data, function(response) {
					var parsed_response = $.parseJSON( response );
					
					if ( parsed_response['status'] == 'OK' ) {
						teams_datapoints[$group_id] = parsed_response['team_datapoints'];
						chart_prototype['data'][0]['dataPoints'] = teams_datapoints[$group_id];
						var chart_average = new CanvasJS.Chart("chart-average-container", chart_prototype);
						chart_average.render();
					} else {
						if ( parsed_response['status'] == 'REQUEST_DENIED' ) {

						} else if ( parsed_response['status'] == 'INVALID_REQUEST' ) {

						} else {

						}

					}
				});
			}	
		}
	});
});

function remove_post() {
	if ( confirm(remove_warning) ) {
		var wpnonce = jQuery('#remove_post_noncename').val();
		var this_row = jQuery(this).closest('tr');
		var post_to_remove = jQuery(this_row).prev('tr');
		var remove_id = jQuery(post_to_remove).attr('data-ref');
		var data = {
				'action':'remove_post',
				'postid' : remove_id,
				'_wpnonce' : wpnonce,
		};
		jQuery.post(ajaxurl, data, function(response) {
			remove_row( post_to_remove );
			remove_row( this_row );
		});
	}
}

function cancel_class_update() {
	row_to_remove = jQuery(this).closest('tr').next('tr');
	remove_row( row_to_remove );
	jQuery(this).unbind('click').bind("click", edit_class_post).val('Edit Row');
}

function cancel_student_update() {
	row_to_remove = jQuery(this).closest('tr').next('tr');
	remove_row( row_to_remove );
	jQuery(this).unbind('click').bind("click", edit_student_post).val('Edit Row');
}

function remove_row( row_to_remove ) {
	jQuery(row_to_remove)
		.children('td')
		.animate({ paddingTop: 0, paddingBottom: 0 })
		.wrapInner('<div />')
		.children()
		.slideUp(function() { jQuery(this).closest('tr').remove(); });
}

function edit_class_post() {
	jQuery(this).unbind('click').bind("click", cancel_class_update).val('Close');
	var post_to_edit = jQuery(this).closest('tr');
	var edit_id = jQuery(post_to_edit).attr('data-ref');
	var class_name = jQuery(post_to_edit).find('span.classname').text();
	var class_grade = jQuery(post_to_edit).find('span.grade').text();
	var text = '<tr class="row-update"><td colspan="5">\
			<div class="uc-metabox">\
				<div class="uc-field uc-textbox uc-meta-single" id="">\
					<div class="label"><label>Class Name</label></div>\
					<div class="field">\
						<div class="input"><input type="text" name="uc-class-title-new" class="uc-input input-large uc-class-title-update" value="'+class_name+'"></div>\
					</div>\
				</div>\
				<div class="uc-field uc-textbox uc-meta-single" id="">\
					<div class="label"><label>Grade</label></div>\
					<div class="field">\
						<div class="input">\
							<select name="uc-class-grade-new" class="uc-input select uc-class-grade-update">\
								<option value="">Various</option>';
	for (var i = 0; i < grades.length; i++)
		text +="<option value='"+grades[i]['value']+"' "+( grades[i]['value'] == class_grade ? 'selected="selected"' : '')+">"+grades[i]['label']+"</option>";
	text += '				</select>\
						</div>\
					</div>\
				</div>\
			</div>\
			<div class="alert-container"></div>\
			<input type="hidden" name="studentid" value="'+edit_id+'">\
			<input type="submit" class="button class-update" value="Update">\
			<button class="button class-remove" onclick="return false;">Remove Class</button>\
		</td></tr>';
	var edit_class = jQuery(text);
	jQuery( edit_class )
		.insertAfter(post_to_edit)
		.find('td')
		.animate({ paddingTop: 8, paddingBottom: 8 } )
		.wrapInner('<div style="display: none;" />')
		.parent()
		.find('td > div')
		.slideDown(400, function(){
			var $set = jQuery(this);
			$set.replaceWith($set.contents());
		});
	jQuery( edit_class ).find('.class-remove').bind("click", remove_post);

	jQuery( edit_class ).find('.class-update').click( function(e) {
		e.preventDefault();

		var wpnonce = jQuery('#update_classes_noncename').val();
		var readytosubmit = true;

		var new_class_name = jQuery(edit_class).find('.uc-class-title-update').val();
		var new_class_grade = jQuery(edit_class).find('.uc-class-grade-update').val();

		var data = {
				'action':'update_class',
				'classname' : new_class_name,
				'classgrade' : new_class_grade,
				'classid' : edit_id,
				'_wpnonce' : wpnonce,
		};

		if ( !new_class_name ) {
			display_alert( class_no_name_error, 'warning', edit_class );
		} else{
			if ( !new_class_grade ) {
				if ( confirm( class_verify_various ))
					readytosubmit = true;
				else
					readytosubmit = false;
			}

			if ( readytosubmit ) {
				jQuery.post(ajaxurl, data, function(response) {
					var parsed_response = jQuery.parseJSON( response );

					if ( parsed_response['status'] == 'OK' ) {

						var newclassname = parsed_response['meta']['classname'].replace(/\\/g, "");

						jQuery(post_to_edit).find('span.classname').text(newclassname);
						jQuery(post_to_edit).find('span.grade').text(parsed_response['meta']['classgrade']);
						display_alert( 'Class updated', 'success', edit_class );

					} else if ( parsed_response['status'] == 'REQUEST_DENIED', edit_class ) {
						display_alert( 'Error: ' + parsed_response['reason'], 'caution' );
					} else if ( parsed_response['status'] == 'INVALID_REQUEST' ) {
						display_alert( 'Error: Invalid request', 'caution', edit_class );
					} else {
						display_alert( 'Error: Unknown error', 'caution', edit_class  );
					}
				});
			}
		}
	});
}

function edit_student_post() {
	jQuery(this).unbind('click').bind("click", cancel_student_update).val('Close');
	var post_to_edit = jQuery(this).closest('tr');
	var edit_id = jQuery(post_to_edit).attr('data-ref');
	var student_first = jQuery(post_to_edit).find('span.firstname').text();
	var student_last = jQuery(post_to_edit).find('span.lastinitial').text();
	var student_grade = jQuery(post_to_edit).find('span.grade').text();
	var student_gender = jQuery(post_to_edit).find('span.gender').text();
	var student_ell = jQuery(post_to_edit).find('span.ell').text();
	var text = "<tr class='row-update'><td colspan='5'>\
						<form class='student-update-form' method='get' action='' novalidate='novalidate'>\
							<div class='uc-metabox'>\
								<div class='uc-field uc-textbox uc-meta-single'>\
									<div class='label'><label>First Name</label></div>\
									<div class='field'>\
										<div class='input'>\
											<input type='text' name='stfirstname' class='uc-input input-large uc-student-firstname-new' value='"+student_first+"'>\
										</div>\
									</div>\
								</div>\
								<div class='uc-field uc-textbox uc-meta-single'>\
									<div class='label'><label>Last Initial</label></div>\
									<div class='field'>\
										<div class='input'>\
											<input type='text' name='stlastinitial' class='uc-input input-large uc-student-lastinitial-new' value='"+student_last+"' maxlength='1'>\
										</div>\
									</div>\
								</div>\
								<div class='uc-field uc-textbox uc-meta-single'>\
									<div class='label'><label>Grade</label></div>\
										<div class='field'>\
											<div class='input'>\
												<select name='stgrade' class='uc-input select uc-student-grade-new'>";
	for (var i = 0; i < grades.length; i++)
		text +="<option value='"+grades[i]['value']+"' "+( grades[i]['value'] == student_grade ? 'selected="selected"' : '')+">"+grades[i]['label']+"</option>";
	text +="									</select>\
											</div>\
										</div>\
								</div>\
								<div class='uc-field uc-radio uc-meta-single'>\
									<div class='label'><label>Gender</label></div>\
									<div class='field'>\
										<div class='input'>";
	for (var i = 0; i < genders.length; i++)
		text +="<label><input class='uc-input' type='radio' name='stgender' value='"+genders[i]['value']+"' "+( genders[i]['label'] == student_gender ? 'checked="checked"' : '')+">"+genders[i]['label']+"</label>";
	text +="							</div>\
									</div>\
								</div>\
								<div class='uc-field uc-radio uc-meta-single'>\
									<div class='label'><label>Is this student learning English as a second language (ESL)?</label></div>\
									<div class='field'>\
										<div class='input'>";
	check_output = 'checked="checked"';
	this_output = '';
	for (var i = 0; i < yesnos.length; i++) {
		if ( yesnos[i]['label'] == student_ell ) {
			this_output = check_output;
			check_output = '';
		} else
			this_output = '';

		text +="<label><input class='uc-input' type='radio' name='stell' value='"+yesnos[i]['value']+"' "+this_output+">"+yesnos[i]['label']+"</label>";

	}
	text +="								<label><input class='uc-input' type='radio' name='stell' value='' "+check_output+">No answer</label>\
										</div>\
									</div>\
								</div>\
							</div>\
							<div class='alert-container'></div>\
							<input type='hidden' name='action' value='update_student'>\
							<input type='hidden' name='studentid' value='"+edit_id+"'>\
							<input type='submit' class='button student-update' value='Update'>\
							<button class='button student-remove' onclick='return false;'>Remove Student</button>\
						</form></td></tr>";
	var edit_student = jQuery(text);
	jQuery( edit_student )
		.insertAfter(post_to_edit)
		.find('td')
		.animate({ paddingTop: 8, paddingBottom: 8 } )
		.wrapInner('<div style="display: none;" />')
		.parent()
		.find('td > div')
		.slideDown(400, function(){
			var $set = jQuery(this);
			$set.replaceWith($set.contents());
		});
	jQuery(edit_student).find('.student-remove').bind("click", remove_post);
	var thisform = jQuery(edit_student).find('form.student-update-form');
	jQuery(thisform).validate({
		invalidHandler: function(event, validator) {
			var errors = validator.numberOfInvalids();
			if ( errors ) {
				var message = errors == 1
				? st_verify_error
				: st_verify_errors.replace('!fields!', errors );
				display_alert( message, 'warning', thisform );
			}
		},
		submitHandler: function( form ) {
			var wpnonce = jQuery('#update_students_noncename').val();

			var data = jQuery(form).serialize() + '&_wpnonce=' + wpnonce;
			jQuery.post(ajaxurl, data, function(response) {
				var parsed_response = jQuery.parseJSON( response );

				if ( parsed_response['status'] == 'OK' ) {

					if ( parsed_response['meta']['ell'] == null )
						ellDisplay = '';
					else
						ellDisplay = parsed_response['meta']['ell'];

					var newfirstname = parsed_response['meta']['firstname'].replace(/\\/g, "");
					var newlastinitial = parsed_response['meta']['lastinitial'].replace(/\\/g, "");

					jQuery(post_to_edit).find('span.firstname').text(newfirstname);
					jQuery(post_to_edit).find('span.lastinitial').text(newlastinitial);
					jQuery(post_to_edit).find('span.grade').text(parsed_response['meta']['grade']);
					jQuery(post_to_edit).find('span.gender').text(parsed_response['meta']['gender']);
					jQuery(post_to_edit).find('span.ell').text(ellDisplay);
					display_alert( 'Student updated', 'success', thisform );

				} else if ( parsed_response['status'] == 'REQUEST_DENIED' ) {
					display_alert( 'Error: ' + parsed_response['reason'], 'caution', thisform );
				} else if ( parsed_response['status'] == 'INVALID_REQUEST' ) {
					display_alert( 'Error: Invalid request', 'caution', thisform );
				} else {
					display_alert( 'Error: Unknown error', 'caution', thisform );
				}
			});
		},
	});
}

function handleStudentDrop( event, ui ) {
	drop_target = jQuery(this).find('.clear');
	jQuery(ui.draggable).css({'top': 0, 'left' : 0}).insertBefore(drop_target);
	jQuery(this).closest('.group-modify-container').attr('data-changed', '1').find('.uc-drag-container').each( function() {
		if ( jQuery(this).find('.uc-drag').length == 0 )
			jQuery(this).addClass('empty');
		else
			jQuery(this).removeClass('empty');
	});
}

/*
 * Display errors
 * Types: 'info', 'success', 'caution', 'warning'
 */
function display_alert( message, error_type, container, container_class = 'alert-container' ) {
	var error_types = ['info', 'success', 'caution', 'warning'];
	if ( !error_types.indexOf( error_type ) )
		error_type = 'info';

	if ( container == null )
		var alert_container = jQuery('.'+container_class);
	else
		var alert_container = jQuery(container).find('.'+container_class);

	console.log(alert_container);
	console.log(container_class);

	if ( !alert_container )
		return false;
	else {
		jQuery(alert_container).hide().html( '<div class="alert alert-'+error_type+'">'+message+'</div>').slideDown();
		return true;
	}
}

var escapeChars = { lt: '<', gt: '>', quot: '"', apos: "'", amp: '&' };

function unescapeHTML(str) {//modified from underscore.string and string.js
    return str.replace(/\&([^;]+);/g, function(entity, entityCode) {
        var match;

        if ( entityCode in escapeChars) {
            return escapeChars[entityCode];
        } else if ( match = entityCode.match(/^#x([\da-fA-F]+)$/)) {
            return String.fromCharCode(parseInt(match[1], 16));
        } else if ( match = entityCode.match(/^#(\d+)$/)) {
            return String.fromCharCode(~~match[1]);
        } else {
            return entity;
        }
    });
}