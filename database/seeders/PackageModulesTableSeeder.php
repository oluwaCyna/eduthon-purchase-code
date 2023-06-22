<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageModulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('package_modules')->delete();
        
        \DB::table('package_modules')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Core',
                'amount' => 0,
                'created_at' => '2022-12-21 18:55:21',
                'updated_at' => '2022-12-21 18:55:21',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Admission',
                'amount' => 0,
                'created_at' => '2022-12-21 18:56:21',
                'updated_at' => '2022-12-21 18:56:21',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Student',
                'amount' => 0,
                'created_at' => '2022-12-21 18:56:56',
                'updated_at' => '2022-12-21 18:56:56',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Academic',
                'amount' => 0,
                'created_at' => '2022-12-21 18:57:07',
                'updated_at' => '2022-12-21 18:57:07',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Guardian',
                'amount' => 0,
                'created_at' => '2022-12-21 18:55:21',
                'updated_at' => '2022-12-21 18:55:21',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Schedule',
                'amount' => 10,
                'created_at' => '2022-12-21 18:56:21',
                'updated_at' => '2022-12-21 18:56:21',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Attendance',
                'amount' => 20,
                'created_at' => '2022-12-21 18:56:56',
                'updated_at' => '2022-12-21 18:56:56',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Grade',
                'amount' => 30,
                'created_at' => '2022-12-21 18:57:07',
                'updated_at' => '2022-12-21 18:57:07',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Fee',
                'amount' => 50,
                'created_at' => '2022-12-21 18:56:56',
                'updated_at' => '2022-12-21 18:56:56',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Account',
                'amount' => 40,
                'created_at' => '2022-12-21 18:57:07',
                'updated_at' => '2022-12-21 18:57:07',
            ),
        ));
        
     
    }
}
