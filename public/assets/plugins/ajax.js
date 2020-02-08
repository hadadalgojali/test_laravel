if(typeof(Storage) !== 'undefined') {
	if(localStorage.getItem('pubkey') === null) {
		jQuery.get('/publickey.txt', (data) => {
			localStorage.setItem('pubkey', data);
		});
	}
}else{
	console.log('Penyimpanan lokal tidak tersedia dalam browser ini');
}

	function encrypt_parameter(variable){
		let parameter = variable;

		// let pubkey = null;

		let pubkey = localStorage.getItem('pubkey');
		// let pubkey = localStorage.getItem('pubkey');
		if(pubkey === null) {
			return false;
		}
		let key = CryptoJS.lib.WordArray.random(16);
		let iv 	= CryptoJS.lib.WordArray.random(16);
		let enc = CryptoJS.AES.encrypt(JSON.stringify(parameter), key, { iv: iv });
		let jse = new JSEncrypt();

		jse.setPublicKey(pubkey);
		let payload = {
			cipher 	: enc.toString(),
			iv 		  : jse.encrypt(iv.toString(CryptoJS.enc.Base64)),
			key 	  : jse.encrypt(key.toString(CryptoJS.enc.Base64))
		};
		return payload;
	}

function process(parameter){
	$('#modal-btn-confirm').html('Simpan <i class="fa fa-spinner fa-spin fa-fw"></i>').prop('disabled', true);
	$('#modal-btn-delete').html('Hapus <i class="fa fa-spinner fa-spin fa-fw"></i>').prop('disabled', true);
  	if (parameter == "delete") {
  		$.ajax({
			url:localStorage.getItem('url')+"/cpanel/p/"+$(".modal-body")[0].children[0].value,
			type:"POST",
  			headers : {
  				'Accept'      : 'appl csrfication/json',
  				'Content-Type': 'application/json',
  				'X-CSRF-TOKEN': reactInit.csrf_token,
  			},
			data:JSON.stringify(encrypt_parameter({ id : $(".modal-body")[0].children[1].value})),
			success:function(response) {
            response = JSON.parse(response);
  			if (response.code == 200) {
				if (response.response.reload === true && response.response.reload_panel == false) {
					window.location.reload();
				}else if(response.response.reload === false && response.response.reload_panel == true){
					toastr.success(response.message);
            		get_panel(response.response.link, response.response.parameter, response.response.panel);
				}else{
					toastr.success(response.message);
				}
				document.getElementById("modal-btn-close").click();
  			}else{
				toastr.warning(response.message);
			}
			$('#modal-btn-confirm').html('Simpan').prop('disabled', false);
			$('#modal-btn-delete').html('Hapus').prop('disabled', false);
        },
		error:function(response){
			toastr.error(response);
			$('#modal-btn-confirm').html('Simpan').prop('disabled', false);
			$('#modal-btn-delete').html('Hapus').prop('disabled', false);
		}
    });
  }else{
  		$.ajax({
			url:localStorage.getItem('url')+"/process/"+$(".modal-body")[0].children[0].value,
			type:"POST",
  			headers : {
  				'Accept'      : 'appl csrfication/json',
  				'Content-Type': 'application/json',
  				'X-CSRF-TOKEN': reactInit.csrf_token,
			},
			data:JSON.stringify(encrypt_parameter($("#form_process").serialize())),
			success:function(response) {
            	response = JSON.parse(response);
  				if (response.code == 200) {
					if (response.response.reload === true && response.response.reload_panel == false) {
					  	window.location.reload();
					}else if(response.response.reload === false && response.response.reload_panel == true){
						toastr.success(response.message);
                		get_panel(response.response.link, response.response.parameter, response.response.panel);
					}else{
						toastr.success(response.message);
					}
					document.getElementById("modal-btn-close").click();
  				}else{
					toastr.warning(response.message);
				}
				$('#modal-btn-confirm').html('Simpan').prop('disabled', false);
				$('#modal-btn-delete').html('Hapus').prop('disabled', false);
         	},
			error:function(response){
				toastr.error(response);
				$('#modal-btn-confirm').html('Simpan').prop('disabled', false);
				$('#modal-btn-delete').html('Hapus').prop('disabled', false);
			}
      	});
	}
}

function confirm_delete(link, id){
  hide_button();
  document.getElementById('modal-btn-delete').style.display  = '';
  $("#exampleModalCenterTitle").html('Konfirmasi');
  $(".modal-body").html(''+
    '<input type="hidden" value="'+link+'" name="link" id="link">'+
    '<input type="hidden" value="'+id+'" name="id" id="id">'+
    '<p>Anda yakin untuk menghapus data ini ?</p>'+
  '');
}

function get_form(link, token, id = null){
  hide_button();
  document.getElementById('modal-btn-confirm').style.display  = '';
  $("#exampleModalCenterTitle").html('Formulir #'+token);
	$(".modal-body").html('<i class="fa fa-circle-o-notch fa-lg fa-spin"></i>');
	$.ajax({
		url:link,
		type:"POST",
		headers : {
			'X-CSRF-TOKEN': reactInit.csrf_token,
		},
		data:JSON.stringify(encrypt_parameter({ token : token, link : link, id:id})),
		success:function(response) {
			 $(".modal-body").html(response);
		},
		error:function(response){
			// console.log(response);
			toastr.error("Connection failed : please retry again");
		}
	});
}

function get_panel(link, parameter, panel){
  $(panel).html('<i class="fa fa-circle-o-notch fa-lg fa-spin"></i>');
  $.ajax({
    url:localStorage.getItem('url')+link,
    type:"POST",
    headers : {
      'X-CSRF-TOKEN': reactInit.csrf_token,
    },
    data:JSON.stringify(encrypt_parameter(parameter)),
    success:function(response) {
      $(panel).html(response);
    },
	error:function(response){
		toastr.error(response);
	}
  });
}

function reset_modal(){
	$(".modal-dialog").removeClass("modal-lg");
	document.getElementById("modal-btn-confirm").disabled = false; 
	document.getElementById("modal-btn-delete").disabled  = false; 
}
function hide_button(){
	document.getElementById('modal-btn-close').style.display   = '';
	document.getElementById('modal-btn-confirm').style.display = 'none';
	document.getElementById('modal-btn-delete').style.display  = 'none';
}


    	function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
			try {
			decimalCount = Math.abs(decimalCount);
			decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
			const negativeSign = amount < 0 ? "-" : "";
			
			let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
			let j = (i.length > 3) ? i.length % 3 : 0;
			
			return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
			} catch (e) {
				console.log(e)
			}
		}