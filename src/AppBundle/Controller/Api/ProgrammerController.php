<?php
namespace AppBundle\Controller\Api;

use AppBundle\AppBundle;
use AppBundle\Controller\BaseController;
use AppBundle\Entity\Programmer;
use AppBundle\Form\ProgrammerType;
use AppBundle\Form\UpdateProgrammerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProgrammerController extends BaseController
{

    /**
     * @Route("/api/programmers")
     * @Method("POST")
     */
    public function newAction(Request $request)
    {
        $programmer = new Programmer();
        $programmer->setUser($this->findUserByUsername('weaverryan'));

        $form = $this->createForm(new ProgrammerType() , $programmer);
        $this->processForm($request, $form);

        $em = $this->getDoctrine()->getManager();
        $em->persist($programmer);
        $em->flush();

        $location = $this->generateUrl('api_programmers_show',[
           'nickname' => $programmer->getNickname()
        ]);
        $data =  $this->serializeProgrammer($programmer);

        $response = new JsonResponse($data,201);
        $response->headers->set('Location',$location);

        return $response;
    }


    /**
     * @Route("/api/programmers/{nickname}", name="api_programmers_show")
     * @Method("GET")
     */
    public function showAction($nickname)
    {
        $programmer = $this->getDoctrine()
            ->getRepository(Programmer::class)
            ->findOneByNickname($nickname);
        if(!$programmer){
            throw $this->createNotFoundException('No programmer found for username '.$nickname);

        }

        $data  = $this->serializeProgrammer($programmer);
        $response = new JsonResponse($data);
        return $response;
    }


    /**
     * @Route("/api/programmers")
     * @Method("GET")
     */
    public function listAction()
    {
        $programmers = $this->getDoctrine()
            ->getRepository(Programmer::class)
            ->findAll();

        $data = ['programmers' => []];
        foreach ($programmers as $programmer){
            $data['programmers'][] = $this->serializeProgrammer($programmer);
        }

        $response = new JsonResponse($data);

        return $response;
    }

    /**
     * @Route("/api/programmers/{nickname}", name="api_programmers_update")
     * @Method("PUT")
     */
    public function updateAction($nickname, Request $request)
    {
        $programmer = $this->getDoctrine()
            ->getRepository(Programmer::class)
            ->findOneByNickname($nickname);
        if(!$programmer){
            throw $this->createNotFoundException('No programmer found for username '.$nickname);
        }

        $form = $this->createForm(new UpdateProgrammerType(), $programmer);

        $this->processForm($request, $form);

        $em = $this->getDoctrine()->getManager();
        $em->persist($programmer);
        $em->flush();


        $data =  $this->serializeProgrammer($programmer);

        $response = new JsonResponse($data,200);

        return $response;

    }

    private function processForm(Request $request, FormInterface $form)
    {
        $body = $request->getContent();
        $data = json_decode($body,true);

        $form->submit($data);
    }

    private function serializeProgrammer(Programmer $programmer)
    {
        return [
            'nickname' => $programmer->getNickname(),
            'avatarNumber' => $programmer->getAvatarNumber(),
            'powerLevel' => $programmer->getPowerLevel(),
            'tagLine' => $programmer->getTagLine()
        ];
    }

}