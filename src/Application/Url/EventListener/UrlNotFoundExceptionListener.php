<?php

namespace Application\Url\EventListener;

use Domain\Url\Exceptions\UrlNotFoundException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
final class UrlNotFoundExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof UrlNotFoundException) {
            return;
        }

        $event->setThrowable(new NotFoundHttpException($exception->getMessage(), $exception));
    }
}
