// WPGear. List-Forms for Gravity
// gravity-list-forms_gv.js
	
	window.addEventListener ('load', function() {
		console.log('list_forms_gravity GV Loaded.');
		
		var Forms = new Array ();
		var Table_Forms = document.getElementById("the-list");		
		
		if (Table_Forms) {
			// Message Box
			var Table_Message_Box = document.createElement("div");
			var Attribute_ID = document.createAttribute("id");
			var Attribute_Class = document.createAttribute("class");
			Attribute_ID.value = 'list_forms_gravity-messagebox';
			Attribute_Class.value = 'list_forms_gravity-messagebox';
			Table_Message_Box.setAttributeNode(Attribute_ID);
			Table_Message_Box.setAttributeNode(Attribute_Class);
			Table_Message_Box.innerHTML = '... get Notes processing ...';
			Table_Forms.insertAdjacentElement("beforeBegin", Table_Message_Box);
			
			for (i = 0; i < Table_Forms.children.length; i++) {
				var Row = Table_Forms.children[i];
				var Form_ID = Row.querySelectorAll('[id^=cb-select-]')[0].value;
	
				var Attribute_Name = document.createAttribute("id");
				Attribute_Name.value = 'list_forms_gravity_' + Form_ID;
				Row.setAttributeNode(Attribute_Name); 				
				
				Forms.push(Form_ID);
			}

			var WP_Ajax_URL = ajaxurl;
			var WP_Ajax_Data = 'action=list_forms_gravity&mode=get_forms_description&forms=' + Forms + '&type=gv';

			jQuery.ajax({
				type:"POST",
				url: WP_Ajax_URL,
				dataType: 'json',
				data: WP_Ajax_Data,
				cache: false,
				success: function(jsondata) {
					var Obj_Request = jsondata;	
					
					var Status	= Obj_Request.status;
					var Answer 	= Obj_Request.answer;					
					var Notes  	= Obj_Request.notes;
					
					if (Notes) {	
						var Count_Columns = Table_Forms.rows[0].cells.length
				
						for (i = 0; i < Notes.length; i++) {
							var Form_ID 		= Notes[i].id;
							var Note_Content 	= Notes[i].note;
							
							var Row = document.getElementById("list_forms_gravity_" + Form_ID);
							
							var Note_Box = document.createElement("tr");
							var Attribute_ID 	= document.createAttribute("id");
							Attribute_ID.value = 'list_forms_gravity_note-box_' + Form_ID;
							Note_Box.setAttributeNode(Attribute_ID);
							Row.insertAdjacentElement("afterend", Note_Box);

							// if (Note_Content) {
								var Note_Label = "Note: ";
								var Note = '';
								
								Note +=	'<td colspan ="' + Count_Columns + '" class="list_forms_gravity-box">';
								Note +=	'<span title="This is View Description. To Edit - go to Veiw Settings.">';
								Note +=	Note_Label;
								Note +=	'</span>';
								Note +=	'<span>';
								Note +=	Note_Content;
								Note +=	'</span>';
								Note +=	'</td>';
												
								Note_Box.innerHTML = Note;								
							// }
						}
						Table_Message_Box.style.display = 'none';
					}
				}
			});				
		}			
	});