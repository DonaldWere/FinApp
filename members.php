<?php
include_once('includes/db_connect.php');

?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8">
<title>FinApp | Members</title>
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
</head>
<body>
<?php include_once("includes/header.php"); ?> 

<div class="container"> 
<div >
<div class="main1" >
		<h3>All Members</h3>
	<?php	
		// connect to database
		include ('includes/db_connect.php');
		
		//number of results to show per page
		$per_page = 3;
		
		//figure out the total pages in the database
		if ($result = mysqli_query($con, "SELECT * FROM allmember ORDER BY id")){
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
						echo "<ul class='pagination'><li><a href='members.php?page=$i'>$i</a></li></ul>";
					}
				}
				echo "</p>";
				
				// display data in table
				echo "<table border='1' cellpadding='10' class='table table-responsive'>";
				echo "<tr><th>#</th><th>Name:</th><th>Parent:</th><th></th><th></th></tr>";
				
				//loop through results of database query, displaying the in the table
				for ($i = $start; $i < $end; $i++){
					//make sure that PHP doesn't try to show results that do not exist
					if ($i == $total_results) {break; }
					
					//find specific row
					$result->data_seek($i);
					$row = $result->fetch_row();
					
					//echo out the contents of each row into a table
					echo "<tr class='success'>";
					echo '<td>' . $row[0] . '</td>';
					echo '<td>' . $row[1] . '</td>';
					echo '<td>' . $row[2] . '</td>';
					echo '<td><a href="records.php?id=' . $row[0] . ' "><image src="images/pensil.jpg" height="16px" width="16px"></a></td>';
					echo '<td><a href="delete.php?id=' . $row[0] . '" onClick="return ConfirmDelete();"><image src="images/trash.png" height="16px" width="16px"></a></td>';
					echo "</tr>";
				}
				// close table
				echo "</table>";
				echo "<p><b>Showing " . $entries . " of " . $total_results . " Entries </b></p>";
			}else{
			echo "No results to display!";
			}
		} else{
			//error with the query
			echo "Error: " . $con->error;
		}
?>
<?php
if(isset($_POST['newmember'])){
    $name=mysqli_real_escape_string($con, $_POST['name']);
    $parent=mysqli_real_escape_string($con, $_POST['parent']);
    

        
    mysqli_query($con,"INSERT INTO allmember (name,parent) VALUES('$name','$parent')");
	echo"(
				<script>
				 window.alert('Confirmed. Registration was successful');
				 window.location.href=('members.php');
				 </script>
					)";
}
if(isset($_POST['newparent'])){
    $name=mysqli_real_escape_string($con, $_POST['name']);
        
    mysqli_query($con,"INSERT INTO parents (name) VALUES('$name')");
	echo"(
				<script>
				 window.alert('Confirmed. New Parent Added. Also remember to add them as members');
				 window.location.href=('members.php');
				 </script>
					)";
}
?>
			<a class="btn btn-info"  onClick="return mymodal();" id="myBtn" class="link-butn">New Member</a>
	</div>	
	<div class="mainside">
	<div class="mainside1">
	<p><b><h4> Add New Parent </h4></b></p>
		<form action="members.php" method="post">
						<div class="form-group">
							<input type="text" name="name" class="form-control" placeholder="Enter Name" required /> <br/>
						</div>
						<button type="submit" name= "newparent"class="btn btn-primary">Add</button>&nbsp;&nbsp;&nbsp;<button type="reset" class="btn btn-primary">Cancel</button>
		</form>
	</div>
	</div>
	</div>
	

			<!-- The Modal -->
	<div id="myModal" class="modal">

			  <!-- Modal content -->
			  <div class="modal-content">
					<div class="modal-header">
						<span class="close">&times;</span>
						<h2>Add New Member</h2>
					 </div>
					 <div class="modal-body">

					<form action="members.php" method="post">
						<div class="form-group">
							<input type="text" name="name" class="form-control" placeholder="Enter Name" required /> <br/>
						</div>
						<div class="form-group">
							<select name="parent" class="form-control">
								<option selected="selected">Select Parent</option>
								<?php
									$row = mysqli_query($con, "SELECT name FROM parents ORDER BY name");
									while($result = mysqli_fetch_array($row))
									{ 
									?>
										<option value="<?php echo $result["name"]; ?>"><?php echo $result['name']; ?></option>
										<?php 
									}
								?>
						</select>
						</div>
						<button type="submit" name= "newmember"class="btn btn-primary">Save</button>&nbsp;&nbsp;&nbsp;<button type="reset" class="btn btn-primary">Cancel</button>
					</form>
	
					 </div>
					 <div class="modal-footer">
						<h2>Thanks</h2>
					 </div>
			</div>

	</div>
			
</div>
<?php include_once("includes/footer.php"); ?>
</body>
</html>