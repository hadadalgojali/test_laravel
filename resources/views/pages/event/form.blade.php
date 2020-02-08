@extends('template')
@section('content')
	<div class ="container-fluid">
		<div style="margin-top: 10px;display: none;" id="msg-success" class="alert alert-primary" role="alert">Success</div>
		<div style="margin-top: 10px;display: none;" id="msg-error" class="alert alert-danger" role="alert">Failed</div>

		<h1 class ="mt-4">Form Organizer</h1>
		<form action="#" id="form_process" method="POST">
			<input type="hidden" name="id" id="id" value="{{$id}}" />
			<div class="form-group">
				<label>Event name</label>
				<input type="text" placeholder="Event name" class="form-control" name="eventname" id="eventname" value="{{$eventname}}">
			</div>
			<div class="form-group">
				<label>Event Type</label>
				<input type="text" placeholder="Event Type" class="form-control" name="eventtype" id="eventtype" value="{{$eventtype}}">
			</div>
			<div class="form-group">
				<label>Event Date</label>
				<input type="text" placeholder="Event Date" class="form-control" name="eventdatetime" id="eventdatetime" value="{{$eventdatetime}}">
			</div>

			<div class="form-group">
				<label>Organizer</label>
				<select id="organizer" name="organizer">
					@foreach($organizer as $result)
						<option value="{{$result->id}}">{{$result->organizerName}}</option>
					@endforeach
				</select>
			</div>

		</form>
		<button class="btn btn-primary" id="btn-save">Simpan</button>
		<i id="loading" class="fa fa-spinner fa-pulse fa-fw" style="display: none;"></i>
	</div>
@endsection

@push('scripts')
	<script type="text/javascript">
		$("#btn-save").click(function(){
			document.getElementById('msg-success').style.display = "none";
			document.getElementById('msg-error').style.display = "none";

			document.getElementById('loading').style.display = "";
			document.getElementById('btn-save').disabled = true;
			var url = "";

			if ($("#id").val().length == 0) {
				url = "{{ URL('/') }}/api/v1/event/create";
			}else{
				url = "{{ URL('/') }}/api/v1/event/update";
			}

			$.ajax({
				type: "POST",
				url: url,
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
					}else{
						document.getElementById('msg-error').style.display = "";
						$("#msg-error").html(response.message);
					}
					document.getElementById('loading').style.display = "none";
					document.getElementById('btn-save').disabled = false;
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					document.getElementById('msg-error').style.display = "";
					$("msg-error").html("Error connection");
					document.getElementById('loading').style.display = "none";
					document.getElementById('btn-save').disabled = false;
				}
			});
		});

	</script>
@endpush