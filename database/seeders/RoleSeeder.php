<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Roles
        $adminRole = Role::create(['name' => 'admin']);
        $employeeRole = Role::create(['name' => 'employee']);

        // Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123')
        ]);
        $admin->assignRole($adminRole);

        // Employee
        $employee = User::create([
            'name' => 'Employee User',
            'email' => 'employee@gmail.com',
            'password' => bcrypt('employee123')
        ]);
        $employee->assignRole($employeeRole);

        $employee2 = User::create([
            'name' => 'Employee User 2',
            'email' => 'employeeuser2@gmail.com',
            'password' => bcrypt('employee123')
        ]);
        $employee2->assignRole($employeeRole);
        $employee3 = User::create([
            'name' => 'Employee User 3',
            'email' => 'employeeuser3@gmail.com',
            'password' => bcrypt('employee123')
        ]);
        $employee3->assignRole($employeeRole);
        
        $employee4 = User::create([
            'name' => 'Employee User 4',
            'email' => 'employeeuser4@gmail.com',
            'password' => bcrypt('employee123')
        ]);
        $employee4->assignRole($employeeRole);

        $employee5 = User::create([
            'name' => 'Employee User 5',
            'email' => 'employeeuser5@gmail,com',
            'password' => bcrypt('employee123')
        ]);
        $employee5->assignRole($employeeRole);
    }
}
