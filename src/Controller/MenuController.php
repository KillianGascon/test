<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Finder\Finder;

class MenuController extends AbstractController
{
    public function menu(): Response
    {
        $finder = new Finder();
        $dossiers = $finder->directories()->in('photos');

        return $this->render('menu/menu.html.twig', [
            "dossiers" => $dossiers
        ]);
    }
}
