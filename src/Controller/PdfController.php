<?php

namespace App\Controller;

use DateTime;
use DateTimeInterface;
use Jaspersoft\Client\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Sergio Villanueva <sergiovillanueva@protonmail.com>
 */
class PdfController extends AbstractController
{
    private Client $jasperClient;
    private ContainerBagInterface $params;

    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
        $this->jasperClient = new Client(
            $params->get('jasper-url'),
            $params->get('jasper-username'),
            $params->get('jasper-password'),
        );
    }

    #[Route('/pdf', name: 'pdf', methods: ['GET'])]
    public function index(): Response
    {

        $report = $this->jasperClient->reportService()->runReport($this->params->get('jasper-report-path'));

        $dateTime = new \DateTime;
        return new Response($report, 200, array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="report-' . $dateTime->format('Y-m-d\TH:i:s.z\Z') . '.pdf"'
            )
        );
    }


}