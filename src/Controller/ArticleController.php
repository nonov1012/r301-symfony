<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    #[Route('/article/creer', name: 'app_article_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        
        // $article->setTitre('Mon premier article')
        //     ->setTexte('Ullamco officia ea occaecat adipisicing non deserunt deserunt eu cupidatat reprehenderit. Cillum anim laborum et tempor voluptate eu veniam labore aute anim laborum voluptate. Sint quis enim aliqua consectetur fugiat.')
        //     ->setPublie(1)
        //     ->setDate(new DateTimeImmutable());
        //dd($article)
        
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setDate(new DateTimeImmutable());
            $entityManager->persist($article);

            $entityManager->flush();
            $this->addFlash('success', 'Article Créer');
            return $this->redirectToRoute('app_article_list');
        }

        return $this->render('article/creer.html.twig', [
            // 'controller_name' => 'ArticleController',
            // 'titre' => 'Article',
            // 'article' => $article
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/list', name: 'app_article_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les articles de la base de données
        $articles = $entityManager->getRepository(Article::class)->findAll();

        return $this->render('article/list.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/article/update/{id}', name: 'app_article_edit')]
    public function update(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id '.$id
            );
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Sauvegarde des modifications
    
            return $this->redirectToRoute('app_article_list');
        }

        return $this->render('article/creer.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    #[Route('/article/delete/{id}', name: 'app_article_delete')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id '.$id
            );
        }

        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('app_article_list', [
            'id' => $article->getId()
        ]);
    }
}