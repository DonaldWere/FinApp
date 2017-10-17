<! DOCTYPE html>
<html>
	<head>
		<style>
			table {
				width: 100%;
				border-collapse: collapse;
			}
			table, td, th {
				border:1px solid black;
				padding: 5px;
			}
			th {
				text-align:left;
			}
		</style>
	</head>
	<body>
		<?php
			$q =$_GET['q'];
			include_once('includes/db_connect.php');
			$result=mysqli_query($con,"SELECT * FROM deposits WHERE name='".$q"'");
			echo "<table><tr><th>Month</th><th>Amount</th></tr>";
			while ($row = mysqli_fetch_array($result)){
				echo "<tr><td>".$row['month']."</td><td>".$row['amount']."</td></tr>";
				}
			echo "</table>";
		?>
	</body>
</html>