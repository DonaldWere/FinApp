<?php
include_once('includes/db_connect.php');

?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8">
<title>FinApp | Loans</title>
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
</head>
<body>
<?php include_once("includes/header.php"); ?> 
<?php
if(isset($_POST['newtransaction'])){
    $name=mysqli_real_escape_string($con, $_POST['name']);
    $amount=mysqli_real_escape_string($con, $_POST['amount']);
	$type=mysqli_real_escape_string($con, $_POST['type']);
	
	if ($type == 'Credit'){
	
		//check if there is a loan balance
		$total_loan= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(net_amount) FROM loans WHERE parent='$name' && transaction='Debit'"));
		$total_repayment= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(net_amount) FROM loans WHERE parent='$name' && transaction='Credit'"));
		$balance=$total_loan[0] - $total_repayment[0];
		//do not accept another payment if there is no balance  or there is an overpayment
		if ($balance <= 0){
			echo"(
				<script>
				window.alert('$name has a loan balance of Ksh. 0. No payment is required');
				 window.location.href=('loans.php');
				 </script>
					)"; 
			}else{
			//make sure no overpayment above the loan amount
			if ($amount - $balance > 0){
				echo"(
				<script>
				window.alert('The payment exceeds the outstanding loan balance. Please pay Ksh. $balance only');
				 window.location.href=('loans.php');
				 </script>
					)"; 
			}else{
		$gross_amount = ($amount / 1.1);

		mysqli_query($con,"INSERT INTO loans (parent,transaction,gross_amount,net_amount) VALUES('$name','$type','$gross_amount','$amount')")  or die (mysqli_error($con)); 
			
			//calculate new loan balance
			$total_loan1= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(net_amount) FROM loans WHERE parent='$name' && transaction='Debit'"));
			$total_repayment1= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(net_amount) FROM loans WHERE parent='$name' && transaction='Credit'"));
			$balance1=$total_loan1[0] - $total_repayment1[0];
			
	echo"(
				<script>
				window.alert('Confirmed. $name has repaid Ksh $amount.Outstanding loan balance is Ksh.$balance1');
				 window.location.href=('loans.php');
				 </script>
					)"; 
			}
		}
	}else{
	
		//check if there is a loan balance
		$total_loan= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(net_amount) FROM loans WHERE parent='$name' && transaction='Debit'"));
		$total_repayment= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(net_amount) FROM loans WHERE parent='$name' && transaction='Credit'"));
		$balance=$total_loan[0] - $total_repayment[0];
		// if their is an unpaid loan, do not issue any more loan
				if (($balance/1.1) == 20000){
					echo"(
						<script>
						window.alert('$name has reached the Maximum loan limit of Ksh. 20,000. They therefore do not qualify for another loan');
						 window.location.href=('loans.php');
						 </script>
							)"; 
				}else{
				if(($amount+($balance/1.1)) > 20000) {
				
				$max= 20000 - ($balance/1.1);
					echo"(
						<script>
						window.alert('$name will exceed the maximum loan amount of Ksh. 20,000. The maximum they can borrow is Ksh. $max');
						 window.location.href=('loans.php');
						 </script>
							)"; 
				}else{
				$net_amount = ($amount * 1.1);
				
				mysqli_query($con,"INSERT INTO loans (parent,transaction,gross_amount,net_amount) VALUES('$name','$type','$amount','$net_amount')")  or die (mysqli_error($con)); 
				
				//calculate the new balance
				$total_loan2= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(net_amount) FROM loans WHERE parent='$name' && transaction='Debit'"));
				$total_repayment2= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(net_amount) FROM loans WHERE parent='$name' && transaction='Credit'"));
				$balance2=$total_loan2[0] - $total_repayment2[0];	
					
			echo"(
						<script>
						window.alert('Confirmed. $name has borrowed Ksh $amount.Outstanding loan balance is $balance2');
						 window.location.href=('loans.php');
						 </script>
							)"; 
					}
				}
	}
	}
?>

<div class="container"> 
<div>
<div class="main1">
		<h3>Loans and Repayments</h3>
	<?php	
		// connect to database
		include ('includes/db_connect.php');
		
		//number of results to show per page
		$per_page = 7;
		
		//figure out the total pages in the database
		if ($result = mysqli_query($con, "SELECT DISTINCT(parent) FROM loans ORDER BY parent")){
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
						echo "<ul class='pagination'><li><a href='loans.php?page=$i'>$i</a></li></ul>";
					}
				}
				echo "</p>";
				
				// display data in table
				echo "<table border='1' cellpadding='10' class='table table-responsive'>";
				echo "<tr><th>#</th><th>Name:</th><th>Loan Balance</th></tr>";
				
				//loop through results of database query, displaying the in the table
				for ($i = $start; $i < $end; $i++){
					//make sure that PHP doesn't try to show results that do not exist
					if ($i == $total_results) {break; }
					
					//find specific row
					$result->data_seek($i);
					$row = $result->fetch_row();
					
					//calculate the total contributions per member
					$total_loan= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(net_amount) FROM loans WHERE parent='$row[0]' && transaction='Debit'"));
					$total_repayment= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(net_amount) FROM loans WHERE parent='$row[0]' && transaction='Credit'"));
					$balance=$total_loan[0] - $total_repayment[0];
					
					
					$total_loan3= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(net_amount) FROM loans WHERE transaction='Debit'"));
					$total_repayment3= mysqli_fetch_row(mysqli_query($con, "SELECT SUM(net_amount) FROM loans WHERE transaction='Credit'"));
					$balance3=$total_loan3[0] - $total_repayment3[0];
					if ($balance > 0){
					//echo out the contents of each row into a table
					echo "<tr class='success'>";
					echo '<td></td>';
					echo '<td>' . $row[0] . '</td>';
					echo '<td>' . $balance. '</td>';
					echo "</tr>";
					}
				}
				// close table
				echo "</table>";
					echo "<p><b>Total Outstanding  Loan Ksh. ".$balance3."</b></p>";
			}else{
			echo "No results to display!";
			}
		} else{
			//error with the query
			echo "Error: " . $con->error;
		}
?>
<a class="btn btn-info"  onClick="return mymodal();" id="myBtn" class="link-butn">Make a Transaction</a>
	</div>	


			<!-- The Modal -->
	<div id="myModal" class="modal">

			  <!-- Modal content -->
			  <div class="modal-content">
					<div class="modal-header">
						<span class="close">&times;</span>
						<h2>Loan Transaction</h2>
					 </div>
					 <div class="modal-body">

					<form action="loans.php" method="post">
						<div class="form-group">
							<select name="name" class="form-control">
								<option selected="selected">Select Name</option>
								<?php
									$row = mysqli_query($con, "SELECT * FROM parents ORDER BY name");
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
						  <select class="form-control" name="type">
						  <option>Select 'Credit' For loan Repayment, and 'Debit' For loan issuance</option>
							<option value="Credit">Credit</option>
							<option value="Debit">Debit</option>
						  </select>
						</div>
						<div class="form-group">
							<input type="text" name="amount" class="form-control"  placeholder="Enter Amount"required /> <br/>
						</div>
						<button type="submit" name= "newtransaction"class="btn btn-primary">Confirm</button>&nbsp;&nbsp;&nbsp;<button type="reset" class="btn btn-primary">Cancel</button>
					</form>
	
					 </div>
					 <div class="modal-footer">
						<h2>Thanks</h2>
					 </div>
			</div>

	</div>
	</div>
	<div class="mainaside">
	</div>
	</div>
</div>
<?php include_once("includes/footer.php"); ?>
</body>
</html>