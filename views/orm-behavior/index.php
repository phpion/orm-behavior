<h1>Articles list</h1>

<?php foreach ($articles as $article): ?>
<h2><?= HTML::anchor('ormbehavior/view?id='.$article->id, $article->title) ?></h2>
<div>Created at: <?= $article->created_at ?> | <?= $article->count_comments ?> comment(s)</div>
<?php endforeach; ?>

<?=  View::factory('profiler/stats') ?>