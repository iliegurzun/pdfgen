<?php

namespace AppBundle\Controller;

use AppBundle\Service\ConvertorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/convert", name="convert")
     */
    public function convertAction(Request $request)
    {
        /** @var ConvertorService $service */
        $service = $this->get(ConvertorService::SERVICE_NAME);

        return $service->setVariablesAndConvert($request);
    }
}
