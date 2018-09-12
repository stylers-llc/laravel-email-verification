<?php


namespace Stylers\EmailVerification\Tests\Frameworks\Laravel\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Stylers\EmailVerification\EmailVerifiableInterface;
use Stylers\EmailVerification\Frameworks\Laravel\Models\Traits\EmailVerifiable;
use Stylers\EmailVerification\NotifiableInterface;

class User extends Model implements EmailVerifiableInterface, NotifiableInterface
{
    use Notifiable;
    use EmailVerifiable;

    public function getName(): string
    {
        return $this->name;
    }
}