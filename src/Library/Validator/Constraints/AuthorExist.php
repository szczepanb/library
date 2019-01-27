<?php
namespace App\Library\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AuthorExist extends Constraint
{
    public $message = 'The author not exist.';
}