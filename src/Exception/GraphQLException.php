<?php

/**
 * PHP version 7.4 & Symfony 4.4.
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * https://www.php.net/license/3_01.txt.
 *
 * Keytchens developed by Ben Macha.
 *
 * @category   Symfony Project
 *
 * @author     Ali BEN MECHA       <dali@keytchens.com>
 * @dev        Abdessalem GHOZIA   <ghozia@keytchens.com>
 * @dev        Abdallah LAHBIB     <abdallah@keytchens.com>
 * @dev        Rabï Gattoufi       <rabi@keytchens.com>
 *
 * @copyright  Ⓒ 2018 Cubes.TN
 *
 * @see       https://www.benmacha.tn
 * @see       https://keytchens.com
 * @see       https://keytchens.app
 *
 */

namespace App\Exception;

use Exception;
use GraphQL\Error\UserError as GraphQLUserError;
use InvalidArgumentException;
use Overblog\GraphQLBundle\Error\UserError;
use Overblog\GraphQLBundle\Error\UserErrors;
use Symfony\Component\Form\FormInterface;

class GraphQLException
{

    public function __construct(array $errors, $message = '', $code = 0, ?Exception $previous = null)
    {
        $this->setErrors($errors);
    }

    public function fromArray(array $errors): UserErrors
    {
        return new UserErrors($errors);
    }
    public function setErrors(array $errors): void
    {
        foreach ($errors as $error) {
            $this->addError($error);
        }
    }
    public function addError(GraphQLUserError|string $error): self
    {
        if (\is_string($error)) {
            $error = new UserError($error);
        } elseif (!\is_object($error) || !$error instanceof GraphQLUserError) {
            throw new InvalidArgumentException(\sprintf('Error must be string or instance of %s.', GraphQLUserError::class));
        }

        $this->errors[] = $error;

        return $this;
    }

    /**
     * @param string $message
     */
    public static function fromString(string $message): UserErrors
    {
        return new UserErrors([$message]);
    }

    public static function fromFormErrors(FormInterface $form): UserErrors
    {
        return new UserErrors(self::getPlainErrors($form), 'eeeee', 403);
    }

    /**
     * @param FormInterface $form
     */
    public static function getPlainErrors($form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors = array_merge($errors, static::getPlainErrors($child));
            }
        }

        return $errors;
    }
}
