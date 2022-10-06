<?php
namespace App\Controllers;

use App\Models\CommentsModel;
use App\Models\PostsModel;
use App\Models\UsersModel;
use App\Utils\FilterInput;

class AdminController extends Controller
{
    public function index() {
        if($this->isAdmin()){
            $this->twig->display('admin/index.html.twig');     
        }
    }

    public function posts() {
        if($this->isAdmin()){
            $postModel = new PostsModel;
            $allPosts = $postModel->findAll();

            if (FilterInput::getInput($_POST, 'supprimer')) {
                $postId = strip_tags(FilterInput::getInput($_POST, 'postId'));
                $postModel->delete($postId);
                header(('Location: /admin/posts'));
            }


            $this->twig->display('admin/posts/index.html.twig', ['posts' => $allPosts]);     
        }
    }

    public function comments() {
        if($this->isAdmin()){
            $commentModel = new CommentsModel;

            if (FilterInput::getInput($_POST, 'valider')) {
                $commentId = strip_tags(FilterInput::getInput($_POST, 'commentId'));
                $commentArray = $commentModel->find($commentId);
                if($commentArray){
                    $comment = $commentModel->hydrate($commentArray);
                    $comment->setIsValid(1);
                    $comment->update();
                }
                header(('Location: /admin/comments'));
            } elseif (FilterInput::getInput($_POST, 'supprimer')) {
                $commentId = strip_tags(FilterInput::getInput($_POST, 'commentId'));
                $commentModel->delete($commentId);
                header(('Location: /admin/comments'));
            }

            $allComments = $commentModel->findAll();
            $this->twig->display('admin/comments/index.html.twig', ['allComments' => $allComments]);     
        }
    }

    public function add() {
        if($this->isAdmin()){
            $postModel = new PostsModel;

            if (FilterInput::getInput($_POST, 'title') && FilterInput::getInput($_POST, 'content')) {
                $title = strip_tags(FilterInput::getInput($_POST, 'title'));
                $content = strip_tags(FilterInput::getInput($_POST, 'content'));
                $postModel
                    ->settitle($title)
                    ->setContent($content)
                    ->setCreatedAt((new \DateTime())->format('Y-m-d H:i:s'))
                    ->setUpdatedAt((new \DateTime())->format('Y-m-d H:i:s'));
                
                $postModel->create();
                header(('Location: /admin/posts'));
            }
            $this->twig->display('admin/posts/add.html.twig');     
        }
    }

    public function update(int $id) {
        if($this->isAdmin()){
            $postModel = new PostsModel;
            $postArray = $postModel->find($id);
            $post = $postModel->hydrate($postArray);

            if (FilterInput::getInput($_POST, 'title') && FilterInput::getInput($_POST, 'content')) {
                $title = strip_tags(FilterInput::getInput($_POST, 'title'));
                $content = strip_tags(FilterInput::getInput($_POST, 'content'));
                $post
                    ->setTitle($title)
                    ->setContent($content)
                    ->setUpdatedAt((new \DateTime())->format('Y-m-d H:i:s'));
                
                $post->update();
                header(('Location: /admin/posts'));
            }
        
        $this->twig->display('admin/posts/update.html.twig', ['post' => $post]);     
        }

    }


        /**
     * Vérifie si on est admin
     * @return true 
     */
    private function isAdmin()
    {
        // On vérifie si on est connecté et si "ROLE_ADMIN" est dans nos rôles
        if(isset($_SESSION['user']) && in_array('ROLE_ADMIN', $_SESSION['user']['roles'])){
            // On est admin
            return true;
        }else{
            // On n'est pas admin
            $_SESSION['erreur'] = "Vous n'avez pas accès à cette zone";
            header('Location: /post');
            exit;
        }
    }
}