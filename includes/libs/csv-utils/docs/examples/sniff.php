<html>

  <head>
  
    <title>Csv Uploader</title>

  </head>
  
  <body>

  <pre><?php
  
  if (!empty($_FILES['csv'])) {

      $datalocation = '/home/luke/data/';
  
      set_include_path(get_include_path() . PATH_SEPARATOR . realpath('../../'));
      require_once 'Csv/Dialect.php';
      require_once 'Csv/Sniffer.php';
      
      $filename = $_FILES['csv']['tmp_name'];
      $data = file_get_contents($filename);
      $sniffer = new Csv_Sniffer();
      $dialect = $sniffer->sniff($data);

      require_once 'Csv/Exception.php';
      require_once 'Csv/Exception/FileNotFound.php';
      require_once 'Csv/Reader.php';
      $filename = $_FILES['csv']['tmp_name'];
      try {
          $newfilename = $datalocation . time() . '.csv';
          move_uploaded_file($filename, $newfilename);
          $reader = new Csv_Reader($newfilename, $dialect);
          ?>
          
          <h2>Csv Format</h2>
          <p>Delimiter: <?php echo $dialect->delimiter; ?></p>
          <p>Quote character: <?php echo $dialect->quotechar; ?></p>
          <p>Rows in file: <?php echo $reader->count(); ?></p>
          <p><a href="./sniff.php">Upload another file</a></p>
          
          <?php
          
          echo "<table border='1'>";
          //$row = $reader->getRow();
          //echo "<tr>";
          //foreach ($row as $header) printf("<th>%s</th>", $header);
          //echo "</tr>";
          print_r($reader);
          foreach ($reader as $x) print_r($x);
          while ($row = $reader->getRow()) {
              echo "<tr>";
              foreach ($row as $col) printf("<td>%s</td>", $col);
              echo "</tr>";
          }
          echo "</table>";
      } catch (CSv_Exception_FileNotFound $e) {
          printf("<p>%s</p>", $e->getMessage());
      }
  
  } else {
  
  ?>
  
  <form method="post" action="./sniff.php" enctype="multipart/form-data">
  
      <input type="file" name="csv"> <input type="submit" value="Detect format">
  
  </form>
  
  <?php
  
  }
  
  ?></pre>
  
  </body>
  
</html>