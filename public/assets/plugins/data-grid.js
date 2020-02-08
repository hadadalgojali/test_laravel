$(document).ready(function(){

	$("#grid-project").DataTable({
		"processing": true,
		"serverSide": true,
		"ajax": localStorage.getItem('url')+"/json/data/category",
		"columns": [
			{ "data": "category" },
			{ "data": "icon" },
			{
				"data": "id",
				"render": function (data, type, row) {
					return '<div align="center">'+
						'<a href="#myModal" data-toggle="modal" onclick="load_page(\'Confirmation\',\'confirm_delete_category\', '+row.id+');" class="btn btn-danger btn-sm" data-id="' + row.id + '"><span class="fa fa-trash"> </span></a>'+
						'<a href="#myModal" data-toggle="modal" onclick="load_page(\'Formulir\',\'form_category\', '+row.id+');" class="btn btn-success btn-sm" data-id="' + row.id + '"><span class="fa fa-pencil"> </span></a>'+
					'</div>'
				}
			}
		],
	});

});
