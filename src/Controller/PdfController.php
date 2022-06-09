<?php

namespace App\Controller;

use Jaspersoft\Client\Client;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function index(LoggerInterface $log): Response
    {
        $log->debug("Trying to get pdf");
        $report = $this->jasperClient->reportService()->runReport($this->params->get('jasper-report-path'));

        $dateTime = new \DateTime;
        return new Response($report, Response::HTTP_OK, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="report-' . $dateTime->format('Y-m-d\TH-i-s.z\Z') . '.pdf"',
                'Content-Description' => ' File Transfer',
            ]
        );
    }

}
