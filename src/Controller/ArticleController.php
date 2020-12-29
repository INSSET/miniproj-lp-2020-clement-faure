<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
    * @Route("/", name="articles")
    */
    public function index(): Response
    {


        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
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

            $creation_date = new DateTime();
            $article->setCreationDate($creation_date);
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
}
