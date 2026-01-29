<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UnitModel;
use App\Models\PositionModel;
use App\Models\UnitKerjaModel;
use App\Models\UserModel;

class StrukturController extends BaseController
{
    protected $unitModel;
    protected $positionModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->unitModel     = new UnitKerjaModel();
        $this->positionModel = new PositionModel();
        $this->userModel     = new UserModel();
        $this->db = \Config\Database::connect();  
    }

    public function index()
    {
          
        // $positions = $this->positionModel
        //     ->select('positions.*, unit_kerja.unit_kerja')
        //     ->join('unit_kerja', 'unit_kerja.unit_id = positions.unit_id')
        //     ->orderBy('unit_kerja.unit_kerja', 'ASC')
        //     ->orderBy('positions.level', 'ASC')
        //     ->findAll();

        $positions = $this->positionModel
            ->select('positions.*, unit_kerja.unit_kerja')
            ->join('unit_kerja', 'unit_kerja.unit_id = positions.unit_id', 'left')
            ->orderBy('positions.level', 'ASC')
            ->findAll();


        $positionUsers = $this->db->table('position_user pu')
            ->select('pu.position_id, users.id, users.fullname')
            ->join('users', 'users.id = pu.user_id')
            ->get()
            ->getResultArray();

        $mapUsers = [];
        foreach ($positionUsers as $pu) {
            $mapUsers[$pu['position_id']][] = $pu;
        }

        foreach ($positions as &$pos) {
            $pos['users'] = $mapUsers[$pos['position_id']] ?? [];
        }

        $tree = [];
        $map  = [];

        foreach ($positions as $p) {
            $p['children'] = [];
            $map[$p['position_id']] = $p;
        }

        foreach ($map as $id => &$node) {
            if ($node['parent_id']) {
                $map[$node['parent_id']]['children'][] = &$node;
            } else {
                $tree[] = &$node;
            }
        }

        $units = $this->unitModel->findAll();

        $allPositions = $this->positionModel
            ->select('positions.position_id, positions.name, positions.unit_id, unit_kerja.unit_kerja')
            ->join('unit_kerja', 'unit_kerja.unit_id = positions.unit_id' ,'left')
            ->orderBy('name')
            ->findAll();

        $users = $this->userModel
                    ->select('users.id, users.fullname, users.email')
                    ->join('auth_groups_users agu', 'agu.user_id = users.id')
                    ->join('unit_user uu', 'uu.user_id = users.id')
                    ->where('agu.group_id', 4)
                    ->orderBy('fullname')
                    ->findAll();
                    // dd($users);

        return view('admin/strukturnew', [
            'tree'          => $tree,
            'units'     => $units,
            'allPositions'  => $allPositions,
            'users'         => $users
        ]);
    }

    public function indexold()
    {
          
        // $positions = $this->positionModel
        //     ->select('positions.*, unit_kerja.unit_kerja')
        //     ->join('unit_kerja', 'unit_kerja.unit_id = positions.unit_id')
        //     ->orderBy('unit_kerja.unit_kerja', 'ASC')
        //     ->orderBy('positions.level', 'ASC')
        //     ->findAll();

        $positions = $this->positionModel
            ->select('positions.*, unit_kerja.unit_kerja')
            ->join('unit_kerja', 'unit_kerja.unit_id = positions.unit_id', 'left')
            ->orderBy('positions.level', 'ASC')
            ->findAll();


        $positionUsers = $this->db->table('position_user pu')
            ->select('pu.position_id, users.id, users.fullname')
            ->join('users', 'users.id = pu.user_id')
            ->get()
            ->getResultArray();

        $mapUsers = [];
        foreach ($positionUsers as $pu) {
            $mapUsers[$pu['position_id']][] = $pu;
        }

        foreach ($positions as &$pos) {
            $pos['users'] = $mapUsers[$pos['position_id']] ?? [];
        }

        $tree = [];
        $map  = [];

        foreach ($positions as $p) {
            $p['children'] = [];
            $map[$p['position_id']] = $p;
        }

        foreach ($map as $id => &$node) {
            if ($node['parent_id']) {
                $map[$node['parent_id']]['children'][] = &$node;
            } else {
                $tree[] = &$node;
            }
        }

        $units = $this->unitModel->findAll();

        $allPositions = $this->positionModel
            ->select('positions.position_id, positions.name, positions.unit_id, unit_kerja.unit_kerja')
            ->join('unit_kerja', 'unit_kerja.unit_id = positions.unit_id')
            ->orderBy('name')
            ->findAll();

        $users = $this->userModel
                    ->select('users.id, users.fullname, users.email')
                    ->join('auth_groups_users agu', 'agu.user_id = users.id')
                    ->join('unit_user uu', 'uu.user_id = users.id')
                    ->where('agu.group_id', 4)
                    ->orderBy('fullname')
                    ->findAll();
                    // dd($users);

        return view('admin/struktur', [
            'positions' => $positions,
            'units'     => $units,
            'allPositions'  => $allPositions,
            'users'         => $users
        ]);
    }

    // posistin_id di tabel user
    // public function index()
    // {
    //     $positions = $this->positionModel
    //         ->select('positions.*, unit_kerja.unit_kerja')
    //         ->join('unit_kerja', 'unit_kerja.unit_id = positions.unit_id')
    //         ->orderBy('unit_kerja.unit_kerja', 'ASC')
    //         ->orderBy('positions.level', 'ASC')
    //         ->findAll();

    //     foreach ($positions as &$pos) {
    //         $pos['users'] = $this->userModel
    //             ->where('position_id', $pos['position_id'])
    //             ->findAll();
    //     }

    //     $units = $this->unitModel->findAll();

    //     $allPositions = $this->positionModel
    //         ->select('positions.position_id, positions.name, positions.unit_id, unit_kerja.unit_kerja')
    //         ->join('unit_kerja', 'unit_kerja.unit_id = positions.unit_id')
    //         ->orderBy('name')
    //         ->findAll();

    //     $users = $this->userModel
    //                 ->select('users.id, users.fullname, users.email')
    //                 ->join('auth_groups_users agu', 'agu.user_id = users.id')
    //                 ->join('unit_user uu', 'uu.user_id = users.id')
    //                 ->where('agu.group_id', 4)
    //                 ->orderBy('fullname')
    //                 ->findAll();
    //                 // dd($users);

    //     return view('admin/struktur', [
    //         'positions' => $positions,
    //         'units'     => $units,
    //         'allPositions'  => $allPositions,
    //         'users'         => $users
    //     ]);
    // }

    public function savePosition()
    {
        $id = $this->request->getPost('id');

        $data = [
            'name'      => $this->request->getPost('name'),
            'level'     => $this->request->getPost('level'),
            'parent_id' => $this->request->getPost('parent_id') ?: null,
            'unit_id'   => $this->request->getPost('unit_id') ?: null,
        ];

        if ($id) {
            // UPDATE
            $this->positionModel->update($id, $data);
        } else {
            // INSERT
            $this->positionModel->insert($data);
        }

        return redirect()->back()->with('success', 'Posisi berhasil disimpan');
    }


    public function deletePosition($id)
    {
        $this->positionModel->delete($id);
        return redirect()->back()->with('success', 'Posisi dihapus');
    }

    public function saveUser()
    {
        $this->userModel->save([
            'id'          => $this->request->getPost('id'),
            'fullname'    => $this->request->getPost('fullname'),
            'email'       => $this->request->getPost('email'),
            'position_id' => $this->request->getPost('position_id'),
        ]);

        return redirect()->back()->with('success', 'Data orang berhasil disimpan');
    }

    // public function removeUser($id)
    // {
    //     $this->userModel->update($id, [
    //         'position_id' => null
    //     ]);

    //     return redirect()->back()->with('success', 'Orang dilepas dari posisi');
    // }

    public function assignUser()
    {
        $positionId = $this->request->getPost('position_id');
        $userId     = $this->request->getPost('user_id');

        $db = \Config\Database::connect();

        // hapus dulu kalau sudah ada
        $db->table('position_user')
            ->where('position_id', $positionId)
            ->delete();

        // insert baru
        $db->table('position_user')->insert([
            'position_id' => $positionId,
            'user_id'     => $userId
        ]);

        return redirect()->back()->with('success', 'Posisi berhasil diisi');
    }

    public function removeUser($positionId)
    {
        $db = \Config\Database::connect();
        $db->table('position_user')
            ->where('position_id', $positionId)
            ->delete();

        return redirect()->back()->with('success', 'Orang dihapus dari posisi');
    }

}
