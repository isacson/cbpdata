<?php require_once 'functions.php';

/* CBP changed their notation from "UC / Single Minors" to "UAC". Let's get rid of "UAC" in the dataset where it appears, so that we're searching for the same thing. */

try {
    // Connect to the database using PDO

    // Step 1: Check if "UAC" exists in the demographic column
    $checkQuery = "SELECT COUNT(*) FROM data WHERE demographic = 'UAC'";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    // Step 2: If "UAC" exists, update it to "UC / Single Minors"
    if ($count > 0) {
        $updateQuery = "UPDATE data SET demographic = 'UC / Single Minors' WHERE demographic = 'UAC'";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute();
    }
} catch (PDOException $e) {
    // Handle potential errors gracefully
    error_log("Database error: " . $e->getMessage());
}
?>

<html>
	<head>
		<title>
			Make a Table from CBP's Migration Data
		</title>
		<!--There's no separate stylesheet, just this inline bit. Feel free to change any of this.-->
		<style>
			body {
				font-family: "Avenir Next", Merriweather, Verdana;
				margin-left: 5%;
			}
			fieldset {
				width: 80%; 
			}
			.submitbutton {
				font-weight:bold; width:40%; margin-left: 20%; background-color: #797979; color: white; font-family: "Avenir Next", Merriweather, Verdana; padding: 0.25em; font-size: 1em; border-radius: 8px;
			}
			button {
				font-family: "Avenir Next", Merriweather, Verdana; margin-bottom: 0.25em;
			}
			label {
				white-space:nowrap;
			}
			.footer-github {
				font-size: 80%;
				text-align: center;
				width: 80%;
			}
		</style>

		<!--The JavaScripts here: (1) select /de-select all the checkboxes in a section, by their "name" attribute. (This comes from the javaTpoint website: https://www.javatpoint.com/how-to-select-all-checkboxes-using-javascript) clearForm() restores the form to its default state. It comes from a 2018 StackOverflow response at https://stackoverflow.com/questions/2279760/how-to-reset-all-checkboxes-using-jquery-or-pure-js-->
		<script type="text/javascript">  

			function selectCheck(selectName) {  
				var ele=document.getElementsByName(selectName);  
				for(var i=0; i<ele.length; i++) {
					if(ele[i].type=='checkbox')  
						ele[i].checked=true;  
				}  
			}  

			function deSelectCheck(selectName) {  
				var ele=document.getElementsByName(selectName);  
				for(var i=0; i<ele.length; i++) {  
					if(ele[i].type=='checkbox')  
						ele[i].checked=false;
				}  
			}

			function clearForm() {
				var clist = document.getElementsByTagName("input");
				for (var i = 0; i < clist.length; ++i) {
					if(clist[i].type == "checkbox") {
						clist[i].checked = false;
					}
				}  
				document.getElementById("months"). checked = false;
				document.getElementById("years"). checked = true;
				document.getElementById("by_nationality"). checked = true;
				document.getElementById("by_demographic"). checked = false;
				document.getElementById("by_geographic_area"). checked = false;
				document.getElementById("by_agency"). checked = false;
//				document.getElementById("by_state"). checked = false;
				document.getElementById("by_title"). checked = false;
				document.getElementById("by_nothing"). checked = false;
				document.getElementById("year1").selectedIndex = 0;	
				document.getElementById("year2").selectedIndex = 0;	
			}
			
        </script>
        <script defer data-domain="cbpdata.adamisacson.com" src="https://plausible.io/js/script.js"></script>
	</head>
	<body>
		<h2 style="color:white; background-color: #797979; width: 80%; padding: 0 15px 0 15px;" align="center">
			This form will make a table using CBP&rsquo;s migration data since Fiscal Year 2020
		</h2>
		
		<!--The form uses the "get" method so that the page with the resulting table will have a unique URL.-->
		<form id="theForm" action="cbp_data_table.php" method="get">
		
			<h3>
				1. The table&rsquo;s columns:
			</h3>

				<fieldset>
				<legend><strong>Show a table of migration data by</strong></legend>
					<input type="radio" id="months" name="time_period" value="months"><label for="months">Month</label>
					<input type="radio" id="years" name="time_period" value="years" checked><label for="years">Year</label>
					&nbsp;&nbsp;&nbsp; (Optional: show only the years
					<select name="year1" id="year1">
					<?php					
						// Let's grab the fiscal years that show any data, and order them from earliest to latest
						$years_stmt = $pdo->query ("SELECT DISTINCT fiscal_year FROM data WHERE land_border_region = 'Southwest Land Border' GROUP BY fiscal_year HAVING SUM(encounter_count) > 0 ORDER BY fiscal_year ASC;");
						
						while ($row = $years_stmt->fetch()) {
							$years[] = $row["fiscal_year"];
						}
						
						// This function gets rid of CBP's " (FYTD)" notation from the "year"
						
						$removeFYTD = function($text) {
							return str_replace(' (FYTD)', '', $text);
						};
						
						$years = array_map($removeFYTD, $years);
		
						foreach($years as $year) {
						
							echo "<option value='$year'>$year</option>";
						}					
					?>
					</select>
					through
					<select name="year2" id="year2">
					<?php
					rsort($years);
					
					foreach($years as $year) {
				
					echo "<option value='$year'>$year</option>";
					}				
					?>
					</select>
					)
				</fieldset>
			
			<h3 style="color: #5BA8C8;">
				2. The table&rsquo;s rows:
			</h3>

			<fieldset>
				<legend><strong>Display migration data according to</strong></legend>
					<input type="radio" id="by_nationality" name="organized_by" value="by_nationality" checked><label for="by_nationality"><strong>Nationality</strong></label>
					<label for="by_demographic"><input type="radio" id="by_demographic" name="organized_by" value="by_demographic"><strong>Demographic Category</strong> (single adult, family, unaccompanied child)</label>
					<br>
					<label for="by_geographic_area"><input type="radio" id="by_geographic_area" name="organized_by" value="by_geographic_area"><strong>Geographic Area</strong> (sector or field office)</label>
					<label for="by_agency"><input type="radio" id="by_agency" name="organized_by" value="by_agency"><strong>At Ports of Entry or Between Them</strong></label>
					<br>
					<!-- <label for="by_state"><input type="radio" id="by_state" name="organized_by" value="by_state"><strong>U.S. State</strong></label> -->
					<label for="by_title"><input type="radio" id="by_title" name="organized_by" value="by_title"><strong>Title 8 or Title 42</strong></label>
					<br>
					<label for="by_nothing"><input type="radio" id="by_nothing" name="organized_by" value="by_nothing"><strong>Nothing</strong> (just show a single row of totals)</label>
			</fieldset>
			
			<p>
				<input type="submit" class="submitbutton" value="Show the Data"> <button type="button" onclick="clearForm()">Clear the Form</button>
			</p>
			
			<h3 style="color: #7CAE6C;">
				3. Optionally, have the table show only:
			</h3>
			
			<?php
				// The "makeFieldset()" function returns the HTML for each of the groups of optional checkboxes. It sends the text of the group's title, the checkboxes' "name" attribute (with brackets [] indicating that it will be read as an array), the MySQL credentials variable, the field of CBP's dataset to be queried to populate the group, and the MySQL text snippet indicating what the query should search for.
				echo makeFieldset("Border Patrol Sectors", "bp_sectors[]", $pdo, "area_of_responsibility", " AND component = 'U.S. Border Patrol' ") . "<br>";
				echo makeFieldset("CBP field offices", "field_offices[]", $pdo, "area_of_responsibility", " AND component = 'Office of Field Operations' ") . "<br>";
				echo makeFieldset("Nationalities", "nationalities[]", $pdo, "citizenship", "") . "<br>";
				echo makeFieldset("Demographic Categories", "demographics[]", $pdo, "demographic", "") . "<br>";
//				echo makeFieldset("States", "states[]", $pdo, "state", "") . "<br>";
				echo makeFieldset("Title 8 or Title 42", "titles[]", $pdo, "title_of_authority", "");
			?>
			
			<p>
				<input type="submit" class="submitbutton" value="Show the Data"> <button type="button" onclick="clearForm()">Clear the Form</button>
			</p>	
			
		</form>
		
		<?php require_once 'footer.php'; ?>
		
	</body>
</html>

<?php

function fix_weird($co) {
	// Translates into plain English some of the jargon and abbreviations in CBP's database.

	$co = ucwords(strtolower($co));
	switch ($co) {
		case "Other":
			$co = "Other Countries";
			break;
		case "China, Peoples Republic Of":
			$co = "China";
			break;
		case "Myanmar (burma)":
			$co = "Myanmar (Burma)";
			break;
		case "Fmua":
			$co = "Family Unit Members";
			break;
		case "Uc / Single Minors":
			$co = "Unaccompanied Children / Single Minors";
			break;
		case "U.s. Border Patrol":
			$co = "Between the Ports of Entry (Border Patrol)";
			break;
		case "Office Of Field Operations":
			$co = "At the Ports of Entry (CBP Office of Field Operations)";
			break;
		case "Tx":
			$co = "Texas";
			break;
		case "Az":
			$co = "Arizona";
			break;
		case "Nm":
			$co = "New Mexico";
			break;
		case "Ca":
			$co = "California";
			break;
	}
	return $co;
}

function makeFieldset($legend_text, $button_js, $pdo, $search_field, $additional_search) {
	// Generates the HTML for the sets of checkboxes for the optional categories.
	
	$fieldset = "
		<fieldset>
			<legend><strong>$legend_text</strong>
				<button type='button' onclick='selectCheck(\"$button_js\")'>Select All</button>
				 <button type='button' onclick='deSelectCheck(\"$button_js\")'>Clear All</button></legend>	";
		$stmt = $pdo->prepare("SELECT DISTINCT $search_field FROM data WHERE land_border_region = 'Southwest Land Border' $additional_search ORDER BY $search_field ASC;");
		$stmt->execute();
		$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		foreach($result as $sector) {
			$ucresult = fix_weird($sector["$search_field"]);
			$fieldset .= "	<label for='$sector[$search_field]'><input type='checkbox' id='$sector[$search_field]' name='$button_js' value='$sector[$search_field]'>$ucresult</label>";					
		}
		$fieldset .= "	</fieldset>";
	return $fieldset;
}
			
?>