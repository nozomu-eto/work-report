<?php

namespace App\Enums;

enum UserRole: string
{
    case Worker = 'worker';
    case Admin = 'admin';
}
