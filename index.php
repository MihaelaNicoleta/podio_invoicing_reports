<html>
<head>
    <title>Podio Report</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link href='https://fonts.googleapis.com/css?family=Comfortaa|Source+Sans+Pro' rel='stylesheet' type='text/css'>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
</head>
<body>
	<div id="wrapper">
		<div class="title">
			<h1>Podio invoice</h1>
		</div>
    <?php
    require_once 'api/PodioAPI.php';
    require_once 'config.php';
    require_once 'functions.php';
    require_once 'session.php';


    $api = new Podio($client_id, $client_secret);
    Podio::setup($client_id, $client_secret,  array( "session_manager" => "PodioBrowserSession"));

    if (!isset($_GET['code']) && !$api->is_authenticated()) {
        $auth_url = htmlentities('https://podio.com/oauth/authorize?response_type=code&client_id='.$client_id.'&redirect_uri='.rawurlencode($redirect_uri)."&scope=".rawurlencode($scope));
        ?>
		
		<div id="content">
			<p>You need to authenticate on Podio</p>			
			<a href="<?php echo $auth_url; ?>" class="button">Login</a>		
		</div>
        <?php
    }
    elseif (isset($_GET['code']) || $api->is_authenticated()) {
    if (isset($_GET['error'])) {
        print "There was a problem. The server said: {$_GET['error_description']}";
    }
    else {
    if(isset($_GET['code'])) {
        $api->authenticate('authorization_code', array('code' => $_GET['code'], 'redirect_uri' => $redirect_uri));
    }
    ?>
	
	<div id="content">
		<p>Select workspace</p>
		<form id="workspaces" method="GET" action="">
			<select id="workspace" name="workspace">
				<?php
					foreach($workspaces as $app_short_name => $app_name) { ?>
						<option value="<?php echo $app_short_name; ?>" <?php echo ($app_short_name == $_GET['workspace']) ? selected : '';?>><?php echo $app_name;?></option>               
				<?php } ?>
			</select>			
		</form>
	
        <?php
            $workspace = $_GET['workspace'];
            if($workspace) {
                $app_id = get_app_id($workspace);
                $total_hours = get_total_hours($app_id);
        ?>

            <div id="results">
                <p>Total hours: <?php echo !empty($total_hours['total']) ? $total_hours['total'] : 0;?></p>                
                <p>Not invoiced hours:  <?php echo !empty($total_hours['not_invoiced_hours']) ? $total_hours['not_invoiced_hours'] : 0;?></p>                
                <p>Invoiced hours:  <?php echo !empty($total_hours['invoiced_hours']) ? $total_hours['invoiced_hours'] : 0;?></p>
            </div>
	</div>
            <?php
            }
    }

}
            ?>
	</div>
    </body>
	
	<script>
		$( document ).ready(function() {
			$('select#workspace').on('change', function() {			
				$('form#workspaces').submit();			
			});
		});
		
	</script>
	
</html>

