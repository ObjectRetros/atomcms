<?php

namespace App\Livewire;

use App\Models\Articles\WebsiteArticle;
use App\Models\Articles\WebsiteArticleComment;
use App\Rules\WebsiteWordfilterRule;
use App\Services\Articles\CommentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ArticleComments extends Component
{
    #[Locked]
    public WebsiteArticle $article;

    public string $comment = '';

    public function mount(WebsiteArticle $article): void
    {
        $this->article = $article;
    }

    public function postComment(CommentService $comments): void
    {
        $user = Auth::user();
        abort_if($user === null, 403);

        $this->validate([
            'comment' => ['required', 'string', 'min:2', 'max:255', new WebsiteWordfilterRule],
        ]);

        $comments->store($user, $this->comment, $this->article);

        $this->comment = '';

        $this->dispatch('comment-posted');
        $this->dispatch('toast', icon: 'success', title: __('Comment posted successfully'));
    }

    public function deleteComment(int $commentId, CommentService $comments): void
    {
        $user = Auth::user();
        abort_if($user === null, 403);

        /** @var WebsiteArticleComment|null $comment */
        $comment = $this->article->comments()->find($commentId);

        abort_if($comment === null, 404);

        $comments->destroy($user, $comment);

        $this->dispatch('toast', icon: 'success', title: __('Comment deleted successfully'));
    }

    public function render(): View
    {
        return view('livewire.article-comments', [
            'article' => $this->article->load(['comments.user']),
        ]);
    }
}
