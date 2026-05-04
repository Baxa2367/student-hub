<?php
/**
 * CommentController.php - Comment Management Controller
 * 
 * Handles all comment-related operations.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Notification;

class CommentController extends Controller
{
    private $commentModel;
    private $postModel;
    private $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->commentModel = new Comment();
        $this->postModel = new Post();
        $this->notificationModel = new Notification();
    }

    /**
     * Store new comment
     */
    public function store()
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $postId = intval($_POST['post_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $parentCommentId = intval($_POST['parent_comment_id'] ?? 0);

        $post = $this->postModel->find($postId);

        if (!$post) {
            http_response_code(404);
            die('Post not found');
        }

        if (empty($content)) {
            $_SESSION['errors'] = ['general' => 'Comment cannot be empty'];
            $this->redirect('/student-hub/public/index.php?route=posts/view&id=' . $postId);
        }

        try {
            $commentId = $this->commentModel->create([
                'post_id' => $postId,
                'user_id' => $this->getCurrentUser()['id'],
                'content' => $content,
                'parent_comment_id' => $parentCommentId > 0 ? $parentCommentId : null,
            ]);

            // Notify post author
            if ($post['user_id'] !== $this->getCurrentUser()['id']) {
                $this->notificationModel->notify(
                    $post['user_id'],
                    'comment',
                    'New Comment on Your Post',
                    $this->getCurrentUser()['name'] . ' commented on your post',
                    $postId
                );
            }

            $_SESSION['success'] = 'Comment posted successfully!';
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['general' => 'Failed to post comment: ' . $e->getMessage()];
        }

        $this->redirect('/student-hub/public/index.php?route=posts/view&id=' . $postId);
    }

    /**
     * Delete comment
     */
    public function delete()
    {
        $this->requireAuth();
        
        $commentId = intval($_GET['id'] ?? 0);
        $comment = $this->commentModel->find($commentId);

        if (!$comment || $comment['user_id'] !== $this->getCurrentUser()['id']) {
            http_response_code(403);
            die('Unauthorized');
        }

        $postId = $comment['post_id'];

        try {
            $this->commentModel->delete($commentId);
            $_SESSION['success'] = 'Comment deleted successfully!';
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['general' => 'Deletion failed: ' . $e->getMessage()];
        }

        $this->redirect('/student-hub/public/index.php?route=posts/view&id=' . $postId);
    }
}
