// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Displays different views of the logs.
 *
 * @package    block_logreport
 * @copyright  2018 onwards Naveen kumar(naveen@eabyas.in)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 define(['jquery', 'block_logreport/datatables', 'block_logreport/select2'], function($, Datatable, select2){
 	/**
 	 *
 	 * Log report filter elements for table data
 	 *
 	 */
 	var logreport;
 	var FILTER = {
 		FORM: '.logselecform',
 		COURSE: '#menuid',
 		MODULE: '#menumodid',
 		USER: '#menuuser',
 		CLEAR: '#lr_clearfilter'
 	};	
 	
 	return  logreport = {
 		/**
 		 * Initialization function to setup filters 
 		 * and dependent data based on course to activities and users.
 		 */
 		
 		Init: function() {
 			/*----------  Intialize selcet to for all the select box enabled data-select2  ----------*/
 			
 			$("select[data-select2='1']").select2()

 			/**
 			 *
 			 * Clear option always important to reset the filters and view complete data.
 			 *
 			 */
 			
 			$(FILTER.CLEAR).on('click', function(e){
 				$(FILTER.FORM).trigger("reset");
 				$(FILTER.COURSE).trigger("change");
 				$("select[data-select2='1']").select2("destroy").select2();
 				$('.generaltable[data-datatable=true]').DataTable().destroy();
 				logreport.InitDatatable();
 			});
 			/**
 			 *
 			 * On change of course, filter options will change for users, activities and groups
 			 *
 			 */
 			
 			$(FILTER.COURSE).on("change", function(e) {
			  $(FILTER.MODULE).find('option').not(':first').remove();
			  $(FILTER.MODULE).find('optgroup').remove();
			  /**
			   *
			   * React only on course filter change
			   *
			   */
                if($(this).hasClass('cousrefilter')){
                	var courseid = $(this).val();
                	/*----------  XHR request to get course related activties, users and groups  ----------*/
                	$.ajax({
					  url: M.cfg.wwwroot + "/blocks/logreport/ajax.php?courseid="+ courseid
					}).done(function(data) {
					  var response = data;
					 /*==========================================================================
					 =            Course related activities selection with optgroups            =
					 ==========================================================================*/
					 
					  /*----------  For site level course, only one option available, no optgroups ----------*/
					  if(courseid == 1){
					  	$.each(response.activities, function(id, name){
			  				$('<option value='+ id +'>'+ name +'</option>').appendTo($(FILTER.MODULE));
				  		})
					  }else{
					  	$.each(response.activities, function(key, val){
						  	$.each(val, function(topic, activities){
						  		var $optgroup = $("<optgroup>", {label: topic});
						  		$($optgroup).appendTo($($(FILTER.MODULE)));
					  			$.each(activities, function(id, name){
					  				$('<option value='+ id +'>'+ name +'</option>').appendTo($optgroup);
						  		})
						  	})
					  	});
					  }
					  /*=====  End of Course related activities selection with optgroups  ======*/
					  
					  /*----------  Destroy and reintialize select2  ----------*/
					  $(FILTER.MODULE).select2("destroy").select2();
					});
                }
            });
 		},
 		/**
 		 *
 		 * Process Filter for Log report table, and reinitialize datatable.
 		 *
 		 */
 		ProcessFilter: function(){
 			$(".logselecform").submit(function(e){
 				e.preventDefault();
 				var filters = $(this).serializeArray();
 				$('.generaltable[data-datatable=true]').DataTable().destroy();
 				logreport.InitDatatable(filters);
 			})
 		},
 		/**
 		 *
 		 * Intitialize datatable with selected filtersand process the data serverside
 		 *
 		 */
 		InitDatatable: function(filters){
 			var params = {};
 			if(typeof filters == 'undefined'){
 				var params = {};
 			}else{
 				$.each(filters, function(k,v){
 					params[v.name] = v.value;
 				})
 			}
 			$('.generaltable[data-datatable=true]').DataTable({
 				"dom":'<l<t>ip>',
 				"ordering": false,
 				"processing": true,
		        "serverSide": true,
		        "ajax":{
		        	type: "POST",
		        	url: M.cfg.wwwroot+"/blocks/logreport/server_processing.php",
		        	data: params
		        } 
 			});
 		}
 	}
 });
