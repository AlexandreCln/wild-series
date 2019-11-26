<?php


namespace App\Controller;

use App\Entity\Program;
use App\Entity\Season;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wild", name="wild_")
 */
class WildController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        return $this->render(
            'wild/index.html.twig',
            ['programs' => $programs]
        );
    }

    /**
     * @Route("/show/{slug}",
     *      requirements={"slug"="[a-z0-9-]+"},
     *      defaults={"slug" = null},
     *      name="show")
     */
    public function show(?string $slug):Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
        ]);
    }

    /**
     * @Route("/category/{categoryName}",
     *      name="show_category")
     */
    public function showByCategory(string $categoryName, CategoryRepository $categoryRepository)
    {

//        $categoryRepository = $this->getDoctrine()
//            ->getRepository(Category::class);

        $category = $categoryRepository->findOneBy(['name' => mb_strtolower($categoryName)]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category],
                ['id' => 'desc'],
                3);

        return $this->render('wild/category.html.twig', [
            'programsOfCategory' => $programs,
            'categoryName'  => $categoryName
        ]);
    }

    /**
     * @Route("/program/{programName}",
     *      name="program")
     */
    public function showByProgram(string $programName, ProgramRepository $programRepository)
    {
        if (!$programName) {
            throw $this
                ->createNotFoundException('No program has been sent to find a program in program\'s table.');
        }

        $programName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($programName)), "-")
        );

        $program = $programRepository->findOneBy(['title' => $programName]);

        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program]);

        return $this->render('wild/program.html.twig', [
            'seasons' => $seasons,
            'programName' => $programName
        ]);
    }

    /**
     * @Route("/season/{id}",
     *      name="season")
     */
    public function showBySeason(int $id, SeasonRepository $seasonRepository)
    {
        $season = $seasonRepository->findOneBy(['id' => $id]);
        $program = $season->getProgram();
        $episodes = $season->getEpisodes();

        return $this->render('wild/season.html.twig', [
            'season' => $season,
            'program' => $program,
            'episodes' => $episodes
        ]);
    }
}
