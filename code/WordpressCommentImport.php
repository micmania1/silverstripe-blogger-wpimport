<?php

class WordpressCommentImport extends Extension {

	protected $comments = array();

	public function addAttachments(SimpleXMLElement $item, BlogPost $blogPost) {
		$comments = $item->children($this->owner->namespaces['wp'])->comment;
		foreach($comments as $comment) {
			$c = Comment::create();
			$c->Name = (string) $comment->comment_author;
			$c->Email = (string) $comment->comment_author_email;
			$c->URL = (string) $comment->comment_author_url;
			$c->Comment = (string) $comment->comment_content;
			$c->Created = (string) $comment->comment_date;
			$c->Moderated = !!$comment->comment_approved;
			$c->WordpressID = intval($comment->comment_id);
			$c->ParentID = $blogPost->ID;
			$this->owner->extend("beforeImportComment", $comment);
			if(!$this->owner->isDryRun()) {
				$c->write();
			}
			$this->owner->addImportedObject("Comment", $c);
		}
	}


	public function beforeImportBlogPost(SimpleXMLElement $element, BlogPost $blogPost) {
		$post = $element->children($this->owner->namespaces['wp']);
		$status = (string) $post->wp_comment;
		$blogPost->ProvideComments = ($status == "open") ? true : false;
	}

}