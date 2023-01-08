<?php
global $wpdb;

// Table name
$key_value_csv=$wpdb->prefix."keyvalue_csv";

// Import CSV
if(isset($_POST['csvimport'])){

  // File extension
  $extension = pathinfo($_FILES['import_csv_file']['name'], PATHINFO_EXTENSION);

  // If file extension is 'csv'
  if(!empty($_FILES['import_csv_file']['name']) && $extension == 'csv'){

    $totalInserted = 0;

    // Open file in read mode
    $csvFile = fopen($_FILES['import_csv_file']['tmp_name'], 'r');

    fgetcsv($csvFile); // Skipping header row

    // Read file
    while(($csvData = fgetcsv($csvFile)) !== FALSE){
      $csvData = array_map("utf8_encode", $csvData);

    $csvlen = count($csvData);

      // Row column length
      $dataLen = count($csvData);

      // Skip row if length != 3
      if( !($dataLen == 3) ) continue;

      // Assign value to variables if name,email,phone
      $row_id=$csvData[0];
      $csv_key=$csvData[1];
      $csv_value=$csvData[2];

      // Check record already exists or not
      $cntSQL = "SELECT count(*) as count FROM {$key_value_csv} where csv_value='".$csv_value."'";
      $record = $wpdb->get_results($cntSQL, OBJECT);

      if($record[0]->count==0){

        // Check if variable is empty or not
        if(!empty($row_id) && !empty($csv_key) && !empty($csv_value) ) {

          // Insert Record
          $wpdb->insert($key_value_csv, array(
            'row_id' =>$row_id,
            'csv_key' =>$csv_key,
            'csv_value' => $csv_value
          ));

          if($wpdb->insert_id > 0){
            $totalInserted++;
          }
        }

      }

    }
    echo "<h3 style='color: green;'>Total record Inserted : ".$totalInserted."</h3>";


  }else{
    echo "<h3 style='color: red;'>Invalid Extension</h3>";
  }

}

?>
<h2>All Entries</h2>

<!-- Form -->
<form method='post' action='<?= $_SERVER['REQUEST_URI']; ?>' enctype='multipart/form-data' id="csv_import_form">
  <input type="file" name="import_csv_file" >
  <button type="submit" name="csvimport">Import</button>
</form><br/>
<hr/><br/>
<!-- Record List -->
<table width='100%' ,border='1'  id="csv_table">
   <thead>
   <tr>
     <th>S.no</th>
     <th>Row id</th>
     <th>Key data</th>
     <th>Value data</th>
   </tr>
   </thead>
   <tbody>
   <?php
   // Fetch records
   $csv_entriesList = $wpdb->get_results("SELECT * FROM ".$key_value_csv." order by id asc");
  if(count($csv_entriesList)>0){
    $count=0;
    foreach($csv_entriesList as $item){
      $id = $item->id;
      $row_id = $item->row_id;
      $csv_key = $item->csv_key;
      $csv_value = $item->csv_value;

      print_r( "<tr>
      <td>".++$count."</td>
      <td>".$row_id."</td>
      <td>".$csv_key."</td>
      <td>".$csv_value."</td>
      </tr>
      ");
   }
   }
   else{
     echo "<tr><td colspan='5'>No record found</td></tr>";
  }
  ?>
  </tbody>
</table>

