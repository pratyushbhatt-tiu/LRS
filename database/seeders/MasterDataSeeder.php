<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Client;
use App\Models\County;
use App\Models\DocType;
use App\Models\RecordingPurpose;
use App\Models\State;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clients
        Client::create(['code' => 'CLI001', 'name' => 'ABC Title Company', 'active' => true]);
        Client::create(['code' => 'CLI002', 'name' => 'XYZ Legal Services', 'active' => true]);
        Client::create(['code' => 'CLI003', 'name' => 'First National Bank', 'active' => true]);

        // Document Types
        DocType::create(['code' => 'DEED', 'name' => 'Deed', 'active' => true]);
        DocType::create(['code' => 'MORT', 'name' => 'Mortgage', 'active' => true]);
        DocType::create(['code' => 'LIEN', 'name' => 'Lien', 'active' => true]);
        DocType::create(['code' => 'RELS', 'name' => 'Release', 'active' => true]);
        DocType::create(['code' => 'ASGN', 'name' => 'Assignment', 'active' => true]);

        // Recording Purposes
        RecordingPurpose::create(['code' => 'SALE', 'name' => 'Sale Transaction', 'active' => true]);
        RecordingPurpose::create(['code' => 'REFI', 'name' => 'Refinance', 'active' => true]);
        RecordingPurpose::create(['code' => 'LNRL', 'name' => 'Lien Release', 'active' => true]);
        RecordingPurpose::create(['code' => 'TRNS', 'name' => 'Transfer', 'active' => true]);

        // States
        $california = State::create(['code' => 'CA', 'name' => 'California', 'active' => true]);
        $texas = State::create(['code' => 'TX', 'name' => 'Texas', 'active' => true]);
        $florida = State::create(['code' => 'FL', 'name' => 'Florida', 'active' => true]);

        // Counties for California
        $losAngeles = County::create(['state_id' => $california->id, 'code' => 'LA', 'name' => 'Los Angeles', 'active' => true]);
        $sanDiego = County::create(['state_id' => $california->id, 'code' => 'SD', 'name' => 'San Diego', 'active' => true]);
        $orange = County::create(['state_id' => $california->id, 'code' => 'OR', 'name' => 'Orange', 'active' => true]);

        // Counties for Texas
        $harris = County::create(['state_id' => $texas->id, 'code' => 'HAR', 'name' => 'Harris', 'active' => true]);
        $dallas = County::create(['state_id' => $texas->id, 'code' => 'DAL', 'name' => 'Dallas', 'active' => true]);

        // Counties for Florida
        $miami = County::create(['state_id' => $florida->id, 'code' => 'MIA', 'name' => 'Miami-Dade', 'active' => true]);

        // Cities for Los Angeles County
        City::create(['state_id' => $california->id, 'county_id' => $losAngeles->id, 'code' => 'LA', 'name' => 'Los Angeles', 'active' => true]);
        City::create(['state_id' => $california->id, 'county_id' => $losAngeles->id, 'code' => 'PASAD', 'name' => 'Pasadena', 'active' => true]);
        City::create(['state_id' => $california->id, 'county_id' => $losAngeles->id, 'code' => 'GLEND', 'name' => 'Glendale', 'active' => true]);

        // Cities for San Diego County
        City::create(['state_id' => $california->id, 'county_id' => $sanDiego->id, 'code' => 'SD', 'name' => 'San Diego', 'active' => true]);
        City::create(['state_id' => $california->id, 'county_id' => $sanDiego->id, 'code' => 'CHULA', 'name' => 'Chula Vista', 'active' => true]);

        // Cities for Orange County
        City::create(['state_id' => $california->id, 'county_id' => $orange->id, 'code' => 'ANAH', 'name' => 'Anaheim', 'active' => true]);
        City::create(['state_id' => $california->id, 'county_id' => $orange->id, 'code' => 'IRV', 'name' => 'Irvine', 'active' => true]);

        // Cities for Harris County
        City::create(['state_id' => $texas->id, 'county_id' => $harris->id, 'code' => 'HOU', 'name' => 'Houston', 'active' => true]);

        // Cities for Dallas County
        City::create(['state_id' => $texas->id, 'county_id' => $dallas->id, 'code' => 'DAL', 'name' => 'Dallas', 'active' => true]);

        // Cities for Miami-Dade County
        City::create(['state_id' => $florida->id, 'county_id' => $miami->id, 'code' => 'MIA', 'name' => 'Miami', 'active' => true]);
    }
}
