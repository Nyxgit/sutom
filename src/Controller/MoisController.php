<?php
// src/Controller/MoisController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class MoisController extends AbstractController
{
    public function grille($id): Response
    {
        $grid = range(1, cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')));
        $rows = array_chunk($grid, 8);


        $currentMonth = $this->getCurrentMonth();
        $daysInMonth = $this->getDaysInMonth();


        return $this->render('sutom/mois.html.twig', [
            'rows' => $rows,
            'currentMonth' => $currentMonth,
            'daysInMonth' => $daysInMonth,
            'id' => $id,
        ]);
    }

    private function getCurrentMonth(): string
    {
        $currentDate = new \DateTime();
        $currentMonth = $currentDate->format('F');

        $monthTranslations = [
            'January' => 'Janvier',
            'February' => 'Février',
            'March' => 'Mars',
            'April' => 'Avril',
            'May' => 'Mai',
            'June' => 'Juin',
            'July' => 'Juillet',
            'August' => 'Août',
            'September' => 'Septembre',
            'October' => 'Octobre',
            'November' => 'Novembre',
            'December' => 'Décembre',
        ];

        return $monthTranslations[$currentMonth];
    }
    public function getDaysInMonth(): int
    {
        return cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
    }
}
