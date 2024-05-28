<?php
// List all files in the uploads directory
$files = glob('uploads/*');
echo "<pre>";
print_r($files);
echo "</pre>";
?>