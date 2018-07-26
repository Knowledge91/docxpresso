<?php
include 'sdk.php';

$options = array(
  'pKey' => '9caad8f9ccd3cfbd2b848d546519c8989fa3b56978603b9dd11cf19353cad25340c08e3b3dc5',
  'docxpressoInstallation' => 'https://cysae.a.docxpresso.com'
);
$sdk = new SDK_Docxpresso\Utils($options);
$data = array(
  'template' => 112
);
echo $sdk->previewDocument($data) . PHP_EOL;

