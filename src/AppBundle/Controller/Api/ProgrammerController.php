<?php
namespace AppBundle\Controller\Api;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\Programmer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $body = $request->getContent();
        $data = json_decode($body,true);

        $programmer = new Programmer($data['nickName'], $data['avatarNumber']);
        $programmer->setTagLine($data['tagLine']);
        $programmer->setUser($this->findUserByUsername('weaverryan'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($programmer);
        $em->flush();

        return new Response($body);
    }
}