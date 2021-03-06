<?php


namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Program;
use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wild", name="wild_")
 */
class WildController extends AbstractController
{
    /**
     * @Route("/", name="index")
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
    public function show(?string $slug, ProgramRepository $programRepository): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $programRepository->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug' => $slug,
        ]);
    }

    /**
     * @Route("/category/{categoryName}",
     *      name="show_category")
     */
    public function showByCategory(string $categoryName, CategoryRepository $categoryRepository)
    {

        $category = $categoryRepository->findOneBy(['name' => mb_strtolower($categoryName)]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category],
                ['id' => 'desc'],
                3);

        return $this->render('wild/category.html.twig', [
            'programsOfCategory' => $programs,
            'categoryName' => $categoryName
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
            'program' => $program,
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

    /**
     * @Route("/episode/{slug}", name="episode", methods={"GET","POST"})
     * @param Episode $episode
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function showByEpisode(Episode $episode, Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $comment->setAuthor($this->getUser());
            $comment->setEpisode($episode);
            $comment->setDate(new \DateTime());

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('wild_episode', ['slug' => $episode->getSlug()]);
        }

        $season = $episode->getSeason();
        $program = $season->getProgram();
        $comments = $episode->getComments();


        return $this->render('wild/episode.html.twig', [
            'episode' => $episode,
            'season' => $season,
            'program' => $program,
            'comment' => $comment,
            'comments' => $comments,
            'form' => $form->createView(),
        ]);
    }
}
