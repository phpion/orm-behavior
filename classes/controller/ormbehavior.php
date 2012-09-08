<?php
class Controller_ORMBehavior extends Controller {
	public function action_index() {		
		$articles = ORM::factory('article')->find_all();
		
		$response = View::factory('orm-behavior/index');
		$response->articles = $articles;
		
		$this->response->body($response);
	}
	
	public function action_view() {
		$id = (int)$this->request->query('id');
		
		$article = ORM::factory('article')->where('article.id', '=', $id)->find();
		
		if ($article->loaded()) {
			if ($this->request->method() == 'POST') {
				$comment = ORM::factory('comment');
				$comment->content = (string)$this->request->post('comment');
				
				$article->save_comment($comment);
				
				$this->request->redirect('ormbehavior/view?id='.$article->id);
			}
			
			$response = View::factory('orm-behavior/view');
			$response->article = $article;
			$response->comments = $article->get_comments();

			$this->response->body($response);
		}
	}
	
	public function action_deletecomment() {
		$id = (int)$this->request->query('id');
		
		$comment = ORM::factory('comment')->where('comment.id', '=', $id)->find();
		
		if ($comment->loaded()) {
			$comment->delete();
		}
		
		$this->request->redirect($this->request->referrer());
	}
}