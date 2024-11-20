<?php

namespace App\GraphQL\Exception;

use Exception;
use GraphQL\Error\UserError;
use Symfony\Component\Form\FormInterface;

class ErrorStateException extends UserError
{
    private array $errors;

    public function __construct(array $errors, $message = '', $code = 0, ?Exception $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function fromFormErrors(FormInterface $form, $message): ErrorStateException
    {
        $data = self::getPlainErrors($form);

        return new self(['input' => $data], $message, 403);
    }

    public static function getPlainErrors(FormInterface $form): array
    {
        $errors = [];
        $name = $form->getName();
        if ($form->getParent() && $form->getParent()->getName() != $name && $form->getParent()->getName()) {
            $name = $form->getParent()->getName() . '.' . $name;
        }
        foreach ($form->getErrors() as $key => $error) {
            $errors[] = [
                'message' => $error->getMessage(),
                'path' => $name,
            ];
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors = array_merge($errors, static::getPlainErrors($child));
            }
        }

        return $errors;
    }
}
