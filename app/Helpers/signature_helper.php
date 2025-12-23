<?php

use App\Models\UnitUserModel;
use App\Models\UserModel;
use App\Models\UnitKerjaModel;



function getSignature($unit_id)
{
    $unitUser = new UnitUserModel();
    $user = new UserModel();
    $unit = new UnitKerjaModel();

    // Ambil user dengan eselon tertinggi
    $data = $unitUser
        ->select('users.fullname, unit_kerja.unit_kerja, users.eselon')
        ->join('users', 'users.id = unit_user.user_id')
        ->join('unit_kerja', 'unit_kerja.unit_id = unit_user.unit_id')
        ->where('unit_user.unit_id', $unit_id)
        ->where('users.eselon', '2')  
        ->first();
    
    if ($data) {
        $data = formatSignature($data);
    }

    return $data;  
}

function formatSignature($data) {
    $fullname = removeTitle($data['fullname']);
    $fullname = titleCase($fullname);

    $unit = titleCase($data['unit_kerja']);

    return [
        'fullname' => $fullname,
        'unit_kerja' => $unit,
    ];
}

function removeTitle($name) {
    $parts = explode(',', $name);
    return trim($parts[0]);
}

function titleCase($string) {
    $string = strtolower($string);
    return ucwords($string);
}
