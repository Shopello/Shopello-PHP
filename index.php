<?php
header('Content-type: text/html; charset=utf8');

include('Shopello.php');

$limit = 25;
$offset = (!isset($_GET['offset'])) ? 0 : (int) $_GET['offset'];
$shopello = new Shopello();

echo '<ul>';

foreach($shopello->products(array(
	'offset' => $offset,
	'limit' => $limit
))->data as $product){
	echo '<li><a href="' . $product->url . '">' . $product->name . '</a></li>';
}

echo '</ul>';

if($offset !== 0){
	echo '<a href="?offset=' . ($offset - 25) . '">Föregående</a> | ';
}

echo '<a href="?offset=' . ($offset + 25) . '">Nästa</a>';