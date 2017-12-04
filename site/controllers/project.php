<?php

return function ($site, $pages, $page) {
	$title = $page->title()->html();

	return array(
	'title' => $title,
	'images' => $page->medias()->toStructure(),
	);
}

?>
