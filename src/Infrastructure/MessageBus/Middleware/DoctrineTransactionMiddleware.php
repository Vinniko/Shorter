<?php

namespace Infrastructure\MessageBus\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Messenger\AbstractDoctrineMiddleware;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Throwable;

final class DoctrineTransactionMiddleware extends AbstractDoctrineMiddleware
{
    protected function handleForManager(EntityManagerInterface $entityManager, Envelope $envelope, StackInterface $stack): Envelope
    {
        $entityManager->getConnection()->beginTransaction();

        try {
            $envelope = $stack->next()->handle($envelope, $stack);
            $entityManager->flush();
            $entityManager->getConnection()->commit();

            return $envelope;
        } catch (Throwable $exception) {
            $entityManager->getConnection()->rollBack();

            if ($exception instanceof HandlerFailedException) {
                throw new HandlerFailedException($exception->getEnvelope()->withoutAll(HandledStamp::class), $exception->getWrappedExceptions());
            }

            throw $exception;
        }
    }
}
