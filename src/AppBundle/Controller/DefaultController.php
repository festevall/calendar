<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Module\EventModule;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $eventsModule = new EventModule();

        return $this->render('default/index.html.twig', array(
            'csrf_token' => $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate'),
            'events' => json_encode($eventsModule->getUserEvents($user))
        ));
    }

    /**
     * @Route("/get-user-credentials", name="user_credentials")
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserCredentials(Request $request) {
        /** @var User $user */
        $user = $this->container->get('security.context')->getToken()->getUser();

        /** @var \Twig_Environment $twig */
        $twig = $this->container->get('twig');

        $userCalendarView = $twig->render('AppBundle:Ajax:user-calendar.html.twig');
        $userInfoView = $twig->render('AppBundle:Ajax:user-info-container.html.twig', ['user' => $user]);

        return new JsonResponse([
            'status' => true,
            'calendar' => $userCalendarView,
            'user_info_view' => $userInfoView,
        ]);
    }

    /**
     * @Route("/save-event", name="save_event")
     * @param Request $request
     * @return Response
     */
    public function saveEventAction(Request $request) {

        $result['status'] = true;

        $postedEvent = $request->get('event');

        $eventModule = new EventModule($this->container);
        $eventModule->saveEventDetails($postedEvent);

        if(!empty($eventModule->getErrors())) {
            $result['status'] = false;
            $result['description'] = $eventModule->getErrors();
        }

        return new JsonResponse($result);
    }
}
