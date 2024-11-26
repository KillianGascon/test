<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        // On va ajouter un formulaire pour ajouter des dossiers
        $form= $this->createFormBuilder()
            ->add('nom_dossier', TextType::class, ["label"=>"Nom du dossier"])
            ->add('submit', SubmitType::class, ["label"=>"Créer le dossier"])
            ->getForm();

        // Il faut gérer le retour en post
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom_dossier = $data['nom_dossier'];
            $fs = new Filesystem();
            $fs->mkdir("photos/$nom_dossier");

            // On redirige vers la page du dossier
            return $this->redirectToRoute('app_chatons', ['dossier' => $nom_dossier]);
        }


        $finder = new Finder();
        $dossier = $finder->directories()->in('photos');


        return $this->render('home/index.html.twig', [
            'dossiers' => $dossier,
            'formulaire' => $form->createView()
        ]);
    }

    #[Route("/chatons/{dossier}", name: 'app_chatons')]
    public function chatons($dossier, Request $request): Response
    {
        $fs = new Filesystem();
        $chemin = "photos/$dossier";
        if (!$fs->exists($chemin)) {
            throw $this->createNotFoundException("Le dossier $dossier n'existe pas");
        }

        $form= $this->createFormBuilder()
            ->add('photo', FileType::class, ["label"=>"sélectionner le fichier"])
            ->add('submit', SubmitType::class, ["label"=>"ajouter le fichier"])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $photo = $data['photo'];
            $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $photo->getClientOriginalExtension();
            $newFilename = $originalFilename . '.' . $extension;
            $i = 1;

            while (file_exists($chemin . '/' . $newFilename)) {
                $newFilename = $originalFilename . '(' . $i. ')' . '.' . $extension;
                $i++;
            }

            $photo->move($chemin, $newFilename);

            return $this->redirectToRoute('app_chatons', ['dossier' => $dossier]);
        }

        $delete_form= $this->createFormBuilder()
            ->add('submit', SubmitType::class, ["label"=>"supprimer l'image"])
            ->getForm();


        $finder = new Finder();
        $photos = $finder->files()->in($chemin);

        return $this->render('home/chatons.html.twig', [
            'nom_dossier' => $dossier,
            'photos' => $photos,
            'formulaire_chaton' => $form->createView(),
            'delete_form' => $delete_form->createView()
        ]);
    }
}