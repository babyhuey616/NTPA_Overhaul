<!DOCTYPE html>
<html>
<head>
    <title>2023 Events</title>
	<style>
        body 
		{
            background-image: url("tractor.jpg");
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
		.cool-table 
		{
			width: 100%;
			border-collapse: collapse;
			text-align: center;
		}

		/* Table Header Styling */
		.cool-table thead {
			background-color: #333333;
			color: #ffffff;
		}

		.cool-table th {
			padding: 10px;
			text-align: center;
		}

		/* Table Data Styling */
		.cool-table tbody {
			background-color: #f2f2f2;
		}

		.cool-table td {
			padding: 10px;
		}

		/* Alternate Row Color */
		.cool-table tbody tr:nth-child(even) {
			background-color: #dddddd;
		}

		/* Table Hover Effect */
		.cool-table tbody tr:hover {
			background-color: #cccccc;
		}
		
		.search 
		{
        display: inline-block;
        border: 2px solid #555555;
        border-radius: 20px;
        background-color: #ffffff;
        padding: 10px 20px;
        font-size: 16px;
        color: #333333;
        outline: none;
        transition: border-color 0.3s ease;
        width: 300px;
		}

		.search:focus {
			border-color: #ff0000;
			box-shadow: 0 0 5px #ff0000;
		}
    </style>
	<script>
		function sortTable(n) 
		{
			var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
			table = document.getElementById("event-data");
			switching = true;
			dir = "asc";
			while (switching) {
				switching = false;
				rows = table.getElementsByTagName("tr");
				for (i = 1; i < rows.length - 1; i++) {
					shouldSwitch = false;
					x = rows[i].getElementsByTagName("td")[n];
					y = rows[i+1].getElementsByTagName("td")[n];
					
					console.log(x);
					
					if (dir == "asc") {
						if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
							shouldSwitch = true;
							break;
						}
					} else if (dir == "desc") {
						if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
							shouldSwitch = true;
							break;
						}
					}
				}
				if (shouldSwitch) {
					rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
					switching = true;
					switchcount++;
				} else {
					if (switchcount == 0 && dir == "asc") {
						dir = "desc";
						switching = true;
					}
				}
			}
		}
		
	</script>
	
	<script> 
		function tableSearch() {
		
		let input, filter, table, tr, td, td1, txtValue;

		//Intialising Variables
		input = document.getElementById("myInput");
		filter = input.value.toUpperCase();
		table = document.getElementById("event-data");
		tr = table.getElementsByTagName("tr");

		for (let i = 1; i < tr.length; i++) {
			td = tr[i];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
				} else {
					tr[i].style.display = "none";
				}
			}
		}
		}
	</script>
</head>
<body>
    <?php
		//Just a little title I put together :)
        echo "<center> <h1> 2023 NTPA Events List! </h1> </center>";
		
		//Search bar:
		echo '<center>';
		echo '<div class="search-box">';
		echo '<input class="search" type="text" id="myInput" onkeyup="tableSearch()" placeholder="Search Event Data  . . .  ">';
		echo '</div>';
		echo '</center>';
		
		echo '<p>
			
			</p>';
		
		//Code needed to access spreadsheets: 
		require 'vendor/autoload.php';
		
		use PhpOffice\PhpSpreadsheet\IOFactory;

		//Variable to store path to excel spreadsheets:
		$event_path = 'Results_Data';
		
		//Variable for holding the excel files:
		$event_data = scandir($event_path);
		
		// Remove . and .. directories from the file list
		$event_data = array_diff($event_data, array('.', '..'));
		
		echo "<center>";
		
		echo '<table id = "event-data" class ="cool-table">';
		
		//Preparing table headers:
		echo '<thead><th onclick="sortTable(0)">Event</th><th>Date</th><th>State</th><th>City</th><th>Level</th><th>Circuit</th></thead>';
		
		//Iterating through files and reading data:
		foreach ($event_data as $file)
		{
			
			//Readers for scanning excel file:
			$reader = IOFactory::createReader('Xlsx');
			$reader_2 = IOFactory::createReader('Xlsx');
			
			//Telling reader to only open the "Event" sheet:
			$reader->setLoadSheetsOnly(['Event']);
			$reader_2->setLoadSheetsOnly(['Classes-Sessions']);
			
			//Loading the worksheet:
			$event_book = $reader->load('Results_Data/'.$file);
			
			$event_book_2 = $reader_2->load('Results_Data/'.$file);
			
			// Access the sheet and perform actions
			$event_sheet = $event_book->getActiveSheet();
			$event_sheet_2 = $event_book_2->getActiveSheet();
			
			//Obtaining event name:
			$event_name = $event_sheet->getCell('A2')->getValue();
			
			//Obtaining and formatting event date:
			$event_date = $event_sheet->getCell('B11')->getValue();
			$date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($event_date);
			$formattedDate = $date->format('Y-m-d');
			
			//Obtaining event city and state:
			$location = $event_sheet->getCell('A1')->getValue();
			$parts = explode(', ', $location);
			$city = $parts[0];
			$abb_state = $parts[1];
			$state = convert_state($abb_state);
			
			//Obtaining level and circuit:
			$circuit = $event_sheet_2->getCell('E1')->getValue();
			$level = get_level($circuit);
			
			//Creating ID:
			$id = $city.", ".$abb_state."_".$formattedDate;
			
			echo"<tr><td><a href='https://localhost/Demo_Page/Custom_Code/event_results.php?id=$id'>$event_name</a></td><td>$formattedDate</td><td>$state</td><td>$city</td><td>$level</td><td>$circuit</td></tr>";
		}
		
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
				
		function convert_state($abb_state)
		{
			switch ($abb_state) 
			{
			case 'AL':
				$state = 'Alabama';
				break;
			case 'AK':
				$state = 'Alaska';
				break;
			case 'AZ':
				$state = 'Arizona';
				break;
			case 'AR':
				$state = 'Arkansas';
				break;
			case 'CA':
				$state = 'California';
				break;
			case 'CO':
				$state = 'Colorado';
				break;
			case 'CT':
				$state = 'Connecticut';
				break;
			case 'DE':
				$state = 'Delaware';
				break;
			case 'FL':
				$state = 'Florida';
				break;
			case 'GA':
				$state = 'Georgia';
				break;
			case 'HI':
				$state = 'Hawaii';
				break;
			case 'ID':
				$state = 'Idaho';
				break;
			case 'IL':
				$state = 'Illinois';
				break;
			case 'IN':
				$state = 'Indiana';
				break;
			case 'IA':
				$state = 'Iowa';
				break;
			case 'KS':
				$state = 'Kansas';
				break;
			case 'KY':
				$state = 'Kentucky';
				break;
			case 'LA':
				$state = 'Louisiana';
				break;
			case 'ME':
				$state = 'Maine';
				break;
			case 'MD':
				$state = 'Maryland';
				break;
			case 'MA':
				$state = 'Massachusetts';
				break;
			case 'MI':
				$state = 'Michigan';
				break;
			case 'MN':
				$state = 'Minnesota';
				break;
			case 'MS':
				$state = 'Mississippi';
				break;
			case 'MO':
				$state = 'Missouri';
				break;
			case 'MT':
				$state = 'Montana';
				break;
			case 'NE':
				$state = 'Nebraska';
				break;
			case 'NV':
				$state = 'Nevada';
				break;
			case 'NH':
				$state = 'New Hampshire';
				break;
			case 'NJ':
				$state = 'New Jersey';
				break;
			case 'NM':
				$state = 'New Mexico';
				break;
			case 'NY':
				$state = 'New York';
				break;
			case 'NC':
				$state = 'North Carolina';
				break;
			case 'ND':
				$state = 'North Dakota';
				break;
			case 'OH':
				$state = 'Ohio';
				break;
			case 'OK':
				$state = 'Oklahoma';
				break;
			case 'OR':
				$state = 'Oregon';
				break;
			case 'PA':
				$state = 'Pennsylvania';
				break;
			case 'RI':
				$state = 'Rhode Island';
				break;
			case 'SC':
				$state = 'South Carolina';
				break;
			case 'SD':
				$state = 'South Dakota';
				break;
			case 'TN':
				$state = 'Tennessee';
				break;
			case 'TX':
				$state = 'Texas';
				break;
			case 'UT':
				$state = 'Utah';
				break;
			case 'VT':
				$state = 'Vermont';
				break;
			case 'VA':
				$state = 'Virginia';
				break;
			case 'WA':
				$state = 'Washington';
				break;
			case 'WV':
				$state = 'West Virginia';
				break;
			case 'WI':
				$state = 'Wisconsin';
				break;
			case 'WY':
				$state = 'Wyoming';
				break;
			default:
				$state = 'Unknown Abbreviation';
				break;
			}
			
			return $state;
		}
		echo "</table>";
		
		echo "</center>";
	?>
</body>
</html>
