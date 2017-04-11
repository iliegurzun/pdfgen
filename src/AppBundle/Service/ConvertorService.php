<?php
/**
 * Created by PhpStorm.
 * User: iliegurzun
 * Date: 18/03/2017
 * Time: 15:55
 */

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MBence\OpenTBSBundle\Services\OpenTBS;

/**
 * Class ConvertorService
 * @package AppBundle\Service
 */
class ConvertorService
{
    /** @const string */
    const SERVICE_NAME = 'app.convert_engine';

    const TEMPLATES_PATHS = 'uploads/templates/';

    /** @var OpenTBS */
    protected $openTbs;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function setVariablesAndConvert(Request $request)
    {
        $payload = $request->request->get('variables');
        $templateName = $request->get('template').'.odt';
        $odtTemplate = realpath(self::TEMPLATES_PATHS . $templateName);
        if (!file_exists($odtTemplate)) {
            throw new NotFoundHttpException('Template does not exist!');
        }

        $this->getOpenTbs()->LoadTemplate($odtTemplate);
        if (!empty($payload)) {
            // replace variables
            foreach ($payload as $key => $value) {
                $this->getOpenTbs()->MergeField($key, $value);
            }
        }
        // create temp_file
        $tempFile = 'temp_' . $templateName;
        $this->getOpenTbs()->Show(OPENTBS_FILE, $tempFile);
        if (!file_exists($tempFile)) {
            throw new NotFoundHttpException('Temp template not generated.');
        }
        // convert to pdf
        Unoconv::convertToPdf(realpath($tempFile), realpath($tempFile).'pdf');
        $tempPdf = realpath(pathinfo('temp_' . $request->get('template'), PATHINFO_FILENAME) . '.pdf');

        if (!file_exists($tempPdf)) {
            throw new NotFoundHttpException('Pdf Template not created. Please try again!');
        }

        $encodedPdf = chunk_split(base64_encode(file_get_contents($tempPdf)));
        try {
            unlink($tempFile);
            unlink($tempPdf);
        } catch (\Exception $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        }

        return new JsonResponse([
            'content' => $encodedPdf
        ]);
    }

    /**
     * @return OpenTBS
     */
    public function getOpenTbs()
    {
        return $this->openTbs;
    }

    /**
     * @param OpenTBS $openTbs
     * @return ConvertorService
     */
    public function setOpenTbs($openTbs)
    {
        $this->openTbs = $openTbs;
        
        return $this;
    }
}