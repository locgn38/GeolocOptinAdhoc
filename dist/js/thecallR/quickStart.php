<?php
require(dirname(__FILE__).'/html/ExampleViewer.php');

$viewer = new ExampleViewer(dirname(__FILE__).'/examples');

$summary = $viewer->get_summary();

$selected = (array_key_exists('e',$_REQUEST))?$_REQUEST['e']:key($summary);

$html = $viewer->get_example($selected);

if (!is_array($html) || count($html) == 0) {
	$selected = key($summary);
	$html = $viewer->get_example($selected);
}
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>THECALLR SDK for PHP</title>
<link rel="icon" type="image/png" href="html/favicon.png" />
<link type="text/css" rel="stylesheet" href="html/prettify.css" />
<link type="text/css" rel="stylesheet" href="html/examples.css" />
<script type="text/javascript" src="html/prettify.js"></script>
</head>
<body onLoad="prettyPrint()">
<div id="examples-header">
	<img src="html/thecallr_transparent.png" />
</div>
<div id="examples-main">
<h2>THECALLR PHP SDK</h2>
<ul id="examples-summary">
<?php
foreach ($summary as $summary_link=>$summary_label) {
	if ($selected == $summary_link) {
		echo "<li><a href=\"?e=$summary_link\" class=\"selected\">$summary_label</a></li>";
	} else {
		echo "<li><a href=\"?e=$summary_link\">$summary_label</a></li>";
	}
}
?>
</ul>
<?php
foreach ($html as $html_line) {
	echo $html_line;
}
?>
</div>
<div id="examples-footer">&copy; Copyright 2010-2013, THECALLR.</div>
</body>
</html>