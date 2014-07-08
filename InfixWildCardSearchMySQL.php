<?php

// MySQL inherenlty use full table scan for searches with LIKE parametres having a infix wild card. For e.g. SELECT * FROM some_table WHERE ( name LIKE '%blabla')
// This can be optimized by converting it to a postfix search by reversing both the search pattern as well as the strings in dataset.
// This needs a bit of extra work but its worth it as speed up gained on large data base is huge (from a few second to milliseconds) .


//PHP script


//connect to db
$con=mysqli_connect('127.0.0.1:3306','user','password','mydb');
//catch error
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}



// create your table for keeping reverse index
$sql  = "CREATE TABLE infixSearchTable(PID INT NOT NULL AUTO_INCREMENT,PRIMARY KEY(PID),reverse_entry VARCHAR(255))";


//execute query & catch error
if (mysqli_query($con,$sql)) {
	echo "Table revEmail created successfully";
} else {
	echo "Error creating table: " . mysqli_error($con);
}


$query = "SELECT COUNT(row_name) FROM table_name";
$result = mysqli_query($con,$query);

$rows = mysqli_fetch_row($result);

$num_rows = $rows;

$i = 0;


while($i++ < intval($num_rows)) {




	// row_id is the auto-incrementid of the table, if not there create one by the query "ALTER TABLE table_name ADD row_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY"

	$result = mysqli_query($con,"SELECT row_name FROM table_name WHERE row_id = {$i}");
	$rows = mysqli_fetch_row($result);
	$rev_entry = strrev($rows[0]);
    mysqli_query($con,"INSERT INTO infixSearchTable (reverse_entry) VALUES ('{$rev_entry}')");

}

// add index on the reverse feiled
$query = "ALTER TABLE `table` ADD INDEX `product_id` (`product_id`)";
$result = mysqli_query($con,$query);

//create a trigger for auto-update both tables
$query =  "CREATE TRIGGER `add_reverse` BEFORE UPDATE ON `infixSearchTable` FOR EACH ROW BEGIN SET infixSearchTable.rev_entry = reverse( table_name.row_name )";
$result = mysqli_query($con,$query);


//now you can search for the rows using select reverse(rev_entry) from infixSearchTable` where rev_entry like reverse('%search_term');








