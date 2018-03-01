<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Engine Search</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
		<link rel="stylesheet"  href="public/css/list.css?<?php echo substr(md5(mt_rand()), 0, 7);?>" >
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
                
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ></script>
                
                <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	</head>
        
	<body id="track_page">
<!--		<div class="container">
                    <nav class="navbar navbar-default" style="margin-bottom: 5px;">
                        <div class="container-fluid">
                            <ul class="nav navbar-nav">
                                <li><a href="/">Home</a></li>
                            </ul>
                        </div>
                    </nav>
		</div>-->
				   
		<div class="container" style="min-height: 400px;">
                    @yield('content')
		</div>
                
                

<!--		<footer class="m-t">
			<div class="container">
				<div class="panel panel-default">
					<div class="panel-body text-center">
						<p>Copyright &copy; <a href="https://24x7studios.com" target="_blank">24x7studios.com</a> 2018</p>
					</div>
				</div>
			</div>
		</footer>-->
		
		<!-- end DVD code Modal -->
		
               
                
	</body>
</html>
