<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) return;

        // ✅ تحقق من البريد فقط للطلاب
        if (in_array('ROLE_STUDENT', $user->getRoles()) && !$user->isVerified()) {
            throw new CustomUserMessageAuthenticationException(
                'حسابك غير مفعّل. يرجى التحقق من بريدك الإلكتروني أولاً.'
            );
        }


    }

    public function checkPostAuth(UserInterface $user): void {}
}