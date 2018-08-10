<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta name="google-site-verification" content="h56ecF9DExm4zZ5PSDVRaUxO-UEgqCasmWx_Bkem2Ho" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dofuu Admin</title>
    <script>
     window.Laravel = <?php echo json_encode([
         'csrfToken' => csrf_token(),
         ]); ?>
     </script>
     <meta name="csrf-token" content="{{ csrf_token() }}">
     <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
     <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">
 </head>
 <body>
    <div id="app">
    </div>

    <script src="{{asset('js/app.js')}}"></script>
    <script>
        window.fbAsyncInit = function() {
            FB.init({
              appId      : '489765191458790',
              cookie     : true,
              xfbml      : true,
              version    : 'v3.0'
          });

            FB.AppEvents.logPageView();   

        };

        (function(d, s, id){
           var js, fjs = d.getElementsByTagName(s)[0];
           if (d.getElementById(id)) {return;}
           js = d.createElement(s); js.id = id;
           js.src = "https://connect.facebook.net/en_US/sdk.js";
           fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));
   </script>
</body>
</html>