<?php
require("html2text/html2text.php");

echo Html2Text\Html2Text::convert(file_get_contents($argv[1]));

?>