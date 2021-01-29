<?php

namespace App\Controller;

use App\Entity\Article;
use App\Controller\CommentController;
use App\Entity\Comment;
use App\Form\ArticleType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ArticleController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
    * @Route("/", name="articles")
    */
    public function index(): Response
    {

        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();



        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/create", name="article_create")
     */
    public function create(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * $file = $article->getImage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move('%kernel.root_dir%/images', $fileName);
             */
            $article->setImage("");

            $creation_date = new DateTime();
            $article->setCreationDate($creation_date);

            if ($form['published']->getData() == true) {
                $publication_date = new DateTime();
                $article->setPublicationDate($publication_date);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();


            // do anything else you need here, like send an email
            // in this example, we are just redirecting to the homepage
            return $this->redirectToRoute('accueil');
        }

        return $this->render('article/create.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/{id}", name="article_show")
     */

    public function show(int $id, Request $request) : Response
    {
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);

        // Formulaire pour créer les commentaires

        $comment = new Comment();
        $form = $this->createFormBuilder($comment)
            ->add('content', TextareaType::class, array('label' => false))
            ->add('save', SubmitType::class, array('label' => 'Comment'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $creation_date = new DateTime();
            $comment->setCreationDate($creation_date);

            $user = $this->security->getUser();

            $user->addComment($comment);
            $article->addComment($comment);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy(['idArticle' => $article]);

        return $this->render('article/show.html.twig',[
            'article' => $article,
            'commentForm' => $form->createView(),
            'comments' => $comments,
        ]);

    }

    public function getAllArticles() : Response
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        return $this->render('article/list.html.twig',[
            'articles' => $articles,
        ]);
    }
}
