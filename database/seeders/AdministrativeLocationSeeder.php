<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdministrativeLocationSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $upsertByCode = function (string $table, string $code, array $values) use ($now): int {
            $existing = DB::table($table)->where('code', $code)->first();

            if ($existing) {
                DB::table($table)->where('id', $existing->id)->update(array_merge($values, ['updated_at' => $now]));
                return (int) $existing->id;
            }

            return (int) DB::table($table)->insertGetId(array_merge($values, [
                'code' => $code,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        };

        $countryId = $upsertByCode('countries', 'ET', [
            'name' => 'Ethiopia',
        ]);

        $regionId = $upsertByCode('regions', 'ADDIS_ABABA', [
            'name' => 'Addis Ababa',
            'country_id' => $countryId,
        ]);

        $zoneId = $upsertByCode('zones', 'AA_CENTRAL', [
            'name' => 'Central Zone',
            'region_id' => $regionId,
        ]);

        $woreda01Id = $upsertByCode('woredas', 'AA_W01', [
            'name' => 'Woreda 01',
            'zone_id' => $zoneId,
        ]);

        $woreda02Id = $upsertByCode('woredas', 'AA_W02', [
            'name' => 'Woreda 02',
            'zone_id' => $zoneId,
        ]);

        $kebele011Id = $upsertByCode('kebeles', 'AA_W01_K01', [
            'name' => 'Kebele 01',
            'woreda_id' => $woreda01Id,
        ]);

        $kebele012Id = $upsertByCode('kebeles', 'AA_W01_K02', [
            'name' => 'Kebele 02',
            'woreda_id' => $woreda01Id,
        ]);

        $kebele021Id = $upsertByCode('kebeles', 'AA_W02_K01', [
            'name' => 'Kebele 01',
            'woreda_id' => $woreda02Id,
        ]);

        $upsertByCode('localities', 'AA_W01_K01_L01', [
            'name' => 'Piassa',
            'kebele_id' => $kebele011Id,
        ]);

        $upsertByCode('localities', 'AA_W01_K02_L01', [
            'name' => 'Arat Kilo',
            'kebele_id' => $kebele012Id,
        ]);

        $upsertByCode('localities', 'AA_W02_K01_L01', [
            'name' => 'Mexico',
            'kebele_id' => $kebele021Id,
        ]);

        $upsertByCode('tax_centers', 'TX-AA-W01', [
            'name' => 'Addis Tax Center Woreda 01',
            'administrative_level' => 'woreda',
            'woreda_id' => $woreda01Id,
        ]);

        $upsertByCode('tax_centers', 'TX-AA-W02', [
            'name' => 'Addis Tax Center Woreda 02',
            'administrative_level' => 'woreda',
            'woreda_id' => $woreda02Id,
        ]);
    }
}

