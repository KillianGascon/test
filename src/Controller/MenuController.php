<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Finder\Finder;

class MenuController extends AbstractController
{
    public function menu(CategorieRepository $repo): Response
    {
        $categories = $repo->findAll();

        return $this->render('menu/menu.html.twig', [
            "categories" => $categories
        ]);
    }
}

