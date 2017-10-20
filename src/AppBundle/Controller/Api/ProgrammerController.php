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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $programmer = new Programmer($data['nickname'], $data['avatarNumber']);
        $programmer->setTagLine($data['tagLine']);
        $programmer->setUser($this->findUserByUsername('sydorenkovd'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($programmer);
        $em->flush();

        return new Response('It worked. Believe me - I\'m an API');
    }
}