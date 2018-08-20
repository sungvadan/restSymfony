<?php
namespace AppBundle\Controller\Api;

use AppBundle\Api\ApiProblem;
use AppBundle\AppBundle;
use AppBundle\Controller\BaseController;
use AppBundle\Entity\Programmer;
use AppBundle\Form\ProgrammerType;
use AppBundle\Form\UpdateProgrammerType;
use AppBundle\Pagination\PaginatedCollection;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

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


        if(!$form->isValid()){
//            header("Content-Type: CLI");
//            dump((string)$form->getErrors(true,false));die();
            return $this->throwApoProblemValidationException($form);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($programmer);
        $em->flush();

        $location = $this->generateUrl('api_programmers_show',[
           'nickname' => $programmer->getNickname()
        ]);

        $response = $this->createApiResponse($programmer,201 );
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

        $response = $this->createApiResponse($programmer);
        return $response;
    }


    /**
     * @Route("/api/programmers", name="api_programmers_collection")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {


        $qb = $this->getDoctrine()
            ->getRepository(Programmer::class)
            ->findAllQueryBuilder();
        $paginatedCollection = $this->get('pagination_factory')
                                        ->createCollection($qb, $request, 'api_programmers_collection');

        $response = $this->createApiResponse($paginatedCollection);

        return $response;
    }

    /**
     * @Route("/api/programmers/{nickname}", name="api_programmers_update")
     * @Method({"PUT","PATCH"})
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

        if(!$form->isValid()){
//            header("Content-Type: CLI");
//            dump((string)$form->getErrors(true,false));die();
            return $this->throwApoProblemValidationException($form);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($programmer);
        $em->flush();


        $response = $this->createApiResponse($programmer);

        return $response;

    }

    /**
     * @Route("/api/programmers/{nickname}", name="api_programmers_delete")
     * @Method("DELETE")
     */
    public function deleteAction($nickname)
    {
        $programmer = $this->getDoctrine()
            ->getRepository(Programmer::class)
            ->findOneByNickname($nickname);
        if($programmer){
            $em = $this->getDoctrine()->getManager();
            $em->remove($programmer);
            $em->flush();
        }

        return new Response(null, 204);
    }

    private function processForm(Request $request, FormInterface $form)
    {
        $body = $request->getContent();
        $data = json_decode($body,true);
        if( null === $data){
            $apiProblem = new ApiProblem(
                400,
                ApiProblem::TYPE_INVALID_BODY_FORMAT
            );
            throw new ApiProblemException($apiProblem);
        }
        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit($data,$clearMissing);
    }


    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }

    private function throwApoProblemValidationException(FormInterface$form)
    {
        $errors = $this->getErrorsFromForm($form);

        $apiProblem = new ApiProblem(
            400,
            ApiProblem::TYPE_VALIDATION_ERROR
        );
        $apiProblem->set('errors', $errors);

        throw new ApiProblemException($apiProblem);
    }

}