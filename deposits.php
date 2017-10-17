<?php
include_once('includes/db_connect.php');

?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8">
<title>FinApp | Deposits</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="shortcut icon" href="images/logo/favicon.png">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="engine1/style.css" />
<script type="text/javascript" src="engine1/jquery.js"></script>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/custom.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/custom,js"></script>
    <script>
    $(document).ready(function() {
    $("header ul [href]").each(function() {
    if (this.href.split("?")[0] == window.location.href.split("?")[0]) {
        $(this).addClass("active");
        }
    });
         $("#mobile_menu").click(function(){
        $("nav").slideToggle("slow");
    });
});
    </script>
	<script type="text/javascript">
	function updateFilter(min, max) {
		$('#myTable > tbody > tr > td:nth-child(4)').each(function(){
			var scoreCheck = +$(this).text();
			$(this).closest('tr').toggle(min <= scoreCheck && scoreCheck <= max);
		});
	}
	$
</script>
<script type="text/javascript">
function ConfirmDelete(){
    var d = confirm('Do you really want to delete data?');
    if(d == false){
        return false;
    }
}
</script>
<script type="text/javascript">
	function mymodal(){
// Get the modal
var modal = document.getElementById("myModal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
    modal.style.display = "block";

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
}
</script>
<script>
	function memberFunction(str){
		if (str == " "){
			document.getElementById("memberTable").innerHTML = " ";
				return;
		}else{
			if (window.XMLHttpRequest){
				xmlhttp = new XMLHttpRequest ();
			}
			xmlhttp.onreadystatechange = function(){
				if (this.readyState == 4 && this.status == 200){
					document.getElementById("memberTable").innerHTML = this.responseText;
				}
			};
			xmlhttp.open("GET","getmember.php?q="+str,true);
		}
	}
</script>
</head>
<body>
<?php include_once("includes/header.php"); ?> 

<div class="container"> 
<div>
<div class="main1">
		<h3>Savings and Net Worth</h3>
	<?php	
		// connect to database
		include ('includes/db_connect.php');
		
		//number of results to show per page
		$per_page = 6;
		
		//figure out the total pages in the database
		if ($result = mysqli_query($con, "SELECT DISTINCT(name), parent FROM deposits ORDER BY parent")){
			if ($result->num_rows !=0){
				$total_results = $result->num_rows;
				
				//ceil() returns the next highest integer value by rounding up value if necessary
				$total_pages = ceil($total_results / $per_page);
				
				//check if the 'page' variable is set in the URL (ex: view_paginated.php?page=1)
				if(isset($_GET['page']) && is_numeric($_GET['page']) ){
					$show_page = $_GET['page'];
					
					//make sure the $show_page value is valid
					if ($show_page > 0 && $show_page <= $total_pages){
						$start = ($show_page -1) * $per_page;
						$end = $start + $per_page;
						
						if ($show_page >= 1 && $show_page < $total_pages){
							$entries = $show_page * $per_page;
						}elseif ($show_page == $total_pages){
							$entries = $total_results;
						}elseif($show == " "){
							$entries = $per_page;
						}
						
					} else {
						// error - show first set of results
						$start = 0;
						$end = $per_page;
					}
				} else {
					// if page isn't set, show first set of results 
					$start = 0;
					$end = $per_page;
				}
				
				//display pagination
				echo "<p><b>View Page: </b>";
				for ($i = 1; $i <= $total_pages; $i++){
					if (isset($_GET['page']) && $_GET['page'] == $i){
						echo $i . " ";
					} else {
						echo "<ul class='pagination'><li><a href='deposits.php?page=$i'>$i</a></li></ul>";
					}
				}
				echo "</p>";
				
				// display data in table
				echo "<table border='1' cellpadding='10' class='table table-responsive'>";
				echo "<tr><th>#</th><th>Name:</th><th>Parent:</th><th>Amount</th><th> Net Worth</th></tr>";
				
				//loop through results of database query, displaying the in the table
				for ($i = $start; $i < $end; $i++){
					//make sure that PHP doesn't try to show results that do not exist
					if ($i == $total_results) {break; }
					
					//find specific row
					$result->data_seek($i);
					$row = $result->fetch_row();
					
					//calculate the total contributions per member
					$sum = mysqli_query($con, "SELECT SUM(amount) FROM deposits WHERE name='$row[0]'");
					$total = mysqli_query($con, "SELECT SUM(amount) FROM deposits");
					$sum1 = mysqli_fetch_row($sum);
					$total1 = mysqli_fetch_row($total);
					
					//echo out the contents of each row into a table
					echo "<tr class='success'>";
					echo '<td></td>';
					echo '<td>' . $row[0] . '</td>';
					echo '<td>' . $row[1] . '</td>';
					echo '<td>' . $sum1[0]. '</td>';
					echo '<td></td>';
					echo "</tr>";
				}
				// close table
				echo "</table>";
			
				echo "<p><b>Showing " . $entries . " of " . $total_results . " Entries </b></p>";
					echo "<p><b>Total Deposits: Ksh".$total1[0]."</b></p>";
			}else{
			echo "No results to display!";
			}
		} else{
			//error with the query
			echo "Error: " . $con->error;
		}
?>
<?php
if(isset($_POST['newdeposit'])){
    $name=mysqli_real_escape_string($con, $_POST['name']);
    $amount=mysqli_real_escape_string($con, $_POST['amount']);
	$month=mysqli_real_escape_string($con, $_POST['month']);
	$result = mysqli_query($con, "SELECT * FROM allmember WHERE name='$name' ");
	$row= mysqli_fetch_array($result);
	$parent=$row["parent"]; 
	
		mysqli_query($con,"INSERT INTO deposits (name,parent,amount,month) VALUES('$name','$parent','$amount','$month')");
	echo"(
				<script>
				 window.alert('Confirmed. A deposit of Ksh$amount for $name was successful');
				 window.location.href=('deposits.php');
				 </script>
					)"; 
}
?>
		<a class="btn btn-info"  onClick="return mymodal();" id="myBtn" class="link-butn">Add Deposit</a>
	</div>	
	<div class="mainside">
	<div class="mainside1">
	<p><b><h4> Member Specific Contributions </h4></b></p>
	<form action="deposits.php" method="post">
		<div class="form-group">
							<select name="name" class="form-control" id="name" >
								<option selected="selected">Select Name</option>
								<?php
									$row = mysqli_query($con, "SELECT DISTINCT(name) FROM deposits ORDER BY name");
									while($result = mysqli_fetch_array($row))
									{ 
									?>
										<option value="<?php echo $result["name"]; ?>"><?php echo $result['name']; ?></option>
										<?php 
									}
								?>
						</select>
						<button type="submit" name= "getmember" class="btn btn-primary">Get</button>
						</form>
						</div>
			<div id="memberTable">
				<?php
					if(isset($_POST['getmember'])){
						$name=mysqli_real_escape_string($con, $_POST['name']);
										
							$result=mysqli_query($con,"SELECT * FROM deposits WHERE name='$name'");
							$result2=mysqli_fetch_array(mysqli_query($con,"SELECT SUM(amount) FROM deposits WHERE name='$name'"));
							if ($result->num_rows !=0){
									echo "<p><b>".$name."</b></p>";
							echo "<table border='1' cellpadding='10' class='table table-responsive'><tr><th>Month</th><th>Amount</th></tr>";
							while ($row = mysqli_fetch_array($result)){
								echo "<tr class='success'><td>".$row['month']."</td><td>".$row['amount']."</td></tr>";
								}
							echo "<tr class='success'><td><b>Total</td><td>".$result2[0]."</b></td></tr>";
							echo "</table>";
									}else{
									echo "<p>".$name." has not made any contributions</p>";
									}
								}
					?>
			</div>
	</div>
	<div class="mainside1">
	<p><b><h4> Month Specific Contributions </h4></b></p>
	<form action="deposits.php" method="post">
		<div class="form-group">
							<select name="month" class="form-control" id="month" >
								<option selected="selected">Select Month</option>
								<?php
									$row = mysqli_query($con, "SELECT DISTINCT(month) FROM deposits");
									while($result = mysqli_fetch_array($row))
									{ 
									?>
										<option value="<?php echo $result["month"]; ?>"><?php echo $result['month']; ?></option>
										<?php 
									}
								?>
						</select>
						<button type="submit" name= "getmonth" class="btn btn-primary">Get</button>
						</form>
						</div>
			<div id="memberTable">
				<?php
					if(isset($_POST['getmonth'])){
						$month=mysqli_real_escape_string($con, $_POST['month']);
										
							$result=mysqli_query($con,"SELECT * FROM deposits WHERE month='$month'");
							$result2=mysqli_fetch_array(mysqli_query($con,"SELECT SUM(amount) FROM deposits WHERE month='$month'"));
							echo "<p><b>".$month."</b></p>";
							echo "<table border='1' cellpadding='10' class='table table-responsive'><tr><th>Name</th><th>Amount</th></tr>";
							while ($row = mysqli_fetch_array($result)){
								echo "<tr class='success'><td>".$row['name']."</td><td>".$row['amount']."</td></tr>";
								}
								echo "<tr class='success'><td><b>Total</td><td>".$result2[0]."</b></td></tr>";
							echo "</table>";
								
								}
					?>
			</div>
	</div>
	</div>
</div>
			
			<!-- The Modal -->
	<div id="myModal" class="modal">

			  <!-- Modal content -->
			  <div class="modal-content">
					<div class="modal-header">
						<span class="close">&times;</span>
						<h2>Add New Deposit</h2>
					 </div>
					 <div class="modal-body">

					<form action="deposits.php" method="post">
						<div class="form-group">
							<select name="name" class="form-control">
								<option selected="selected">Select Name</option>
								<?php
									$row = mysqli_query($con, "SELECT name FROM allmember ORDER BY name");
									while($result = mysqli_fetch_array($row))
									{ 
									?>
										<option value="<?php echo $result["name"]; ?>"><?php echo $result['name']; ?></option>
										<?php 
									}
								?>
						</select>
						</div>
						<div class="form-group">
							<input type="text" name="amount" class="form-control"  placeholder="Enter Amount"required /> <br/>
						</div>
						 <div class="form-group">
						  <select class="form-control" name="month">
						  <option>Select A Month</option>
							<option value="January">January</option>
							<option value="February">February</option>
							<option value="March">March</option>
							<option value="April">April</option>
							<option value="May">May</option>
							<option value="June">June</option>
							<option value="July">July</option>
							<option value="August">August</option>
							<option value="September">September</option>
							<option value="October">October</option>
							<option value="November">November</option>
							<option value="December">December</option>
						  </select>
						</div>
						<button type="submit" name= "newdeposit"class="btn btn-primary">Confirm</button>&nbsp;&nbsp;&nbsp;<button type="reset" class="btn btn-primary">Cancel</button>
					</form>
	
					 </div>
					 <div class="modal-footer">
						<h2>Thanks</h2>
					 </div>
			</div>

	</div>
	<div class="main2">
	<?php
		$total_jan= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(amount) FROM deposits WHERE month='January'"));
		$total_feb= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(amount) FROM deposits WHERE month='February'"));
		$total_mar= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(amount) FROM deposits WHERE month='March'"));
		$total_apr= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(amount) FROM deposits WHERE month='April'"));
		$total_may= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(amount) FROM deposits WHERE month='May'"));
		$total_jun= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(amount) FROM deposits WHERE month='June'"));
		$total_jul= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(amount) FROM deposits WHERE month='July'"));
		$total_aug= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(amount) FROM deposits WHERE month='August'"));
		$total_sep= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(amount) FROM deposits WHERE month='September'"));
		$total_oct= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(amount) FROM deposits WHERE month='October'"));
		$total_nov= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(amount) FROM deposits WHERE month='November'"));
		$total_dec= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(amount) FROM deposits WHERE month='December'"));

	echo "<h3>Monthly Contributions</h3>";
			echo "<table border='1' cellpadding='5' class='table table-responsive'>";
			echo "<tr><th>Jan</th><th>Feb</th><th>Mar</th><th>Apr</th><th>May</th><th>Jun</th><th>Jul</th><th>Aug</th><th>Sep</th><th>Oct</th><th>Nov</th><th>Dec</th><th>Total</th></tr>";
			echo "<tr><td>".$total_jan[0]."</td><td>".$total_feb[0]."</td><td>".$total_mar[0]."</td><td>".$total_apr[0]."</td><td>".$total_may[0]."</td><td>".$total_jun[0]."</td><td>".$total_jul[0]."</td><td>".$total_aug[0]."</td><td>".$total_sep[0]."</td><td>".$total_oct[0]."</td><td>".$total_nov[0]."</td><td>".$total_dec[0]."</td><td><b> Ksh ".$total1[0]."</b></td></tr>";
			echo "</table>";
		?>	
	</div>
</div>
<?php include_once("includes/footer.php"); ?>
</body>
</html>