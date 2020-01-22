<?php


namespace App\Controller;


use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(ProgramRepository $programRepository) : Response
    {
        $programs = $programRepository->findBy([], ['id' => 'DESC'], 4);


        return $this->render('home/index.html.twig',[
            'programs' => $programs
        ]);
    }
}
