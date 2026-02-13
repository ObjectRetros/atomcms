<?php

namespace App\Livewire;

use App\Models\Articles\WebsiteArticle;
use App\Rules\WebsiteWordfilterRule;
use App\Services\Articles\CommentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ArticleComments extends Component
{
    public WebsiteArticle $article;

    public string $comment = '';

    public function mount(WebsiteArticle $article): void
    {
        $this->article = $article;
    }

    public function postComment(CommentService $commentService): void
    {
        $this->validate([
            'comment' => ['required', 'string', 'min:2', 'max:255', new WebsiteWordfilterRule],
        ]);

        $user = Auth::user();

        $validation = $commentService->canCreate($this->article, $user);

        if (! $validation['allowed']) {
            $this->addError('comment', $validation['message']);

            return;
        }

        $commentService->create($this->article, $user, $this->comment);

        $this->comment = '';

        $this->dispatch('comment-posted');
        $this->dispatch('toast', icon: 'success', title: __('Comment posted successfully'));
    }

    public function deleteComment(int $commentId, CommentService $commentService): void
    {
        $comment = $this->article->comments()->find($commentId);

        if (! $comment) {
            return;
        }

        if (! $commentService->delete($comment, Auth::user())) {
            $this->addError('comment', __('You can only delete your own comments'));

            return;
        }

        $this->dispatch('toast', icon: 'success', title: __('Comment deleted successfully'));
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.article-comments', [
            'article' => $this->article->load(['comments.user']),
        ]);
    }
}
