<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSuccessSubscriber implements EventSubscriberInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        // Determine the user's email/identifier
        if (is_object($user) && method_exists($user, 'getUserIdentifier')) {
            $email = $user->getUserIdentifier();
        } elseif (is_object($user) && method_exists($user, 'getEmail')) {
            $email = $user->getEmail();
        } else {
            return;
        }

        // If this is the admin user, redirect to admin dashboard
        if ($email === 'jbalidhia07@gmail.com') {
            $url = $this->urlGenerator->generate('admin_dashboard');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
