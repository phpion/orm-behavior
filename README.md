ORM behavior module for Kohana framework
========================================

This module provides simple but powerful tool to extend ORM class with new methods. It also provides so called trigger methods run before/after finding/creating/updating/deleting ORM objects.

Installation
------------

1. Download source from Github.
2. Extract files into your modules directory.
3. Enable orm-behavior module in your bootstrap.php
4. !important! Enable database and orm modules in your bootstrap.php, and configure database access.

Usage
-----

Module contains 2 main classes:
1. ORM_Behaviorable.
2. ORM_Behavior.
First one is a base class for your models (Model_My extends ORM_Behaviorable), second one is a base class for model behaviors.

To attach behavior to model you can use model configuration:

``` php
class Model_Article extends ORM_Behaviorable {
	protected $_behaviors = array(
		'Behavior_Commentable',
		'Behavior_Undeletable',
		'Behavior_I18n' => array(
			'columns' => array(
				'title' => '',
				'content' => ''
			)
		)
	);
}
```

As you can see behaviors array contains behaviors class names. If behavior need additional configuration you can pass it as configuration item value. Code above means that article can have comments, can not be deleted and is multi-lingual (with title and content columns).

You can also use following methods (on ORM object) to modify behaviors in runtime:
1. attach_behavior(ORM_Behavior $behavior)
2. detach_behavior(ORM_Behavior $behavior)
3. has_behavior($behavior)
4. get_behavior($behavior)

If you want to disable behavior just call:

``` php
$orm->get_behavior('Behavior_Class')->disable();
```

Take a look at behaviors classes:

``` php
class Behavior_Commentable extends ORM_Behavior {
	public function get_comments() {		
		return ORM::factory('comment')->get_for_object($this->_orm->pk(), $this->_orm->table_name());
	}
	
	public function save_comment(Model_Comment $comment) {
		$comment->set('object_id', $this->_orm->pk());
		$comment->set('object_type', $this->_orm->table_name());
		
		return $comment->save();
	}
	
	public function trigger_before_find() {
		$query = DB::select(DB::expr('COUNT(comments.id)'))
			->from('comments')
			->where('comments.object_id', '=', DB::expr('`'.$this->_orm->object_name().'`.`'.$this->_orm->primary_key().'`'))
			->where('comments.object_type', '=', $this->_orm->table_name())
			->where('comments.is_deleted', '=', 0)
		;
		
		$this->_orm->db_builder()
			->select(array($query, 'count_comments'))
		;
	}
}
```

This behavior is based on comment model, which has following columns: [id, object_id, object_type, content...]. You can use this model to have comments for many objects of many types. You can have comments for articles, photos, videos... Using this example behavior you don't need do duplicate any code in your models. You just attach behavior to model and have all behavior methods available in model.

You can retrive all comments associated with current article object using get_comments() method. This method does not exist in article model, but $article->get_comments() will call behavior method.

Passing Model_Comment object to save_comment() method you can automaticaly save comment for current object.

And best thing: automaticated counting comments when finding object(s). As a result you can use:

``` php
$articles = ORM::factory('article')->find_all();

foreach ($articles as $article) {
	echo $article->count_comments;
}
```

Wanna next model having comments? No problem, just add this behavior to model and that's it!

Next behavior class is undeletable behavior:

``` php
class Behavior_Undeletable extends ORM_Behavior {
	public function trigger_before_find() {
		$this->_orm->db_builder()
			->where($this->_orm->object_name().'.is_deleted', '=', 0)
		;
	}
	
	public function trigger_before_delete() {
		$this->_orm->set('is_deleted', 1);
		$this->_orm->save();
		
		return static::CHAIN_RETURN;
	}
}
```

This time we don't want to delete an object from database. We just want to hide object. When selecting records from database we want to add condition is_deleted = 0 (trigger_before_find() method).

Before deleting object behavior upadtes is_deleted column and save object. Then we can not allow to execute rest of delete() method code so we return CHAIN_RETURN value.

Last one behavior is an I18n behavior.

``` php
class Behavior_I18n extends ORM_Behavior {
	public function trigger_before_find() {
		$db_builder = $this->_orm->db_builder();
		$table_name = $this->table_name();
		$foreign_key = $this->foreign_key();
		
		$db_builder
			->join($table_name)
			->on($table_name.'.'.$foreign_key, '=', $this->_orm->object_name().'.id')
			->on($table_name.'.lang_id', '=', DB::expr("'".I18n::lang()."'"))
		;
		
		foreach (array_keys($this->_config['columns']) as $column) {
			$db_builder
				->select(array($table_name.'.'.$column, $column))
			;
		}
	}
	
	public function table_name() {
		return isset($this->_config['table_name']) ? $this->_config['table_name'] : $this->_orm->table_name().'_i18n';
	}
	
	public function foreign_key() {
		return isset($this->_config['foreign_key']) ? $this->_config['foreign_key'] : $this->_orm->object_name().'_'.$this->_orm->primary_key();
	}
}
```

This time we use trigger_before_find() method to join translations table and select translations columns. Our main article model doesn't have title and content column. This data is placed in articles_i18n table. But as a result we can use $article->title and $article->content.

Benefits:
--------

Behaviors are really powerful tool. If you want to create multi-lingual page model with title, content, meta_keywords, meta_description you can use created I18n behavior and configure your model:

``` php
class Model_Page extends ORM_Behaviorable {
	protected $_behaviors = array(
		'Behavior_I18n' => array(
			'columns' => array(
				'title' => '',
				'content' => '',
				'meta_keywords' => '',
				'meta_description' => ''
			)
		)
	);
}
```

Now you can use $page->title, $page->content...

Do you have photo model? You can allow to comment photos? Attach commentable behavior:

``` php
class Model_Photo extends ORM_Behaviorable {
	protected $_behaviors = array(
		'Behavior_Commentable'
	);
}
```

That's all! Now you can get comments for photo by calling $photo->get_comments().

Limitations
-----------

Triggers are run when calling ORM methods. If you build queries using database query builder you have to simulate behaviors manually. Take a look at Behavior_Commentable::trigger_before_find():

``` php
$query = DB::select(DB::expr('COUNT(comments.id)'))
	->from('comments')
	->where('comments.object_id', '=', DB::expr('`'.$this->_orm->object_name().'`.`'.$this->_orm->primary_key().'`'))
	->where('comments.object_type', '=', $this->_orm->table_name())
	->where('comments.is_deleted', '=', 0)
;
```

This time we need to add is_deleted = 0 condition manually.

Complete example
----------------

Complete example of using ORM behaviors is included in this module. You should execute install/mysql.sql to create sample tables with sample data. Then you can run Ormbehavior controller.

Cleaning module
---------------

You can clean module by deleting sample files. You can delete all directories EXCEPT classes/kohana and classes/orm.