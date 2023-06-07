<!DOCTYPE html>
<html>
<head>
    <title>Event Races</title>
	
	<style>
        body 
		{
            background-image: url("old_tractor.jpg");
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
		
		button 
		{
			background-color: #ff0000;
			color: #ffffff;
			padding: 12px 24px;
			font-size: 18px;
			border: none;
			border-radius: 8px;
			cursor: pointer;
			text-decoration: none;
			width: 200px;
			height: 60px;
		}
		
		button:hover
		{
			background-color: #cc0000;
		}
    </style>
	
</head>
<body>
    <?php
		//Obtaining current URL and parsing it for spreasheet ID:
		$current_url = $_SERVER['REQUEST_URI']; 
		$ident = parse_data($current_url);
		
		//Preparing to read from spreadsheet:
		require 'vendor/autoload.php';
		use PhpOffice\PhpSpreadsheet\IOFactory;
		$reader = IOFactory::createReader('Xlsx');
		$reader->setLoadSheetsOnly(['Event']);

		//Opening spreadsheet:
		$event_book = $reader->load('Results_Data/'.$ident.'.xlsm');
		$event_sheet = $event_book->getActiveSheet();
		
		//Obtaining event name:
		$event_name = $event_sheet->getCell('A2')->getValue();
		
		
		//Printing event name: 
		echo "<center>";
		echo "<h1> $event_name </h1>";
		echo "</center>";
		
		//Initiliazing arrays for sheet names and buttons:
		$sheets = array();
		$buttons = array();
		
		//Calling functions to obtain the button titles:
		$sheets = get_sheet_names();
		$buttons = set_button_names($sheets);
		
		$sheets_index = 0;
		
		//Printing buttons:
		foreach($buttons as $label)
		{
			$sheet_name = $sheets[$sheets_index];

			echo "<center>";
			echo "<button> <a href='https://localhost/Demo_Page/Custom_Code/individual_event.php?id=$ident!$sheet_name'>".$label."</a></button>";
			echo "</center>";
			echo "</br>";
			
			$sheets_index = $sheets_index + 1;
		}
		
		//This function creates the names for our buttons:
		function set_button_names($sheet_names)
		{
			//Preparing arry and iterator for loop:
			$button_strings = array();
			$button_index = 0;
			
			foreach($sheet_names as $classes)
			{
				//Obtaining current URL and parsing it for spreasheet ID:
				$current_url = $_SERVER['REQUEST_URI']; 
				$ident = parse_data($current_url);
				
				//Preparing to open the appropriate worksheet:
				$reader_3 = IOFactory::createReader('Xlsx');
				$reader_3->setLoadSheetsOnly([$classes]);
				
				//Opening spreadsheet:
				$event_book_3 = $reader_3->load('Results_Data/'.$ident.'.xlsm');
				$event_sheet_3 = $event_book_3->getActiveSheet();
				
				//Obtaining button componenets:
				$weight = $event_sheet_3->getCell('J2')->getValue();
				$weight = strstr($weight, '.', true);
				$class = strstr($classes, '_', true);
				$division = get_level($event_sheet_3->getCell('M2')->getValue());
				
				//Adding button label to button array:
				$button_strings[$button_index] = $weight.' '.$class.' '.$division;
				$button_index = $button_index + 1;
			}
			
			return $button_strings;
		}
		
		//Loops through the "Classes-Sessions" worksheet to determine the worksheets active in the workbook:
		function get_sheet_names()
		{
			//Obtaining current URL and parsing it for spreasheet ID:
			$current_url = $_SERVER['REQUEST_URI']; 
			$ident = parse_data($current_url);
			
			//Preparing to open the appropriate worksheet:
			$reader_2 = IOFactory::createReader('Xlsx');
			$reader_2->setLoadSheetsOnly(['Classes-Sessions']);
			
			//Opening spreadsheet:
			$event_book_2 = $reader_2->load('Results_Data/'.$ident.'.xlsm');
			$event_sheet_2 = $event_book_2->getActiveSheet();
			
			//Obtaining initial value in column L:
			$L_Column = $event_sheet_2->getCell('L1')->getValue();
			
			//Preparing iterator and array:
			$L_index = 1;
			$sheet_array = array();
			
			while($L_Column != 0)
			{
				//Obtaining details from columns to obtain active sheet names:
				$sheet_array[$L_index - 1] = $event_sheet_2->getCell('B'.$L_index)->getValue();
				$sheet_array[$L_index - 1] .= "_";
				$sheet_array[$L_index - 1] .= $event_sheet_2->getCell('C'.$L_index)->getValue();
				
				//Preparing for next iteration:
				$L_index = $L_index + 1;
				$L_Column = $event_sheet_2->getCell('L'.$L_index)->getValue();	
			}
			
			return $sheet_array;
		}
		
		//Parses the given url and obtains the text after 'id=' as a string:
		function parse_data($url)
		{
			// Parse the URL
			$parsedUrl = parse_url($url);

			// Get the query component
			$query = $parsedUrl['query'];

			// Parse the query string into an associative array
			parse_str($query, $queryArray);

			// Access the value of 'id'
			$identification = $queryArray['id'];
			
			return $identification;
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
