<?php snippet('header') ?>

<div id="page-content" class="projects-list">

	<?php foreach ($projects as $key => $project): ?>

		<?php if($project->featured()->isNotEmpty()): ?>

		<?php $featured = $project->featured()->toFile() ?>

			<div class="project-item">

				<?= $project->title()->html() ?>
				
			</div>

		<?php endif ?>

	<?php endforeach ?>

</div>

<?php snippet('footer') ?>