<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(1)->create();
        // \App\Models\User:: create([
        //     'first_name' => "Super",
        //     'last_name' => "Admin",
        //     'email' => "superadmin@mail.com",
        //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        //     "role" => "admin",
        //     'remember_token' => Str::random(10),
        // ]);

        \App\Models\MainCategory:: create([
            "name" => "Test Main Category",
        ]);

        // \App\Models\SubCategory:: create([
        //     "sub_cate_name" => "Test Sub Category",
        //     "main_category_id" => 1,
        // ]);

        // \App\Models\Category:: create([
        //     'category_name' => "Test Category",
        //     "main_category_id" => 1,
        //     "sub_category_id" => 1,
        // ]);

        // \App\Models\Brand:: create([
        //     'brand_name' => "Test Brand",
        // ]);

        // $sizes = ["S","M","L","XL"];
        // foreach($sizes as $size){
        //     \App\Models\Size:: create([
        //         "product_size" => $size,
        //     ]);
        // }
    }
}
