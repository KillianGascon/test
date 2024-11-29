<?php

namespace App\Controller;

use App\Entity\Chaton;
use App\Form\ChatonType;
use App\Repository\ChatonRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chaton')]
final class ChatonController extends AbstractController
{
    #[Route(name: 'app_chaton_index', methods: ['GET'])]
    public function index(ChatonRepository $chatonRepository): Response
    {
        return $this->render('chaton/index.html.twig', [
            'chatons' => $chatonRepository->findAll(),
        ]);
    }

    #[Route('/categorie/{id}', name: 'app_chaton_by_categorie', methods: ['GET'])]
    public function indexByCategorie(int $id, ChatonRepository $chatonRepository, CategorieRepository $categorieRepository): Response
    {
        // Vérifie si la catégorie existe
        $Categorie = $categorieRepository->find($id);
        if (!$Categorie) {
            throw $this->createNotFoundException('Catégorie introuvable');
        }

        // Filtre les chatons par catégorie
        $chatons = $chatonRepository->findBy(['Categorie' => $Categorie]);

        return $this->render('chaton/index.html.twig', [
            'chatons' => $chatons,
            'Categorie' => $Categorie,
        ]);
    }


    #[Route('/new/{id}', name: 'app_chaton_new', methods: ['GET', 'POST'])]
    public function new(int $id,Request $request, CategorieRepository $categorieRepository, EntityManagerInterface $entityManager): Response
    {
        $Categorie = $categorieRepository->find($id);
        if (!$Categorie) {
            throw $this->createNotFoundException('Catégorie introuvable');
        }

        $chaton = new Chaton();
        $form = $this->createForm(ChatonType::class, $chaton);
        $form->handleRequest($request);

        // Récupérer le répertoire de téléchargement depuis les paramètres de configuration
        $uploadsDirectory = $this->getParameter('uploads_directory');

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('Photo')->getData();

            if ($photoFile) {
                // Générer un nom de fichier unique
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();

                // Déplacer le fichier dans le répertoire d'uploads
                $photoFile->move(
                    $uploadsDirectory,
                    $newFilename
                );

                // Mettre à jour l'entité avec le chemin de l'image
                $chaton->setPhoto($newFilename);
            }

            $entityManager->persist($chaton);
            $entityManager->flush();

            return $this->redirectToRoute('app_chaton_by_categorie', ["id"=> $chaton->getCategorie()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chaton/new.html.twig', [
            'chaton' => $chaton,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{id}', name: 'app_chaton_show', methods: ['GET'])]
    public function show(Chaton $chaton): Response
    {
        return $this->render('chaton/show.html.twig', [
            'chaton' => $chaton,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chaton_edit', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_chaton_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Chaton $chaton, EntityManagerInterface $entityManager, ParameterBagInterface $params): Response
{
    // Récupérer le répertoire d'upload depuis les paramètres
    $uploads_Directory = $params->get('uploads_directory');

    $form = $this->createForm(ChatonType::class, $chaton);
    $form->handleRequest($request);

    // Vérification si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer le fichier téléchargé
        $photoFile = $form->get('Photo')->getData();

        if ($photoFile) {
            // Générer un nouveau nom pour l'image
            $newFilename = uniqid() . '.' . $photoFile->guessExtension();

            // Déplacer l'image vers le répertoire uploads
            $photoFile->move(
                $uploads_Directory,
                $newFilename
            );

            // Mettre à jour le champ photo avec le nouveau nom du fichier
            $chaton->setPhoto($newFilename);
        } else {
            // Si aucune nouvelle photo n'est téléchargée, conserver l'ancienne photo
            // Cela ne fait rien, le champ photo restera inchangé
        }

        // Sauvegarder les modifications en base de données
        $entityManager->flush();

        return $this->redirectToRoute('app_chaton_by_categorie', ["id"=> $chaton->getCategorie()->getId()], Response::HTTP_SEE_OTHER);
    }

    return $this->render('chaton/edit.html.twig', [
        'chaton' => $chaton,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_chaton_delete', methods: ['POST'])]
    public function delete(Request $request, Chaton $chaton, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chaton->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($chaton);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chaton_by_categorie', ["id"=> $chaton->getCategorie()->getId()], Response::HTTP_SEE_OTHER);
    }
}
