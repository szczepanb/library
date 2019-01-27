<?php
namespace App\Library\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use App\Repository\AuthorRepository;

class AuthorExistValidator extends ConstraintValidator
{
    private $repository;

    public function __construct(AuthorRepository $repository)
    {
        $this->repository = $repository;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof AuthorExist) {
            throw new UnexpectedTypeException($constraint, AuthorExist::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_int($value->getId())) {
            throw new UnexpectedValueException($value->getId(), 'int');
        }

        if (!$this->repository->findOneById($value->getId())) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}