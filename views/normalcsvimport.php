<?php
global $wpdb;

// Table name
$normal_csv = $wpdb->prefix."normal_csv";

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

      // Row column length
      $dataLen = count($csvData);

      // Skip row if length != 3
      if( !($dataLen == 3) ) continue;

      // Assign value to variables
      $name = trim($csvData[0]);
      $email = trim($csvData[1]);
      $phone = trim($csvData[2]);

      // Check record already exists or not
      $cntSQL = "SELECT count(*) as count FROM {$normal_csv} where email='".$email."'";
      $record = $wpdb->get_results($cntSQL, OBJECT);

      if($record[0]->count==0){

        // Check if variable is empty or not
        if(!empty($name) && !empty($email) && !empty($phone) ) {

          // Insert Record
          $wpdb->insert($normal_csv, array(
            'name' =>$name,
            'email' =>$email,
            'phone' => $phone
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
<table width='100%' ,border='1' id="csv_table">
   <thead>
   <tr>
     <th>S.no</th>
     <th>Name</th>
     <th>Email</th>
     <th>phone</th>
   </tr>
   </thead>
   <tbody>
   <?php
   // Fetch records
   $csv_entriesList = $wpdb->get_results("SELECT * FROM ".$normal_csv." order by id asc");
   if(count($csv_entriesList) > 0){
     $count = 0;
     foreach($csv_entriesList as $entry){
        $id = $entry->id;
        $name = $entry->name;
        $email = $entry->email;
        $phone = $entry->phone;

        print_r( "<tr>
        <td>".++$count."</td>
        <td>".$name."</td>
        <td>".$email."</td>
        <td>".$phone."</td>
        </tr>
        ");
     }
   }else{
     echo "<tr><td colspan='5'>No record found</td></tr>";
  }
  ?>
  </tbody>
</table>

