<?php


namespace Stylers\EmailVerification\Frameworks\Laravel\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Stylers\EmailVerification\Frameworks\Laravel\Contracts\EmailVerifiableInterface;
use Stylers\EmailVerification\Frameworks\Laravel\Models\Traits\EmailVerifiable;

class User extends Model implements EmailVerifiableInterface
{
    use EmailVerifiable;
}