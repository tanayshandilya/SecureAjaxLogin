<?php

function ValidateToken($value='') {
	if ( strlen( $value ) > 100 ) {
		$value = base64_decode(str_replace('/[L,QSA]', null, $value));
		$value = json_decode( $value, true );
		if ( $value['LoginTime'] <= strtotime('now') ) {
			if ( $value['TokenTTL'] > strtotime('now') ) {
				if ( $value['TokenData']['LoginStatus'] == 1 ) {
					return true;
	 			}else{
	 				return 'Invalid Login Request';
	 			}
			}else{
				return 'Login Token Expired';
			}
		}else{
			return 'Invalid Login Token';
		}
	}else{
		return 'Invalid Login Token';
	}

}

function ParseToken( $token ){
	$token = base64_decode(str_replace('/[L,QSA]', null, $token));
	return json_decode( $token, true );
}

function ParseUrl($url) {
	$url = explode('/', $url);
	array_pop($url);
	return $url[0];
}

if ( isset($_GET['url']) && strlen(ParseUrl($_GET['url'])) > 50 ) {
	if ( ValidateToken( ParseUrl($_GET['url']) ) ) {
		$token = ParseToken(ParseUrl($_GET['url']));
		session_start();
		$_SESSION = [
			'LoginTime'   => $token['LoginTime'],
			'TokenTTL'    => $token['TokenTTL'],
			'LoginStatus' => $token['TokenData']['LoginStatus'],
			'LoginUser'   => $token['TokenData']['LoginUser'],
			'SessionID'   => $token['TokenData']['SessionID']
		];
		header('Location: /dashboard/');
	}
}elseif ( isset($_GET['url']) && ParseUrl($_GET['url']) == 'logout' ) {
	session_start();
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

    <title>Secure Ajax Login</title>
  </head>
  <body class="bg-light">
<?php if( !isset($_GET['url']) ) { ?>
	<div class="card mx-auto login-card border-primary">
    	<div class="card-body">
    		<h4 class="text-center">Secure Ajax Login</h4>
    		<hr>
    		<div class="alert alert-danger text-center" role="alert" id="ajax-error"></div>
    		<form class="needs-validation login-form" novalidate>
			  <div class="form-group">
			    <input type="email" class="form-control" name="email" aria-describedby="emailHelp" placeholder="Enter email" autocomplete="off" autosave="off" required>
			     <div class="invalid-tooltip" style="position: inherit;">
		          A valid email is required
		        </div>
			  </div>
			  <div class="form-group">
			    <input type="password" name="password" class="form-control" placeholder="Password" autocomplete="off" autosave="off" required>
			    <div class="invalid-tooltip" style="position: inherit;">
		          A valid password is required
		        </div>
			  </div>	
			  <button type="submit" class="btn btn-block btn-outline-primary">Submit</button>
			</form>
    	</div>
    </div>
<?php } if( isset($_GET['url']) && strlen(ParseUrl($_GET['url'])) > 50 ) { 
	if ( !ValidateToken(ParseUrl($_GET['url'])) ) { ?>
	<div class="alert alert-danget text-center" role="alert"><?php echo ValidateToken(ParseUrl($_GET['url'])); ?></div>
<?php }else{ ?>
	<h1 class="text-center" style="margin-top: 45vh">
		<i class="fas fa-spin fa-spinner" style="color: var(--primary)"></i>
	</h1>
<?php }
 } ?>

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
