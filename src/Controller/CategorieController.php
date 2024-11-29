<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Form\DeleteType;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\Event\ManagerEventArgs;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CategorieController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategorieRepository $repo): Response
    {
        // à l'aide du repository injecte par injection de dépandance
        //on va chercher toutes les catégories

        $categories = $repo->findAll();
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories
        ]);


    }

//    #[Route('/chaton', name: 'app_voir_chatons')]
//    public function chatons(CategorieRepository $repo): Response
//    {
//        // à l'aide du repository injecte par injection de dépandance
//        //on va chercher toutes les catégories
//
//        $categories = $repo->findAll();
//        return $this->render('chaton/index.html.twig', [
//            'categories' => $categories
//        ]);
//    }


    //De quoi ajouter une catégorie

    #[Route('/categorie/ajouter', name: 'app_ajouter_categorie')]
    public function ajouter(Request $request, ManagerRegistry $doctrine): Response
    {
        //Création du formulaire
        //1 Je crée une catégorie vide
        $categorie = new Categorie();

        //2 Je crée le formulaire
        $form = $this->createForm(CategorieType::class, $categorie);

        //3 Gestion du retour en POST
        $form->handleRequest($request);
        if ($form->get('oui')->isClicked()) {
            $em = $doctrine->getManager();
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute('app_home');
        } elseif ($form->get('retour')->isClicked()) {
            return $this->redirectToRoute('app_home');
        }


        return $this->render('categorie/ajouter.html.twig', [
            'formulaire' => $form->createView()
        ]);


    }

    #[Route('/categorie/modifier/{id}', name: 'app_modifier_categorie')]
    public function modifier($id, Request $request, ManagerRegistry $doctrine, CategorieRepository $repo): Response
    {
        //Création du formulaire
        //1 Je crée une catégorie vide
        $categorie = $repo->find($id);

        //2 Je crée le formulaire
        $form = $this->createForm(CategorieType::class, $categorie);

        //3 Gestion du retour en POST
        $form->handleRequest($request);

        //bypass le remplissage du formulaire si le bouton cliqué est "retour"
        if ($form->isSubmitted() && $form->GetClickedButton()->getName() == 'retour') {
            return $this->redirectToRoute('app_home');
        }
        if ($form->isSubmitted() && $form->isValid() && $form->get('oui')->isClicked()) {
            $em = $doctrine->getManager();
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }


        return $this->render('categorie/modifier.html.twig', [
            'formulaire' => $form->createView()
        ]);


    }

    #[Route('/categorie/supprimer/{id}', name: 'app_supprimer_categorie')]
    public function supprimer($id, Request $request, ManagerRegistry $doctrine, CategorieRepository $repo): Response
    {
        //Création du formulaire
        //1 Je crée une catégorie vide
        $categorie = $repo->find($id);

        //2 Je crée le formulaire
        $form = $this->createForm(DeleteType::class, $categorie);

        //3 Gestion du retour en POST
        $form->handleRequest($request);
        //vérification du boutton cliqué (label "oui")
        if ($form->get('oui')->isClicked()) {
            $em = $doctrine->getManager();
            $em->remove($categorie);
            $em->flush();
            return $this->redirectToRoute('app_home');
        } elseif ($form->get('non')->isClicked()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('categorie/supprimer.html.twig', [
            'formulaire_suppr' => $form->createView()
        ]);

    }

}