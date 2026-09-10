<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleCommentFormRequest;
use App\Http\Requests\ToggleReactionFormRequest;
use App\Http\Resources\Api\V1\ArticleResource;
use App\Http\Resources\Api\V1\CommentResource;
use App\Models\Articles\WebsiteArticle;
use App\Models\Articles\WebsiteArticleComment;
use App\Models\User;
use App\Services\Articles\ArticleService;
use App\Services\Articles\CommentService;
use App\Services\Articles\ReactionService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $articles, private readonly CommentService $comments, private readonly ReactionService $reactions) {}

    public function index(): AnonymousResourceCollection
    {
        return ArticleResource::collection($this->articles->getArticles(true));
    }

    public function show(WebsiteArticle $article, Request $request): ArticleResource
    {
        $actor = $request->user();
        $article = $this->articles->loadForDisplay($article);
        $author = $article->user;

        return (new ArticleResource($article))->additional([
            'author_display' => [
                'rank_name' => $author && ! $author->hidden_staff ? $author->permission->rank_name ?? 'Member' : 'Member',
                'background_url' => asset('assets/images/' . ($author->permission->staff_background ?? 'staff-bg.png')),
            ],
            'can_post_comment' => $actor instanceof User && $article->can_comment && ! $article->userHasReachedArticleCommentLimit($actor),
            'reaction_users' => (object) $this->reactions->usersFor($article)->all(),
            'reactions' => (object) $this->reactions->countsFor($article)->all(),
            'my_reactions' => $actor instanceof User ? $this->reactions->reactionsFor($article, $actor) : [],
        ]);
    }

    public function comments(WebsiteArticle $article): AnonymousResourceCollection
    {
        return CommentResource::collection($this->comments->forArticle($article));
    }

    public function storeComment(WebsiteArticle $article, ArticleCommentFormRequest $request): CommentResource
    {
        return new CommentResource($this->comments->store(AuthenticatedUser::from($request), $request->validated('comment'), $article)->load('user:id,username,motto,look,online'));
    }

    public function destroyComment(WebsiteArticleComment $comment, Request $request): Response
    {
        $this->comments->destroy(AuthenticatedUser::from($request), $comment);

        return response()->noContent();
    }

    public function reaction(WebsiteArticle $article, ToggleReactionFormRequest $request): JsonResponse
    {
        return response()->json(['data' => $this->reactions->toggleReaction($article, AuthenticatedUser::from($request), $request->validated('reaction'))]);
    }

    public function setReaction(WebsiteArticle $article, string $reaction, Request $request): Response
    {
        $this->reactions->setReaction($article, AuthenticatedUser::from($request), $reaction, ! $request->isMethod('delete'));

        return response()->noContent();
    }
}
