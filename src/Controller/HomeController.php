<?php

namespace App\Controller;

use App\Entity\Website;
use App\Repository\WebsiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(WebsiteRepository $repository): Response
    {
        $websites = $repository->findAll();

        return $this->render('home/index.html.twig', [
            'websites' => $websites
        ]);
    }

    /**
     * @Route("/website/{id}", name="website_show")
     * @param $id int
     * @return Response
     */
    public function show(Website $website)
    {
        return $this->render('home/show.html.twig', [
            'website' => $website
        ]);
    }
}
