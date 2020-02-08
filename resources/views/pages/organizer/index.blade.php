@extends('template')
@section('content')
	<div class ="container-fluid">
		<div style="margin-top: 10px;display: none;" id="msg-success" class="alert alert-primary" role="alert">Success</div>
		<div style="margin-top: 10px;display: none;" id="msg-error" class="alert alert-danger" role="alert">Failed</div>
		<h1 class ="mt-4">Info Organizer</h1>
		<a class="btn btn-primary" href="{{URL::to('/')}}/organizer/form">Add</a>
		<hr/>
		<table class="table" id="data-table">
			<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">ID</th>
					<th scope="col">Organizer</th>
					<th scope="col" width="240">Image</th>
					<th scope="col">Operate</th>
				</tr>
			</thead>
		@if(count($data) > 0)
			<?php $no = 1; ?>
			@foreach($data as $result)
				<tr id="tr-{{$result->id}}">
					<td>{{ $no }}</td>
					<td>{{$result->id}}</td>
					<td>{{$result->organizerName}}</td>
					<td>{{$result->imageLocation}}</td>
					<td>
						<a class="btn btn-primary" href="{{ URL::to('/') }}/organizer/form/{{ $result->id }}">
							<i class="fa fa-pencil"></i>
						</a>
						<a class="btn btn-danger" href="javascript:;" onclick="confirmation('{{ $result->id }}');">
							<i class="fa fa-trash"></i>
						</a>
					</td>
				</tr>
			<?php $no++; ?>
			@endforeach
		@else
			<tbody>
				<tr>
					<td colspan="4">Tidak ada data</td>
				</tr>
			</tbody>
		@endif
	</div>
	@include('partials.modal')
@endsection

@push('scripts')
	<script type="text/javascript">
		function confirmation(id){
			$("#my_modal").modal("show");
			$("#btn-modal-save").html("Konfirmasi");
			$(".modal-title").html("Konfirmasi penghapusan");
			$(".modal-body").html(
				"Yakin untuk menghapus ?"+
				"<form id='form_process' action='#' method='post'>"+
				"<input type='hidden' value='"+id+"' name='id' />"+
				"</form>"
			);
		}

		$("#btn-modal-save").click(function(){
			document.getElementById('msg-success').style.display = "none";
			document.getElementById('msg-error').style.display = "none";

			document.getElementById('loading').style.display = "";
			document.getElementById('btn-modal-close').disabled = true;
			document.getElementById('btn-modal-save').disabled = true;

			$.ajax({
				type: "POST",
				url: "{{ URL('/') }}/api/v1/organizer/delete",
				data: JSON.stringify(encrypt_parameter({
					parameter : $("#form_process").serialize(),
				})),
	  			headers : {
					'Accept'      : 'appl csrfication/json',
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{csrf_token()}}',
	  			},
				success: function(response){
					console.log(response);
            		response = JSON.parse(response);
					if (response.code == 200) {
						document.getElementById('msg-success').style.display = "";
						$("#msg-success").html(response.message);
						$('table#data-table tr#tr-'+response.id).remove();
					}else{
						document.getElementById('msg-error').style.display = "";
						$("#msg-error").html(response.message);
					}
					document.getElementById('loading').style.display = "none";
					$("#my_modal").modal("hide");
					document.getElementById('btn-modal-close').disabled = false;
					document.getElementById('btn-modal-save').disabled = false;
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					document.getElementById('msg-error').style.display = "";
					$("msg-error").html("Error connection");
					document.getElementById('loading').style.display = "none";
					$("#my_modal").modal("hide");
					document.getElementById('btn-modal-close').disabled = false;
					document.getElementById('btn-modal-save').disabled = false;
				}
			});
		});
	</script>
@endpush