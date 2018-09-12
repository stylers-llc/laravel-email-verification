<?php

namespace Stylers\EmailVerification\Tests\Frameworks\Laravel\Models\Traits;

use Stylers\EmailVerification\Tests\Frameworks\Laravel\BaseTestCase;
use Stylers\EmailVerification\Frameworks\Laravel\EmailVerificationService;
use Stylers\EmailVerification\Tests\Frameworks\Laravel\Fixtures\Models\User;

class EmailVerifiableTest extends BaseTestCase
{
    /**
     * @test
     */
    public function it_can_get_verified_status_default_false()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->assertFalse($user->isEmailVerified());
    }
    /**
     * @test
     */
    public function it_can_set_verified_status()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->setEmailVerified(true);
        $this->assertTrue($user->isEmailVerified());
    }
}
