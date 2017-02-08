<?php

namespace AppBundle\Security\Authenticator;

use Doctrine\Common\Persistence\ObjectManager;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationFailureHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class UserLoginAuthenticator extends AbstractGuardAuthenticator
{
    /** @var  ObjectManager */
    private $om;

    /** @var  UserPasswordEncoderInterface */
    private $encoder;

    /**
     * @var AuthenticationSuccessHandler
     */
    private $successHandler;

    /**
     * @var AuthenticationFailureHandler
     */
    private $failureHandler;

    /**
     * UserLoginAuthenticator constructor.
     * @param ObjectManager $om
     * @param UserPasswordEncoderInterface $encoder
     * @param AuthenticationFailureHandler $failureHandler
     * @param AuthenticationSuccessHandler $successHandler
     */
    public function __construct(ObjectManager $om, UserPasswordEncoderInterface $encoder,
        AuthenticationFailureHandler $failureHandler, AuthenticationSuccessHandler $successHandler
    )
    {
        $this->om = $om;
        $this->encoder = $encoder;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
    }

    public function getCredentials(Request $request)
    {
        if ($request->getPathInfo() != '/login_check') {
            return null;
        }

        return $this->parseCredentials($request);
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $userProvider->loadUserByUsername($credentials['username']);
        if (! $user) {
            throw new BadCredentialsException();
        }
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $plainPassword = $credentials['password'];
        $valid = $this->encoder->isPasswordValid($user,$plainPassword);

        if (! $valid) {
            throw new BadCredentialsException();
        }

        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return $this->failureHandler->onAuthenticationFailure($request,$exception);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return $this->successHandler->onAuthenticationSuccess($request,$token);
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse(
            [
                'message' => 'Authentication Required'
            ],
            401
        );
    }

    private function parseCredentials(Request $request)
    {
        $credentials = json_decode($request->getContent(), true);

        if (null === $credentials ||
            ! array_key_exists('username', $credentials) ||
            ! array_key_exists('password', $credentials)
        ) {
            throw new BadRequestHttpException('Credentials not formatted properly');
        }

        return $credentials;
    }
}