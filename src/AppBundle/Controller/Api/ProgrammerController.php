<?php
/**
 * Created by PhpStorm.
 * User: sydorenkovd
 * Date: 20.10.17
 * Time: 14:05
 */

namespace AppBundle\Controller\Api;

use AppBundle\Controller\BaseController;
use AppBundle\Entity\Programmer;
use AppBundle\Form\ProgrammerType;
use AppBundle\Repository\ProgrammerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgrammerController extends BaseController
{
    /**
     * @Route("/api/programmers")
     * @Method("POST")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $programmer = new Programmer();
        $form = $this->createForm(new ProgrammerType(), $programmer);
        $form->submit($data);



        $programmer->setUser($this->findUserByUsername('sydorenkovd'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($programmer);
        $em->flush();


        $data = $this->serializeProgrammer($programmer);
        $response = new JsonResponse($data, 201);
        $programmerUrl = $this->generateUrl(
            'api_programmers_show',
            ['nickname' => $programmer->getNickname()]
        );
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Location', $programmerUrl);
        return $response;
    }
    /**
     * @Route("/api/programmers")
     * @Method("GET")
     */
    public function listAction()
    {
        $programmers = $this->getDoctrine()
        ->getRepository('AppBundle:Programmer')
        ->findAll();
        $data = array('programmers' => array());
        foreach ($programmers as $programmer) {
            $data['programmers'][] = $this->serializeProgrammer($programmer);
        }
        $response = new Response(json_encode($data), 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    private function serializeProgrammer(Programmer $programmer)
    {
        return array(
            'nickname' => $programmer->getNickname(),
            'avatarNumber' => $programmer->getAvatarNumber(),
            'powerLevel' => $programmer->getPowerLevel(),
            'tagLine' => $programmer->getTagLine(),
        );
    }
    /**
     * @Route("/api/programmers/{nickname}",  name="api_programmers_show")
     * @Method("GET")
     */
    public function showAction($nickname)
    {
        $programmer = $this->getDoctrine()
            ->getRepository('AppBundle:Programmer')
            ->findOneByNickname($nickname);
        if (!$programmer) {
            throw $this->createNotFoundException(sprintf(
                'No programmer found with nickname "%s"',
                $nickname
            ));
        }
        $data = $this->serializeProgrammer($programmer);
        $response = new Response(json_encode($data), 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}