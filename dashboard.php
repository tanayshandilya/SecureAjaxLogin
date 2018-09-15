<?php 
    session_start();
    if ( isset($_SESSION['LoginStatus']) && $_SESSION['LoginStatus'] == 1 ) {
        
    }else{
        unset($_SESSION);
        session_destroy();
        header('Location: /');
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">

    <title><?php echo $_SESSION['LoginUser']; ?> | Secure Ajax Login</title>
  </head>
  <body class="bg-light">
<?php if ( isset($_SESSION['LoginStatus']) && $_SESSION['LoginStatus'] == 1 ) { ?>
    <div class="card mx-auto login-card border-primary">
        <div class="card-body">
            <div class="text-center">
                <img src="https://via.placeholder.com/100x100" class="rounded-circle user-image border border-primary">
            </div>
            <br>
            <h5 class="text-center">
                Welcome <?php echo $_SESSION['LoginUser']; ?>
            </h5>
            <hr>
            <a href="/logout/" class="btn btn-block btn-outline-warning">Logout</a>
        </div>
    </div>
<?php }else{ ?>
    <h1 class="text-center" style="margin-top: 45vh">
        <i class="fas fa-spin fa-spinner" style="color: var(--primary)"></i>
    </h1>
<?php } ?>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/aes.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/enc-base64.min.js"></script>
    <script type="text/javascript" src="/assets/js/ajax.js"></script>
  </body>
</html>
