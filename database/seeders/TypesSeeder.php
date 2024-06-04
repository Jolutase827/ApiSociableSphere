<?php

namespace Database\Seeders;

use App\Models\Type;
use Database\Factories\TypeAdvertisementPhotoFactory;
use Database\Factories\TypeAdvertisementVideoFactory;
use Database\Factories\TypePaymentPhotoFactory;
use Database\Factories\TypePaymentVideoFactory;
use Database\Factories\TypePhotoFactory;
use Database\Factories\TypeTextFactory;
use Database\Factories\TypeVideoFactory;
use Illuminate\Database\Seeder;

class TypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Type::factory()->count(1)->state([
            "content"=>"text",
            "has_footer"=>false,
            "has_reward"=>false,
            "has_cost"=>false
        ])->create();
        Type::factory()->count(1)->state([
            "content"=>"photo",
            "has_footer"=>true,
            "has_reward"=>false,
            "has_cost"=>false
        ])->create();
        Type::factory()->count(1)->state([
            "content"=>"video",
            "has_footer"=>true,
            "has_reward"=>false,
            "has_cost"=>false
        ])->create();
        Type::factory()->count(1)->state([
            "content"=>"photo",
            "has_footer"=>true,
            "has_reward"=>true,
            "has_cost"=>false
        ])->create();
        Type::factory()->count(1)->state([
            "content"=>"video",
            "has_footer"=>true,
            "has_reward"=>true,
            "has_cost"=>false
        ])->create();
        Type::factory()->count(1)->state([
            "content"=>"photo",
            "has_footer"=>true,
            "has_reward"=>false,
            "has_cost"=>true
        ])->create();
        Type::factory()->count(1)->state([
            "content"=>"video",
            "has_footer"=>true,
            "has_reward"=>false,
            "has_cost"=>true
        ])->create();
    }
}
