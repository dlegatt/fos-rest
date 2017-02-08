<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CustomerAccount;
use AppBundle\Entity\CustomerUser;
use AppBundle\Form\CustomerUserType;
use AppBundle\Service\AccountVerificationService;
use AppBundle\Service\CreditAlertService;
use JMS\Serializer\Handler\FormErrorHandler;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/login", name="security_login")
     */
    public function loginAction()
    {
        $data = [];
        $helper = $this->get('security.authentication_utils');
        $status = $helper->getLastAuthenticationError() ? 400 : 200;
        $data['error'] = $helper->getLastAuthenticationError();
        return $this->json($data,$status);
    }

    /**
     * @Route("/login_check", name="security_login_check")
     */
    public function loginCheckAction()
    {

    }
}