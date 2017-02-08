<?php

namespace AppBundle\Security\Authenticator;

use AppBundle\Entity\CustomerUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AuthenticationExpiredException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message' => 'Authentication Required',
        ];
        return new JsonResponse($data,401);
    }

    public function getCredentials(Request $request)
    {
        if ( ! $header = $request->headers->get('Authorization')) {
            return null;
        }

        list($token) = sscanf($header, 'Bearer %s');
        return $token;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $user = $userProvider->loadUserByUsername($credentials);
        } catch (UsernameNotFoundException $e) {
            throw new BadCredentialsException();
        }

        if ( ! $user) {
            throw new BadCredentialsException('Invalid username or password');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        /** @var CustomerUser $user */
        if ($credentials !== $user->getApiToken()){
            throw new BadCredentialsException();
        }

        $tts = $user->getTokenTimestamp()->modify('+1 hour');
        $cts = new \DateTime("now");
        $tokenExpired = $tts < $cts;

        if ($tokenExpired === true) {
            throw new AuthenticationExpiredException('Session has expired, please log in again.');
        }

        $user->setTokenTimestamp(new \DateTime());
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(
            [
                'message' => $exception->getMessageKey()
            ],
            403
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }

}