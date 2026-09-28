<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    use ApiResponse;

    /**
     * Get a list of West Bengal Board secondary/higher secondary schools.
     * Supports search (?q=) and district filter (?district=).
     *
     * GET /api/schools
     */
    public function index(Request $request): JsonResponse
    {
        $schools = self::getWestBengalSchools();

        $search = trim((string) $request->input('q', ''));
        $district = trim((string) $request->input('district', ''));

        if ($district !== '') {
            $schools = array_values(array_filter($schools, fn ($s) => strcasecmp($s['district'], $district) === 0));
        }

        if ($search !== '') {
            $schools = array_values(array_filter($schools, function ($s) use ($search) {
                return stripos($s['name'], $search) !== false ||
                       stripos($s['district'], $search) !== false ||
                       stripos($s['code'], $search) !== false;
            }));
        }

        return $this->successResponse([
            'total' => count($schools),
            'districts' => self::getDistricts(),
            'schools' => array_slice($schools, 0, 100), // Return top 100 matches
        ]);
    }

    /**
     * Get list of districts in West Bengal.
     * GET /api/schools/districts
     */
    public function districts(): JsonResponse
    {
        return $this->successResponse(self::getDistricts());
    }

    public static function getDistricts(): array
    {
        return [
            'Alipurduar',
            'Bankura',
            'Birbhum',
            'Cooch Behar',
            'Dakshin Dinajpur',
            'Darjeeling',
            'Hooghly',
            'Howrah',
            'Jalpaiguri',
            'Jhargram',
            'Kalimpong',
            'Kolkata',
            'Malda',
            'Murshidabad',
            'Nadia',
            'North 24 Parganas',
            'Paschim Bardhaman',
            'Paschim Medinipur',
            'Purba Bardhaman',
            'Purba Medinipur',
            'Purulia',
            'South 24 Parganas',
            'Uttar Dinajpur',
        ];
    }

    public static function getWestBengalSchools(): array
    {
        return [
            // Kolkata
            ['code' => 'WB-KOL-001', 'name' => 'Hare School', 'district' => 'Kolkata'],
            ['code' => 'WB-KOL-002', 'name' => 'Hindu School', 'district' => 'Kolkata'],
            ['code' => 'WB-KOL-003', 'name' => 'Ballygunge Government High School', 'district' => 'Kolkata'],
            ['code' => 'WB-KOL-004', 'name' => 'Scottish Church Collegiate School', 'district' => 'Kolkata'],
            ['code' => 'WB-KOL-005', 'name' => 'Mitra Institution (Main)', 'district' => 'Kolkata'],
            ['code' => 'WB-KOL-006', 'name' => 'Bethune Collegiate School', 'district' => 'Kolkata'],
            ['code' => 'WB-KOL-007', 'name' => 'South Point High School', 'district' => 'Kolkata'],
            ['code' => 'WB-KOL-008', 'name' => 'Jadavpur Vidyapith', 'district' => 'Kolkata'],
            ['code' => 'WB-KOL-009', 'name' => 'Patha Bhavan', 'district' => 'Kolkata'],
            ['code' => 'WB-KOL-010', 'name' => 'Calcutta Boys\' School', 'district' => 'Kolkata'],
            ['code' => 'WB-KOL-011', 'name' => 'Sailendra Sircar Vidyalaya', 'district' => 'Kolkata'],
            ['code' => 'WB-KOL-012', 'name' => 'Chetla Boys\' High School', 'district' => 'Kolkata'],
            ['code' => 'WB-KOL-013', 'name' => 'Sanskrit Collegiate School', 'district' => 'Kolkata'],

            // North 24 Parganas
            ['code' => 'WB-N24-001', 'name' => 'Barrackpore Government High School', 'district' => 'North 24 Parganas'],
            ['code' => 'WB-N24-002', 'name' => 'Barasat Mahatma Gandhi Memorial High School', 'district' => 'North 24 Parganas'],
            ['code' => 'WB-N24-003', 'name' => 'Rahara Ramakrishna Mission Boys\' Home High School', 'district' => 'North 24 Parganas'],
            ['code' => 'WB-N24-004', 'name' => 'Naihati Narendra Vidyaniketan', 'district' => 'North 24 Parganas'],
            ['code' => 'WB-N24-005', 'name' => 'Bhatpara Amrita Vidyalaya', 'district' => 'North 24 Parganas'],
            ['code' => 'WB-N24-006', 'name' => 'Dum Dum Motijheel Government Sponsored High School', 'district' => 'North 24 Parganas'],
            ['code' => 'WB-N24-007', 'name' => 'Basirhat High School', 'district' => 'North 24 Parganas'],
            ['code' => 'WB-N24-008', 'name' => 'Bongaon High School', 'district' => 'North 24 Parganas'],
            ['code' => 'WB-N24-009', 'name' => 'Kalyannagar Vidyapith', 'district' => 'North 24 Parganas'],
            ['code' => 'WB-N24-010', 'name' => 'Habra Model High School', 'district' => 'North 24 Parganas'],

            // South 24 Parganas
            ['code' => 'WB-S24-001', 'name' => 'Narendrapur Ramakrishna Mission Vidyalaya', 'district' => 'South 24 Parganas'],
            ['code' => 'WB-S24-002', 'name' => 'Diamond Harbour High School', 'district' => 'South 24 Parganas'],
            ['code' => 'WB-S24-003', 'name' => 'Baruipur High School', 'district' => 'South 24 Parganas'],
            ['code' => 'WB-S24-004', 'name' => 'Canning David Sassoon High School', 'district' => 'South 24 Parganas'],
            ['code' => 'WB-S24-005', 'name' => 'Kakdwip Birendra Vidyaniketan', 'district' => 'South 24 Parganas'],
            ['code' => 'WB-S24-006', 'name' => 'Sonarpur Vidyapith', 'district' => 'South 24 Parganas'],

            // Howrah
            ['code' => 'WB-HWH-001', 'name' => 'Howrah Zilla School', 'district' => 'Howrah'],
            ['code' => 'WB-HWH-002', 'name' => 'Bally Santiram Vidyalaya', 'district' => 'Howrah'],
            ['code' => 'WB-HWH-003', 'name' => 'Uluberia High School', 'district' => 'Howrah'],
            ['code' => 'WB-HWH-004', 'name' => 'Shibpur Dinobundhoo Institution', 'district' => 'Howrah'],
            ['code' => 'WB-HWH-005', 'name' => 'Andul H.C. High School', 'district' => 'Howrah'],

            // Hooghly
            ['code' => 'WB-HGY-001', 'name' => 'Hooghly Collegiate School', 'district' => 'Hooghly'],
            ['code' => 'WB-HGY-002', 'name' => 'Chinsurah Deshbandhu Memorial High School', 'district' => 'Hooghly'],
            ['code' => 'WB-HGY-003', 'name' => 'Uttarpara Government High School', 'district' => 'Hooghly'],
            ['code' => 'WB-HGY-004', 'name' => 'Serampore Union Institution', 'district' => 'Hooghly'],
            ['code' => 'WB-HGY-005', 'name' => 'Chandannagar Kanailal Vidyamandir', 'district' => 'Hooghly'],
            ['code' => 'WB-HGY-006', 'name' => 'Arambagh High School', 'district' => 'Hooghly'],

            // Nadia
            ['code' => 'WB-NAD-001', 'name' => 'Krishnagar Collegiate School', 'district' => 'Nadia'],
            ['code' => 'WB-NAD-002', 'name' => 'Ranaghat Palchoudhury High School', 'district' => 'Nadia'],
            ['code' => 'WB-NAD-003', 'name' => 'Kalyani Shikyaniketan', 'district' => 'Nadia'],
            ['code' => 'WB-NAD-004', 'name' => 'Nabadwip Bakultala High School', 'district' => 'Nadia'],
            ['code' => 'WB-NAD-005', 'name' => 'Santipur Municipal High School', 'district' => 'Nadia'],

            // Murshidabad
            ['code' => 'WB-MSD-001', 'name' => 'Berhampore Krishnath Collegiate School', 'district' => 'Murshidabad'],
            ['code' => 'WB-MSD-002', 'name' => 'Kandi Raj High School', 'district' => 'Murshidabad'],
            ['code' => 'WB-MSD-003', 'name' => 'Jangipur High School', 'district' => 'Murshidabad'],
            ['code' => 'WB-MSD-004', 'name' => 'Lalbagh Singhi High School', 'district' => 'Murshidabad'],

            // Purba Bardhaman & Paschim Bardhaman
            ['code' => 'WB-PBD-001', 'name' => 'Burdwan Municipal High School', 'district' => 'Purba Bardhaman'],
            ['code' => 'WB-PBD-002', 'name' => 'Burdwan Town School', 'district' => 'Purba Bardhaman'],
            ['code' => 'WB-PBD-003', 'name' => 'Kalna Maharaja\'s High School', 'district' => 'Purba Bardhaman'],
            ['code' => 'WB-PBD-004', 'name' => 'Katwa K.D. Institution', 'district' => 'Purba Bardhaman'],
            ['code' => 'WB-WBD-001', 'name' => 'Asansol Ramakrishna Mission High School', 'district' => 'Paschim Bardhaman'],
            ['code' => 'WB-WBD-002', 'name' => 'Durgapur Projects Boys\' High School', 'district' => 'Paschim Bardhaman'],
            ['code' => 'WB-WBD-003', 'name' => 'Raniganj High School', 'district' => 'Paschim Bardhaman'],

            // Purba & Paschim Medinipur, Jhargram
            ['code' => 'WB-PMD-001', 'name' => 'Tamluk Hamilton High School', 'district' => 'Purba Medinipur'],
            ['code' => 'WB-PMD-002', 'name' => 'Contai High School', 'district' => 'Purba Medinipur'],
            ['code' => 'WB-PMD-003', 'name' => 'Panskura Bradley Birt High School', 'district' => 'Purba Medinipur'],
            ['code' => 'WB-WMD-001', 'name' => 'Midnapore Collegiate School', 'district' => 'Paschim Medinipur'],
            ['code' => 'WB-WMD-002', 'name' => 'Midnapore Town School', 'district' => 'Paschim Medinipur'],
            ['code' => 'WB-WMD-003', 'name' => 'Kharagpur Silver Jubilee High School', 'district' => 'Paschim Medinipur'],
            ['code' => 'WB-JHG-001', 'name' => 'Jhargram Kumud Kumari Institution', 'district' => 'Jhargram'],

            // Bankura, Birbhum, Purulia
            ['code' => 'WB-BNK-001', 'name' => 'Bankura Zilla School', 'district' => 'Bankura'],
            ['code' => 'WB-BNK-002', 'name' => 'Bishnupur High School', 'district' => 'Bankura'],
            ['code' => 'WB-BIR-001', 'name' => 'Suri Beni Madhab Institution', 'district' => 'Birbhum'],
            ['code' => 'WB-BIR-002', 'name' => 'Bolpur High School', 'district' => 'Birbhum'],
            ['code' => 'WB-BIR-003', 'name' => 'Rampurhat High School', 'district' => 'Birbhum'],
            ['code' => 'WB-PRL-001', 'name' => 'Purulia Zilla School', 'district' => 'Purulia'],
            ['code' => 'WB-PRL-002', 'name' => 'Raghunathpur High School', 'district' => 'Purulia'],

            // Malda, Dinajpur
            ['code' => 'WB-MLD-001', 'name' => 'Malda Zilla School', 'district' => 'Malda'],
            ['code' => 'WB-MLD-002', 'name' => 'Malda Barlow Girls\' High School', 'district' => 'Malda'],
            ['code' => 'WB-UDD-001', 'name' => 'Raiganj Coronation High School', 'district' => 'Uttar Dinajpur'],
            ['code' => 'WB-UDD-002', 'name' => 'Islampur High School', 'district' => 'Uttar Dinajpur'],
            ['code' => 'WB-DKD-001', 'name' => 'Balurghat High School', 'district' => 'Dakshin Dinajpur'],

            // Jalpaiguri, Alipurduar, Cooch Behar, Darjeeling, Kalimpong
            ['code' => 'WB-JPG-001', 'name' => 'Jalpaiguri Zilla School', 'district' => 'Jalpaiguri'],
            ['code' => 'WB-JPG-002', 'name' => 'Siliguri Boys\' High School', 'district' => 'Darjeeling'],
            ['code' => 'WB-DAR-001', 'name' => 'Darjeeling Government High School', 'district' => 'Darjeeling'],
            ['code' => 'WB-KLP-001', 'name' => 'Kalimpong Kumudini Homes', 'district' => 'Kalimpong'],
            ['code' => 'WB-COB-001', 'name' => 'Jenkins School, Cooch Behar', 'district' => 'Cooch Behar'],
            ['code' => 'WB-COB-002', 'name' => 'Dinhata High School', 'district' => 'Cooch Behar'],
            ['code' => 'WB-APD-001', 'name' => 'Alipurduar High School', 'district' => 'Alipurduar'],
        ];
    }
}
