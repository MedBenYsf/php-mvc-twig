<?php
namespace App\Controllers;

use App\Models\CommentsModel;
use App\Models\PostsModel;
use DateTime;

class PostsController extends Controller
{
    /**
     * Cette méthode affichera une page listant toutes les annonces de la base de données
     * @return void 
     */
    public function index()
    {
        // On instancie le modèle correspondant à la table 'posts'
        $postsModel = new PostsModel;

        // On va chercher toutes les annonces actives
        $posts = $postsModel->findAll();

        // On génère la vue
        $this->twig->display('posts/index.html.twig', compact('posts'));
    }

      /**
     * Affiche 1e post
     * @param int $id Id de post 
     * @return void 
     */
    public function show(int $id)
    {
        // On instancie le modèle
        $postsModel = new PostsModel;
        // On va chercher 1 annonce
        $post = $postsModel->find($id);

        $commentModel = new CommentsModel;
        $postComments = $commentModel->findBy(['post_id' => $id, 'is_valid' => 1]);
        
        if (isset($_SESSION['user']) && !empty($_SESSION['user']['id'])) {
            if (isset($_POST['comment']) && !empty($_POST['comment'])) {
                $content = strip_tags($_POST['comment']);
                $commentModel->setContent($content)
                             ->setUserId($_SESSION['user']['id'])
                             ->setPostId($id)
                             ->setAuthor($_SESSION['user']['first_name']. ' '.$_SESSION['user']['last_name']);
                if (in_array('ROLE_ADMIN', $_SESSION['user']['roles'])) {
                    $commentModel->setIsValid(1);
                } else {
                    $_SESSION['message'] = "Votre commentaire est bien ajouté !un admin doit le valider avant de l'afficher";
                }
                $commentModel->create();
                header(('Location: /posts/show/'.$id));
            }
        }

        // On envoie à la vue
        $this->twig->display('posts/show.html.twig', compact('post', 'postComments'));
    }
}