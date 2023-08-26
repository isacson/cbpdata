<?php require_once 'functions.php'; ?>

<html>
	<head>
		<title>
			Make a Table from CBP's Migration Data
		</title>
		<style>
			body {font-family: "Avenir Next", Merriweather, Verdana;}
			fieldset {width: 80%; margin-left: 5%;}
			.submitbutton {font-weight:bold; width:40%; margin-left: 25%; background-color: #4B3C3D; color: white; font-family: "Avenir Next", Merriweather, Verdana; padding: 0.25em; font-size: 1em; border-radius: 8px;}
			button {font-family: "Avenir Next", Merriweather, Verdana;}
		</style>
		<script type="text/javascript">  
			function selectCheck(selectName){  
				var ele=document.getElementsByName(selectName);  
				for(var i=0; i<ele.length; i++) {
					if(ele[i].type=='checkbox')  
						ele[i].checked=true;  
				}  
			}  

			function deSelectCheck(selectName){  
				var ele=document.getElementsByName(selectName);  
				for(var i=0; i<ele.length; i++) {  
					if(ele[i].type=='checkbox')  
						ele[i].checked=false;				  
				}  
			}

			function clearForm(){
				var clist = document.getElementsByTagName("input");
				for (var i = 0; i < clist.length; ++i) {
					if(clist[i].type == "checkbox") {
						clist[i].checked = false;
					}
				}  
				document.getElementById("months"). checked = true;
				document.getElementById("years"). checked = false;
				document.getElementById("by_nationality"). checked = true;
				document.getElementById("by_demographic"). checked = false;
				document.getElementById("by_geographic_area"). checked = false;
				document.getElementById("by_agency"). checked = false;
				document.getElementById("by_state"). checked = false;
				document.getElementById("by_title"). checked = false;
				document.getElementById("by_nothing"). checked = false;				
			}
        </script>
	</head>
	<body>
		<h2 style="color: #797979;">
			This form will make a table using CBP&rsquo;s migration data since Fiscal Year 2020
		</h2>
		
		<form id="theForm" action="cbp_data_table.php" method="get">
		
			<h3>
				1. The table&rsquo;s columns:
			</h3>
				<fieldset>
				<legend><strong>Show a table of migration data by</strong></legend>
					<input type="radio" id="months" name="time_period" value="months" checked><label for="months">Month</label>
					<input type="radio" id="years" name="time_period" value="years"><label for="years">Year</label>
				</fieldset>
			
			<h3 style="color: #5BA8C8;">
				2. The table&rsquo;s rows:
			</h3>
			<fieldset>
				<legend><strong>Display migration data according to</strong></legend>
				<p>
					<input type="radio" id="by_nationality" name="organized_by" value="by_nationality" checked><label for="by_nationality"><strong>Nationality</strong></label>
					<input type="radio" id="by_demographic" name="organized_by" value="by_demographic"> 
						<label for="by_demographic"><strong>Demographic Category</strong> (single adult, family, unaccompanied child)</label>
					<br>
					<input type="radio" id="by_geographic_area" name="organized_by" value="by_geographic_area"><label for="by_geographic_area"><strong>Geographic Area</strong> (sector or field office)</label>
					<input type="radio" id="by_agency" name="organized_by" value="by_agency"><label for="by_agency"><strong>At Ports of Entry or Between Them</strong></label>
					<br>
					<input type="radio" id="by_state" name="organized_by" value="by_state"><label for="by_state"><strong>U.S. State</strong></label>
					<input type="radio" id="by_title" name="organized_by" value="by_title"><label for="by_title"><strong>Title 8 or Title 42</strong></label>
					<br>
					<input type="radio" id="by_nothing" name="organized_by" value="by_nothing"><label for="by_nothing"><strong>Nothing</strong> (just show a single row of totals)</label>
				</p>
			</fieldset>
			
			<p>
				<input type="submit" class="submitbutton" value="Show the Data"> <button type="button" onclick="clearForm()">Clear the Form</button>
			</p>
			
			<h3 style="color: #7CAE6C;">
				3. Optionally, have the table show only:
			</h3>
			
			<fieldset>
				<legend><strong>Border Patrol Sectors</strong></legend>
					 <button type="button" onclick="selectCheck('bp_sectors[]')">Select All</button>
					 <button type="button" onclick="deSelectCheck('bp_sectors[]')">Clear All</button><br><br>
				<?php
					$stmt = $pdo->prepare("SELECT DISTINCT area_of_responsibility FROM data WHERE land_border_region = 'Southwest Land Border' AND component = 'U.S. Border Patrol' ORDER BY area_of_responsibility ASC;");
					$stmt->execute();
					$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
					foreach($result as $sector) {
						echo "	<input type='checkbox' id='$sector[area_of_responsibility]' name='bp_sectors[]' value='$sector[area_of_responsibility]'><label for='$sector[area_of_responsibility]'>$sector[area_of_responsibility]</label>";
					}
				?>
			</fieldset>
			<br>
			
			<fieldset>
				<legend><strong>CBP field offices</strong></legend>
					 <button type="button" onclick="selectCheck('field_offices[]')">Select All</button>
					 <button type="button" onclick="deSelectCheck('field_offices[]')">Clear All</button><br><br>
				<?php
					$stmt = $pdo->prepare("SELECT DISTINCT area_of_responsibility FROM data WHERE land_border_region = 'Southwest Land Border' AND component = 'Office of Field Operations' ORDER BY area_of_responsibility ASC;");
					$stmt->execute();
					$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);					
					foreach($result as $sector) {
						echo "	<input type='checkbox' id='$sector[area_of_responsibility]' name='field_offices[]' value='$sector[area_of_responsibility]'><label for='$sector[area_of_responsibility]'>$sector[area_of_responsibility]</label>";
					}
				?>
			</fieldset>
			<br>
			
			<fieldset>
				<legend><strong>Nationalities</strong></legend>
					 <button type="button" onclick="selectCheck('nationalities[]')">Select All</button>
					 <button type="button" onclick="deSelectCheck('nationalities[]')">Clear All</button><br><br>
				<?php
					$stmt = $pdo->prepare("SELECT DISTINCT citizenship FROM data WHERE land_border_region = 'Southwest Land Border' ORDER BY citizenship ASC;");
					$stmt->execute();
					$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);							
					foreach($result as $sector) {
						$ucresult = fix_weird($sector["citizenship"]);
						echo "	<input type='checkbox' id='$ucresult' name='nationalities[]' value='$sector[citizenship]'><label for='$ucresult'>$ucresult</label>";
					}
				?>
			</fieldset>
			<br>
			
			<fieldset>
				<legend><strong>Demographic categories</strong></legend>
					 <button type="button" onclick="selectCheck('demographics[]')">Select All</button>
					 <button type="button" onclick="deSelectCheck('demographics[]')">Clear All</button><br><br>
				<?php
					$stmt = $pdo->prepare("SELECT DISTINCT demographic FROM data WHERE land_border_region = 'Southwest Land Border' ORDER BY demographic ASC;");
					$stmt->execute();
					$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);						
					foreach($result as $sector) {
						$demo = fix_weird($sector["demographic"]);
						echo "	<input type='checkbox' id='$demo' name='demographics[]' value='$sector[demographic]'><label for='$demo'>$demo</label>";
					}
				?>
			</fieldset>
			<br>
			
			<fieldset>
					<legend><strong>States</strong></legend>
					 <button type="button" onclick="selectCheck('states[]')">Select All</button>
					 <button type="button" onclick="deSelectCheck('states[]')">Clear All</button><br><br>
					<?php
						$stmt = $pdo->prepare("SELECT DISTINCT state FROM data WHERE land_border_region = 'Southwest Land Border' ORDER BY state ASC;");
						$stmt->execute();
						$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);						
						foreach($result as $sector) {		
							$state = fix_weird($sector["state"]);
							echo "	<input type='checkbox' id='$state' name='states[]' value='$sector[state]'><label for='$state'>$state</label>";
						}
				?>
			</fieldset>
			<br>
			
			<fieldset>
				<legend><strong>Title 8 or Title 42</strong></legend>
					 <button type="button" onclick="selectCheck('titles[]')">Select All</button>
					 <button type="button" onclick="deSelectCheck('titles[]')">Clear All</button><br><br>
				<?php
					$stmt = $pdo->prepare("SELECT DISTINCT title_of_authority FROM data WHERE land_border_region = 'Southwest Land Border' ORDER BY title_of_authority ASC;");
					$stmt->execute();
					$result = $stmt->fetchAll(\PDO::FETCH_ASSOC);				
					foreach($result as $sector) {
						echo "	<input type='checkbox' id='$sector[title_of_authority]' name='titles[]' value='$sector[title_of_authority]'><label for='$sector[title_of_authority]'>$sector[title_of_authority]</label>";
					}
				?>
			</fieldset>
			
			<p>
				<input type="submit" class="submitbutton" value="Show the Data"> <button type="button" onclick="clearForm()">Clear the Form</button>
			</p>	
			
		</form>
		
	</body>
</html>

<?php

function fix_weird($co) {
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

?>