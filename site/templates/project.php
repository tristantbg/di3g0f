<?php snippet('header') ?>

<div id="page-content" page-type="project">
	
	<div class="slider">

	<div id="mouse-nav"></div>

	<?php foreach ($images as $key => $image): ?>

		<?php if($image = $image->toFile()): ?>
		<?php $isVideo = $image->videofile()->isNotEmpty() || $image->videostream()->isNotEmpty() || $image->videolink()->isNotEmpty() || $image->videoexternal()->isNotEmpty() ?>
	
		<div class="slide" 
		data-id="<?= $key+1 ?>" 
		<?php if($image->caption()->isNotEmpty()): ?>
		data-caption="<?= $image->caption()->kt()->escape() ?>"
		<?php elseif($project->text()->isNotEmpty()): ?>
		data-caption="<?= $project->text()->kt()->escape() ?>"
		<?php endif ?>
		data-media="<?= e($isVideo, 'video', 'image') ?>"
		>
		
		<?php if($isVideo): ?>
		<?php $hasVideoMobile = $image->videostreamMobile()->isNotEmpty() || $image->videoexternalMobile()->isNotEmpty() || $image->videofileMobile()->isNotEmpty(); ?>
			<div class="content video <?= $image->contentSize() ?><?= r($hasVideoMobile, ' desktop') ?>">
				<?php 
				$poster = thumb($image, array('width' => 1500))->url();

				if ($image->videostream()->isNotEmpty() || $image->videoexternal()->isNotEmpty() || $image->videofile()->isNotEmpty()) {
					$video  = '<video class="media js-player"';
					$video .= ' poster="'.$poster.'"';
					if ($image->videostream()->isNotEmpty()) {
						$video .= ' data-stream="'.$image->videostream().'"';
					}
					$video .= ' width="100%" height="100%" controls="false" muted loop playsinline autoplay>';
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
			<?php if ($hasVideoMobile): ?>
				<div class="content video <?= $image->contentSize() ?> mobile">
					<?php 
					$poster = thumb($image, array('width' => 1500))->url();

					if ($image->videostreamMobile()->isNotEmpty() || $image->videoexternalMobile()->isNotEmpty() || $image->videofileMobile()->isNotEmpty()) {
						$video  = '<video class="media js-player mobile"';
						$video .= ' poster="'.$poster.'"';
						if ($image->videostreamMobile()->isNotEmpty()) {
							$video .= ' data-stream="'.$image->videostreamMobile().'"';
						}
						$video .= ' width="100%" height="100%" controls="false" muted loop playsinline autoplay>';
						if ($image->videoexternalMobile()->isNotEmpty()) {
							$video .= '<source src=' . $image->videoexternalMobile() . ' type="video/mp4">';
						} else if ($image->videofileMobile()->isNotEmpty()){
							$video .= '<source src=' . $image->videofileMobile()->toFile()->url() . ' type="video/mp4">';
						}
						$video .= '</video>';
						echo $video;
					}
					else {
						$url = $image->videolinkMobile();
						if ($image->vendor() == "youtube") {
							echo '<div class="media js-player" data-type="youtube" data-video-id="' . $url  . '"></div>';
						} else {
							echo '<div class="media js-player" data-type="vimeo" data-video-id="' . $url  . '"></div>';
						}
					}
				
					?>
				</div>
			<?php endif ?>
		<?php else: ?>
			<div class="content image <?= $image->contentSize() ?>">
				<img class="media lazy" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" 
				data-src="<?= $image->width(1500)->url() ?>" 
				<?php 
				$srcset = '';
				for ($i = 1000; $i <= 2000; $i += 500) $srcset .= $image->width($i)->url() . ' ' . $i . 'w,';
				?>
				data-srcset="<?= $srcset ?>" 
				data-sizes="auto" 
				data-optimumx="1.5" 
				alt="<?= $title.' - © '.$site->title()->html() ?>" height="100%" width="auto" />
				<noscript>
					<img src="<?= thumb($image, array('width' => 1500))->url() ?>" alt="<?= $title.' - © '.$site->title()->html() ?>" width="100%" height="auto" />
				</noscript>
			</div>
		<?php endif ?>
	
		</div>
	
		<?php endif ?>

		<?php endforeach ?>

	<?php endforeach ?>

	</div>

	<div id="slide-description"></div>

</div>

<?php snippet('footer') ?>
