<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 14.08.17
 * Time: 11:34
 */

namespace AppBundle\Handlers;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{

    protected $router;

    protected $security;
    protected $userManager;
    protected $service_container;

    public function __construct(RouterInterface $router, SecurityContext $security, $userManager, $service_container)
    {
        $this->router = $router;
        $this->security = $security;
        $this->userManager = $userManager;
        $this->service_container = $service_container;

    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        if($request->isXmlHttpRequest()) {
            $response = new JsonResponse();
            $result = [
                'status' => true,
                'token' => $token
            ];

            $response->setData($result);

            return $response;
        }

        return new RedirectResponse($this->router->generate('homepage'));
    }
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        if($request->isXmlHttpRequest()) {
            $response = new JsonResponse();

            $result = [
                'status' => false,
                'description' => $exception->getMessage()
            ];

            $response->setData($result);

            return $response;
        }

        return new RedirectResponse($this->router->generate('login_route'));
    }

}