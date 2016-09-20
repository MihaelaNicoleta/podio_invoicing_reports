<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Podio Report</title>
		<link rel="stylesheet" type="text/css" href="style.css">
		<link href='https://fonts.googleapis.com/css?family=Comfortaa' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css'>
	</head>
	<body>
		<div id="wrapper">
			<div class="title">
				<h1>Podio invoice</h1>
			</div>
            <div id="content">

		<?php
			require_once 'api/PodioAPI.php';
			require_once 'config.php';
			require_once 'functions.php';
			require_once 'PodioBrowserSession.php';


			$api = new Podio($client_id, $client_secret);
			Podio::setup($client_id, $client_secret, array( "session_manager" => "PodioBrowserSession"));

			if (!isset($_GET['code']) && !$api->is_authenticated()) {
				$auth_url = htmlentities('https://podio.com/oauth/authorize?response_type=code&client_id='.$client_id.'&redirect_uri='.rawurlencode($redirect_uri)."&scope=".rawurlencode($scope));
		?>
                <div id="login_body">
                    <p>You need to authenticate on Podio</p>
                    <a href="<?php echo $auth_url; ?>" class="button">Login</a>
                </div>

		<?php
			}
			elseif (isset($_GET['code']) || $api->is_authenticated()) {
				if (isset($_GET['error'])) {
					echo "<p class='error'>There was a problem. The server said: {$_GET['error_description']}</p>";
				}
				else {
					if(isset($_GET['code'])) {
						try {
							$api->authenticate('authorization_code', array('code' => $_GET['code'], 'redirect_uri' => $redirect_uri));
						}
						catch(PodioError $e) {
							echo "<p class='error'>There was an error. The API responded with the error type <b>{$e->body['error']}</b> and the message <b>{$e->body['error_description']} <a href='".$redirect_uri."'>Retry</a></p>";
						}
					}
		?>

                    <?php
                    foreach($workspaces as $app_short_name => $app_name) {
                        $app_id = get_app_id($app_short_name);
                        $total_hours = get_total_hours($app_id);

                        if($total_hours) { ?>
                            <div class="results">
                                <h2><?php echo $app_name; ?></h2>
                                <p>Total hours: <span><?php echo !empty($total_hours['total']) ? $total_hours['total'] : 0;?> </span></p>
                                <p>Invoiced hours:  <span><?php echo !empty($total_hours['invoiced_hours']) ? $total_hours['invoiced_hours'] : 0;?> </span></p>
                                <p>Not invoiced hours:  <span><?php echo !empty($total_hours['not_invoiced_hours']) ? $total_hours['not_invoiced_hours'] : 0;?> </span></p>
                            </div>
                   <?php }
                    } ?>

		<?php
				}

			}
		?>

		</div>
    </body>
</html>

