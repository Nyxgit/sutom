<?php

// src/Controller/SutomController.php

namespace App\Controller;

use App\Entity\DailyWord;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SutomController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function index($id, Request $request): Response
    {
        $motParam = $request->query->get('mot');
        $wordFromDatabase = $this->getWordFromDatabase($motParam);
        $currentDate = new \DateTime();
        $dailyWord = $this->entityManager->getRepository(DailyWord::class)->findOneBy(['Date' => $currentDate]);

        if (!$dailyWord) {
            $motAleatoire = $this->getMotAleatoire();

            $dailyWord = new DailyWord();
            $dailyWord->setMot($motAleatoire);
            $dailyWord->setDate($currentDate);

            $this->entityManager->persist($dailyWord);
            $this->entityManager->flush();
        }

        return $this->render('sutom/index.html.twig', [
            'wordToGuess' => $dailyWord->getMot(),
            'id' => $id,
            'wordFromDatabase' => $wordFromDatabase,
        ]);
    }

    private function getMotAleatoire(): string
    {
        $minWordLength = 5;

        do {
            $client = new Client();
            $response = $client->request('GET', 'https://trouve-mot.fr/api/random');

            $decodedResponse = json_decode($response->getBody(), true);
            $motAleatoire = isset($decodedResponse[0]['name']) ? $decodedResponse[0]['name'] : 'Mot non disponible';
        } while (mb_strlen($motAleatoire) < $minWordLength || $this->hasAccents($motAleatoire));

        return $motAleatoire;
    }

    private function hasAccents(string $mot): bool
    {
        return preg_match('/[áàâäéèêëíìîïóòôöúùûü]/u', $mot) === 1;
    }
    private function getWordFromDatabase(int $mot): string
    {
        $dateActuelle = new \DateTime();

        // Obtenez le mois actuel
        $moisActuel = $dateActuelle->format('m');

        // Obtenez l'année actuelle
        $anneeActuelle = $dateActuelle->format('Y');

        //$date = $anneeActuelle + "-" + $moisActuel + "-" + $mot;
        $date = new \DateTime("$anneeActuelle-$moisActuel-$mot");


        $motRepository = $this->entityManager->getRepository(DailyWord::class);

        $old = $motRepository->findOneBy(['Date' => $date]);
        if (!$old) {
            return '';
        } else {
            $dailyWord = $old->getMot();
        }

        return $dailyWord;
    }
}
