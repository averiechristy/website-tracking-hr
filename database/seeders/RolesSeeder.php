<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'nama_role' => 'Superadmin',
              
            ],
            [
                'nama_role' => 'Rekrutmen',
              
            ],
            [
                'nama_role' => 'Admin Mitra',
              
            ],
            [
                'nama_role' => 'Admin BU',
              
            ],
            [
                'nama_role' => 'User HO',
              
            ],
            [
                'nama_role' => 'Admin HR',
              
            ],
        ]);
    }
}
