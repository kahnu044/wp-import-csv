<?php

global $wpdb;

$head = array();

$donor_csv = $wpdb->prefix . "donor_csv_import";

if (isset($_POST['csvimport'])) {

  // File extension
  $extension = pathinfo($_FILES['import_csv_file']['name'], PATHINFO_EXTENSION);

  // If file extension is 'csv'
  if (!empty($_FILES['import_csv_file']['name']) && $extension == 'csv') {

    $totalInserted = 0;

    // Open file in read mode
    $csvFile = fopen($_FILES['import_csv_file']['tmp_name'], 'r');

    $header = fgetcsv($csvFile); // getting header of csv



    // Read file
    while (($csvData = fgetcsv($csvFile)) !== FALSE) {

      $csvData = array_map("utf8_encode", $csvData);

      // Assign value to variables if name,email,phone
      $form_id = $csvData[0];
      $form_value = serialize($csvData);

      //Get contact form 7 all fiels by Form id
      $donor_form_field = WPCF7_ContactForm::get_instance($form_id);

      if ($donor_form_field) {

        $cf7_all_field = $donor_form_field->scan_form_tags();

        foreach ($cf7_all_field as $item) {

          if ($item->type != "group" && $item->name != "user-id" && $item->name != "" && $item->name != "cf7mls_step-1" && $item->name != "cf7mls_step-2") {
            array_push($head, $item->name);
          }
        }
        for ($i = 1; $i < count($header); $i++) {
          if ($header[$i] != $head[$i - 1]) {
            echo "<h3 style='color: red;'>Invalid Csv File</h3>";
          }
        }
        continue;

        $cntSQL = "SELECT count(*) as count FROM {$donor_csv} where form_value='" . $form_value . "'";
        $record = $wpdb->get_results($cntSQL, OBJECT);

        if ($record[0]->count == 0) {

          // Check if variable is empty or not
          if (!empty($form_value)) {

            // Insert Record
            $wpdb->insert($donor_csv, array(
              'form_id' => $form_id,
              'form_value' => $form_value,
            ));

            if ($wpdb->insert_id > 0) {
              $totalInserted++;
            }
          }
        }
      }else{
        echo 'form id not found';
      }
    }

    echo "<h3 style='color: green;'>Total record Inserted : " . $totalInserted . "</h3>";
  } else {
    echo "<h3 style='color: red;'>Invalid Extension</h3>";
  }
}

?>
<h2>All Entries</h2>

<!-- Form -->
<form method='post' action='<?= $_SERVER['REQUEST_URI']; ?>' enctype='multipart/form-data' id="csv_import_form">
  <input type="file" name="import_csv_file">
  <button type="submit" name="csvimport">Import</button>
</form><br />
<hr />