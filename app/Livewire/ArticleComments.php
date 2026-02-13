<?php

namespace App\Livewire;

use App\Models\Articles\WebsiteArticle;
use App\Rules\WebsiteWordfilterRule;
use Livewire\Component;

class ArticleComments extends Component
{
    public WebsiteArticle $article;

    public string $comment = '';

    public function mount(WebsiteArticle $article): void
    {
        $this->article = $article;
    }

    public function postComment(): void
    {
        $this->validate([
            'comment' => ['required', 'string', 'min:2', 'max:255', new WebsiteWordfilterRule],
        ]);

        if ($this->article->userHasReachedArticleCommentLimit()) {
            $this->addError('comment', __('You can only comment :amount times per article', ['amount' => setting('max_comment_per_article')]));

            return;
        }

        if (! $this->article->can_comment) {
            $this->addError('comment', __('This article has been locked from receiving comments'));

            return;
        }

        $this->article->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $this->comment,
        ]);

        $this->comment = '';

        $this->dispatch('comment-posted');
        $this->dispatch('toast', icon: 'success', title: __('Comment posted successfully'));
    }

    public function deleteComment(int $commentId): void
    {
        $comment = $this->article->comments()->find($commentId);

        if (! $comment) {
            return;
        }

        if (! $comment->canBeDeleted()) {
            $this->addError('comment', __('You can only delete your own comments'));

            return;
        }

        $comment->delete();

        $this->dispatch('toast', icon: 'success', title: __('Comment deleted successfully'));
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.article-comments', [
            'article' => $this->article->load(['comments.user']),
        ]);
    }
}
