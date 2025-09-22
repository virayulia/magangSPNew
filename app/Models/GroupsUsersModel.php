<?php

namespace App\Models;

use CodeIgniter\Model;

class GroupsUsersModel extends Model
{
    protected $table            = 'auth_groups_users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'group_id'];
}
