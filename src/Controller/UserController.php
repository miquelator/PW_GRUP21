<?php
    namespace PracticaFinal\Controller;

    use Silex\Application;
    use Symfony\Component\HttpFoundation\Response;

class UserController
    {
        public function getAction(Application $app, $id)
    {
        //$sql = "select * from user ";
        echo $id;
    }
    public function postAction(Application $app, Request $request){

        var_dump($request);
        $response = new Response();

        $data = array(
            'name' => 'Your name',
            'email' => 'Your email',
        );
        /**@var Form $form*/
        $form = $app['form.factory']->createBuilder(FormType::class, $data)
            ->add('name')
            ->add('email')
            ->add('submit',SubmitType::class,[
                'label' => 'Add user',
                ])
            ->getForm();

        $form->handleRequest($request);

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('used.add.twig', array('form'=>createView()));
        $response->setContent($content);



        return $response;


        }
    }

        ?>
