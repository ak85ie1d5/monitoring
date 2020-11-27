<?php

// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class LuckyController extends AbstractController
{
    public function number()
    {
        $number = random_int(0, 100);

        return $this->render('test.html.twig', [
            'number' => $number
        ]);
    }
}