<!DOCTYPE html>
<html>
<head>
    <title>Race Results</title>
	<style>
        body {
            background-image: url("new_tractor.jpg");
            background-repeat: no-repeat;
            background-size: cover;
        }
		
		h1 
		{
			font-family: "Arial", sans-serif;
			font-size: 36px;
			color: #000000;
			text-align: center;
			text-transform: uppercase;
			letter-spacing: 2px;
			margin: 20px 0;
		}
		
		
		 /* Table Styling */
		.race-table {
			width: 100%;
			border-collapse: collapse;
			font-family: Arial, sans-serif;
			font-size: 14px;
			text-align: center;
		}

		/* Table Header Styling */
		.race-table thead {
			background-color: #333333;
			color: #ffffff;
		}

		.race-table th {
			padding: 10px;
		}

		/* Table Data Styling */
		.race-table tbody {
			background-color: #f2f2f2;
		}

		.race-table td {
			padding: 10px;
		}

		/* Alternate Row Color */
		.race-table tbody tr:nth-child(even) {
			background-color: #dddddd;
		}

		/* Table Hover Effect */
		.race-table tbody tr:hover {
			background-color: #cccccc;
		}
    </style>
</head>
<body>
    <?php
	
		//Preparing to read from spreadsheet:
		require 'vendor/autoload.php';
		use PhpOffice\PhpSpreadsheet\IOFactory;
		
		//Obtaining workbook and woorksheet names: 
		$current_url = $_SERVER['REQUEST_URI']; 
		$workbook = get_workbook($current_url);
		$worksheet = get_worksheet($current_url);
		
		//Creating header for page: 
		$header = get_header($workbook, $worksheet);
		echo "<center> <h1>".$header." Results</h1> </center>";
		
		//Preparing table: 
		echo "<center>";
		echo '<table class = "race-table">';
		echo '<thead><th>Placement</th><th>Participant</th><th>Vehicle</th><th>Points</th></thead>';
		
		//Preparing to open the appropriate worksheet:
		$reader = IOFactory::createReader('Xlsx');
		$reader->setLoadSheetsOnly([$worksheet]);
		
		//Opening spreadsheet:
		$event_book = $reader->load('Results_Data/'.$workbook.'.xlsm');
		$event_sheet = $event_book->getActiveSheet();
		
		//Obtaining the highest row of data:
		$row_max = $event_sheet->getHighestDataRow('A');
		
		for($current_row = 4; $current_row <= $row_max; $current_row++)
		{
			//Obtaining data for the respective row:
			$placement = $event_sheet->getCell('A'.$current_row)->getValue();
			$participant = $event_sheet->getCell('H'.$current_row)->getValue();
			$vehicle = $event_sheet->getCell('J'.$current_row)->getValue();
			$points = $event_sheet->getCell('C'.$current_row)->getValue();
			
			//Printing results:
			echo"<tr><td>$placement</td><td>$participant</td><td>$vehicle</td><td>$points</td></tr>";
		}
		
		//Parses the given url and obtains the text after 'id=' as a string:
		function get_workbook($url)
		{
			// Parse the URL
			$parsedUrl = parse_url($url);

			// Get the query component
			$query = $parsedUrl['query'];

			// Parse the query string into an associative array
			parse_str($query, $queryArray);

			// Access the value of 'id'
			$identification = $queryArray['id'];
			
			//Obtain the string before "!":
			$workbook_name = strstr($identification, '!', true);
			
			//Return the workbook name: 
			return $workbook_name;
		}
		
		//Parses the given url and obtains the text after 'id=' as a string:
		function get_worksheet($url)
		{
			// Parse the URL
			$parsedUrl = parse_url($url);

			// Get the query component
			$query = $parsedUrl['query'];

			// Parse the query string into an associative array
			parse_str($query, $queryArray);

			// Access the value of 'id'
			$identification = $queryArray['id'];
			
			//Obtain string after the '!':
			$worksheet_name = strstr($identification, '!', false);
			$worksheet_name = ltrim($worksheet_name, '!');
			
			//Return the worksheet name:
			return $worksheet_name;
		}
		
		function get_header($workbook, $worksheet)
		{
			//Preparing to open the appropriate worksheet:
			$reader = IOFactory::createReader('Xlsx');
			$reader->setLoadSheetsOnly([$worksheet]);
			
			//Opening spreadsheet:
			$event_book = $reader->load('Results_Data/'.$workbook.'.xlsm');
			$event_sheet = $event_book->getActiveSheet();
			
			//Obtaining title componenets:
			$weight = $event_sheet->getCell('J2')->getValue();
			$weight = strstr($weight, '.', true);
			$class = strstr($worksheet, '_', true);
			$division = get_level($event_sheet->getCell('M2')->getValue());
			
			//Adding button label to button array:
			$title = $weight.' '.$class.' '.$division;
			
			return $title;
		}
		
		//This is used to convert the circuit to the division for the printed button: 
		function get_level($circuit)
		{
			switch($circuit) {
			case 'IN':
				$level = "Invitational";
				break;
			case 'GN':
				$level = "Grand National";
				break;
			case 'SN':
				$level = "Super National";
				break;
			default:
				$level = "Regional";
				break;
			}
			return $level;
		}
	?>
</body>
</html>
