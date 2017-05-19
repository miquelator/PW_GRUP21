<?php
namespace PracticaFinal\Model;

use PracticaFinal\Controller\DatabaseController;
use Symfony\Component\Validator\Constraints\DateTime;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class resize
{
    public function resizeImage(Application $app, $filename){ //rep l'image path

            //The blur factor where &gt; 1 is blurry, &lt; 1 is sharp.
            $imagick = new \Imagick(realpath($filename));

            $imagick->resizeImage(400, 400, imagick::FILTER_LANCZOS, 1, TRUE);
    }

}