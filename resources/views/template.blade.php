<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Example') }}</title>

    <!-- begin::global styles -->
    <link rel="stylesheet" href="{{asset('assets/template/startbootstrap-simple-sidebar/vendor/bootstrap/css/bootstrap.min.css')}}" type="text/css">

    <link rel="stylesheet" href="{{asset('assets/template/startbootstrap-simple-sidebar/css/simple-sidebar.css')}}" type="text/css">

    <link rel="stylesheet" href="{{asset('assets/vendor/font-awesome-4.7.0/css/font-awesome.min.css')}}" type="text/css">

    <link rel="stylesheet" href="{{asset('assets/vendor/DataTables/datatables.min.css')}}" type="text/css">
</head>

<body>

  <div class="d-flex" id="wrapper">
    <!-- Menu Side bare -->
    @include('partials.menu')

    <!-- Page Content -->
    <div id="page-content-wrapper">

      <!-- Header menu bare -->
      @include('partials.header')
      @yield('content')
    </div>
    <!-- /#page-content-wrapper -->

  </div>

  <!-- /#wrapper -->

  <!-- Bootstrap core JavaScript -->
  <script src="{{asset('assets/template/startbootstrap-simple-sidebar/vendor/jquery/jquery.min.js')}}"></script>
  <script src="{{asset('assets/template/startbootstrap-simple-sidebar/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('assets/plugins/crypto-js.js')}}"></script>
  <script src="{{asset('assets/plugins/jsencrypt.js')}}"></script>
  <script type="text/javascript" src="{{asset('assets/vendor/DataTables/datatables.min.js')}}"></script>
  <!-- <script src="{{URL('/')}}/js/app.js" type="text/javascript"></script> -->

  @stack('scripts')
  <!-- Menu Toggle Script -->
  <script>
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
      let iv  = CryptoJS.lib.WordArray.random(16);
      let enc = CryptoJS.AES.encrypt(JSON.stringify(parameter), key, { iv: iv });
      let jse = new JSEncrypt();

      jse.setPublicKey(pubkey);
      let payload = {
        cipher  : enc.toString(),
        iv      : jse.encrypt(iv.toString(CryptoJS.enc.Base64)),
        key     : jse.encrypt(key.toString(CryptoJS.enc.Base64))
      };
      return payload;
    }

    $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
    });
  </script>

</body>

</html>
