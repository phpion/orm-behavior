<h1><?= $article->title ?></h1>
<div>Created at: <?= $article->created_at ?> | <?= $article->count_comments ?> comment(s)</div>

<hr />

<?= nl2br($article->content) ?>

<hr />

<h2>Comments for this article:</h2>

<?php if (count($comments) > 0): ?>
	<?php foreach ($comments as $comment): ?>
	<p><?= $comment->content ?> <?= HTML::anchor('ormbehavior/deletecomment?id='.$comment->id, '[Delete]') ?></p>
	<?php endforeach; ?>
<?php else: ?>
<i>No comments...</i>
<?php endif; ?>

<hr />

<h2>Comment this article!</h2>

<?= Form::open() ?>
	<?= Form::textarea('comment', '') ?>
	<?= Form::submit(NULL, 'Add comment') ?>
<?= Form::close() ?>

<?=  View::factory('profiler/stats') ?>