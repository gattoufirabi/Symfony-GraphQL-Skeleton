<?php


namespace App\GraphQL\Formatter;

use App\GraphQL\Exception\UserException;
use Overblog\GraphQLBundle\Event\ErrorFormattingEvent;
use Symfony\Component\Validator\ConstraintViolationInterface;

class Formatter
{
    public function onErrorFormatting(ErrorFormattingEvent $event): void
    {
        $error = $event->getError()->getPrevious();

        if ($error instanceof UserException) {
            $state = [];
            $code = [];
            $violations = $error->getViolations();

            foreach ($violations as $violation) {
                /* @var $violation ConstraintViolationInterface */
                $state[$violation->getPropertyPath()][] = $violation->getMessage();
                $code[$violation->getPropertyPath()][] = $violation->getCode();
            }
            $formattedError = $event->getFormattedError();
            $formattedError->offsetSet('state', $state);
            $formattedError->offsetSet('code', $code);
        }
    }
}
