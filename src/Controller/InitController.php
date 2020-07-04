<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\InterfellService;


class InitController extends AbstractController
{
    /**
     * @Route("/interfellWallet")
     */
    public function InterfellService(InterfellService $interfellService)
    {
        $soapServer = new \SoapServer('interfellWallet.wsdl');
        $soapServer->setObject($interfellService);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');

        ob_start();
        $soapServer->handle();
        $response->setContent(ob_get_clean());

        return $response;
    }
}
