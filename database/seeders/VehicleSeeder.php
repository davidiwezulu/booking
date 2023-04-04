<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VehicleMake::factory()
            ->afterCreating(static function (VehicleMake $vehicleMake) {
                $model = VehicleModel::factory()->create([
                    'name' => 'C Class',
                    'vehicle_make_id' => $vehicleMake->id,
                ]);
                Vehicle::factory()->create([
                    'name' => 'Benz saloon',
                    'registration' => '123456',
                    'vehicle_model_id' => $model->id,
                    'vehicle_make_id' => $vehicleMake->id,
                ]);
            })
            ->create(['name' => 'Mercedes benz']);

        VehicleMake::factory()
            ->afterCreating(static function (VehicleMake $vehicleMake) {
                $model = VehicleModel::factory()->create([
                    'name' => '3 Series',
                    'vehicle_make_id' => $vehicleMake->id,
                ]);
                Vehicle::factory()->create([
                    'name' => 'Benz saloon',
                    'registration' => '123456',
                    'vehicle_model_id' => $model->id,
                    'vehicle_make_id' => $vehicleMake->id,
                ]);
            })
            ->create(['name' => 'BMW']);
    }
}
