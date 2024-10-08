<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserVoter extends AbstractVoter
{
    public const CREATE = "USER_CREATE";
    public const VIEW = "USER_VIEW";
    public const EDIT = "USER_EDIT";
    public const DELETE = "USER_DELETE";

    public function __construct(private readonly Security $security)
    {
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /**
         * @var User $subject
         * @var UserInterface $user
         */
        if (!($user = $token->getUser()) instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
            case self::VIEW:
                return true;
            case self::EDIT:
                if ($subject === $user) {
                    return true;
                }
                break;
            case self::DELETE:
                if ($subject === $user) {
                    return true;
                }
                if ($this->security->isGranted("ROLE_ADMIN")) {
                    if (in_array("ROLE_ADMIN", $subject->getRoles())) {
                        return false;
                    }
                    return true;
                }
                break;
        }
        return false;
    }

    protected function getSubjectClass(): string
    {
        return User::class;
    }
}