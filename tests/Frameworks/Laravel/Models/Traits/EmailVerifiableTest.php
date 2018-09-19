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
    public function it_can_get_verification_type()
    {
        /** @var User $user */
        $user = factory(User::class)->make();
        $this->assertNull($user->getVerificationType());
    }
}
