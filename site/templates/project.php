<?php snippet('header') ?>

<div id="page-content" class="project">
	
	<div class="slider">

	<div id="mouse-nav"></div>

	<?php foreach ($images as $key => $image): ?>

		<?php if($image = $image->toFile()): ?>
		<?php $isVideo = $image->videofile()->isNotEmpty() || $image->videostream()->isNotEmpty() || $image->videolink()->isNotEmpty() || $image->videoexternal()->isNotEmpty() ?>
	
		<div class="slide" 
		data-id="<?= $key+1 ?>" 
		<?php if($image->caption()->isNotEmpty()): ?>
		data-caption="<?= $image->caption()->kt()->escape() ?>"
		<?php elseif($page->text()->isNotEmpty()): ?>
		data-caption="<?= $page->text()->kt()->escape() ?>"
		<?php endif ?>
		data-media="<?= e($isVideo, 'video', 'image') ?>"
		>
		
		<?php if($isVideo): ?>
			<div class="content video <?= $image->contentSize() ?>">
				<?php 
				$poster = thumb($image, array('width' => 1500))->url();

				if ($image->videostream()->isNotEmpty() || $image->videoexternal()->isNotEmpty() || $image->videofile()->isNotEmpty()) {
					$video  = '<video class="media js-player"';
					$video .= ' poster="'.$poster.'"';
					if ($image->videostream()->isNotEmpty()) {
						$video .= ' data-stream="'.$image->videostream().'"';
					}
					$video .= ' width="100%" height="100%" controls="false" loop>';
					if ($image->videoexternal()->isNotEmpty()) {
						$video .= '<source src=' . $image->videoexternal() . ' type="video/mp4">';
					} else if ($image->videofile()->isNotEmpty()){
						$video .= '<source src=' . $image->videofile()->toFile()->url() . ' type="video/mp4">';
					}
					$video .= '</video>';
					echo $video;
				}
				else {
					$url = $image->videolink();
					if ($image->vendor() == "youtube") {
						echo '<div class="media js-player" data-type="youtube" data-video-id="' . $url  . '"></div>';
					} else {
						echo '<div class="media js-player" data-type="vimeo" data-video-id="' . $url  . '"></div>';
					}
				}
				?>
			</div>
		<?php else: ?>
			<div class="content image <?= $image->contentSize() ?>">
				<img class="media lazy" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" 
				data-src="<?= $image->width(1500)->url() ?>" 
				data-srcset="<?= thumb($image, array('width' => 300))->url() ?> 300w, <?= thumb($image, array('width' => 500))->url() ?> 500w, <?= thumb($image, array('width' => 800))->url() ?> 800w, <?= thumb($image, array('width' => 1000))->url() ?> 1000w, <?= thumb($image, array('width' => 1500))->url() ?> 1500w, <?= thumb($image, array('width' => 2000))->url() ?> 2000w, <?= thumb($image, array('width' => 2500))->url() ?> 2500w, <?= thumb($image, array('width' => 3000))->url() ?> 3000w" 
				data-sizes="auto" 
				data-optimumx="1.5" 
				alt="<?= $title.' - © '.$site->title()->html() ?>" height="100%" width="auto" />
				<noscript>
					<img src="<?= thumb($image, array('height' => 1500))->url() ?>" alt="<?= $title.' - © '.$site->title()->html() ?>" height="100%" width="auto" />
				</noscript>
			</div>
		<?php endif ?>
	
		</div>
	
		<?php endif ?>

	<?php endforeach ?>

	</div>

	<div id="slide-description"></div>

</div>

<?php snippet('footer') ?>