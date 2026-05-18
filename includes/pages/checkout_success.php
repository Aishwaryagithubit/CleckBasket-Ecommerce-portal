<?php include('../header.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Checkout Success</title>
	<meta http-equiv="refresh" content="2;url=/cleckbasket/includes/cart/invoice.php" />
	<style>
		body {
			font-family: 'Poppins', sans-serif;
			background: #f5f5f5;
			color: #222222;
		}
		.success-wrap {
			max-width: 720px;
			margin: 80px auto;
			background: #ffffff;
			border: 1px solid #ececec;
			border-radius: 12px;
			padding: 40px 32px;
			text-align: center;
		}
		.success-wrap h1 {
			margin-bottom: 12px;
			font-size: 28px;
		}
		.success-wrap p {
			color: #666666;
			margin-bottom: 18px;
		}
		.success-link {
			color: #5c3a21;
			text-decoration: none;
			font-weight: 600;
		}
	</style>
</head>
<body>
	<div class="success-wrap">
		<h1>Checkout Success</h1>
		<p>Your order has been placed successfully.</p>
		<p>Redirecting to invoice... <a class="success-link" href="/cleckbasket/includes/cart/invoice.php">View now</a></p>
	</div>

	<?php include('../footer.php'); ?>
</body>
</html>
