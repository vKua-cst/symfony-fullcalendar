<?php

namespace App\Controller;

use App\Entity\Calendar;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="api")
     */
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/api/{id}/edit", name="api_event_edit", methods={"PUT"})
     */
    public function editEvent(?Calendar $calendar, Request $request): Response
    {
        // Récupération des données envoyées
        $datas = json_decode($request->getContent());

        if(
            isset($datas->title) && !empty($datas->title) &&
            isset($datas->start) && !empty($datas->start) &&
            isset($datas->end) && !empty($datas->end) &&
            isset($datas->description) && !empty($datas->description) &&
            isset($datas->backgroundColor) && !empty($datas->backgroundColor) &&
            isset($datas->borderColor) && !empty($datas->borderColor) &&
            isset($datas->textColor) && !empty($datas->textColor)
        ) {
            // Toutes les données sont là
            $code = 200;
            // Vérification de l'identifiant
            if(!$calendar) {
                // Instanciation du rendez-vous
                $calendar = new Calendar;
                // Changement de code de retour
                $code = 201;
            }
            $calendar->setTitle($datas->title);
            $calendar->setDescription($datas->description);
            $calendar->setStart(new \Datetime($datas->start));
            if($datas->allDay) {
                $calendar->setEnd(new \Datetime($datas->start));
            } else {
                $calendar->setEnd(new \Datetime($datas->end));
            }
            $calendar->setAllDay($datas->allDay);
            $calendar->setBackgroundColor($datas->backgroundColor);
            $calendar->setBorderColor($datas->borderColor);
            $calendar->setTextColor($datas->textColor);

            $em = $this->getDoctrine()->getManager();
            $em->persist($calendar);
            $em->flush();

            // On retourne le code
            return new Response('OK', $code);
        } else {
            return new Response("Données incomplètes", 404);
        }
        
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
}
