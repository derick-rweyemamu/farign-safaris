<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'Afghanistan', 'code' => 'AFG', 'phone_code' => '+93', 'flag' => 'af.png'],
            ['name' => 'Albania', 'code' => 'ALB', 'phone_code' => '+355', 'flag' => 'al.png'],
            ['name' => 'Algeria', 'code' => 'DZA', 'phone_code' => '+213', 'flag' => 'dz.png'],
            ['name' => 'Andorra', 'code' => 'AND', 'phone_code' => '+376', 'flag' => 'ad.png'],
            ['name' => 'Angola', 'code' => 'AGO', 'phone_code' => '+244', 'flag' => 'ao.png'],
            ['name' => 'Argentina', 'code' => 'ARG', 'phone_code' => '+54', 'flag' => 'ar.png'],
            ['name' => 'Armenia', 'code' => 'ARM', 'phone_code' => '+374', 'flag' => 'am.png'],
            ['name' => 'Australia', 'code' => 'AUS', 'phone_code' => '+61', 'flag' => 'au.png'],
            ['name' => 'Austria', 'code' => 'AUT', 'phone_code' => '+43', 'flag' => 'at.png'],
            ['name' => 'Azerbaijan', 'code' => 'AZE', 'phone_code' => '+994', 'flag' => 'az.png'],
            ['name' => 'Bahamas', 'code' => 'BHS', 'phone_code' => '+1-242', 'flag' => 'bs.png'],
            ['name' => 'Bahrain', 'code' => 'BHR', 'phone_code' => '+973', 'flag' => 'bh.png'],
            ['name' => 'Bangladesh', 'code' => 'BGD', 'phone_code' => '+880', 'flag' => 'bd.png'],
            ['name' => 'Belgium', 'code' => 'BEL', 'phone_code' => '+32', 'flag' => 'be.png'],
            ['name' => 'Benin', 'code' => 'BEN', 'phone_code' => '+229', 'flag' => 'bj.png'],
            ['name' => 'Bhutan', 'code' => 'BTN', 'phone_code' => '+975', 'flag' => 'bt.png'],
            ['name' => 'Bolivia', 'code' => 'BOL', 'phone_code' => '+591', 'flag' => 'bo.png'],
            ['name' => 'Botswana', 'code' => 'BWA', 'phone_code' => '+267', 'flag' => 'bw.png'],
            ['name' => 'Brazil', 'code' => 'BRA', 'phone_code' => '+55', 'flag' => 'br.png'],
            ['name' => 'Bulgaria', 'code' => 'BGR', 'phone_code' => '+359', 'flag' => 'bg.png'],
            ['name' => 'Burkina Faso', 'code' => 'BFA', 'phone_code' => '+226', 'flag' => 'bf.png'],
            ['name' => 'Burundi', 'code' => 'BDI', 'phone_code' => '+257', 'flag' => 'bi.png'],
            ['name' => 'Cambodia', 'code' => 'KHM', 'phone_code' => '+855', 'flag' => 'kh.png'],
            ['name' => 'Cameroon', 'code' => 'CMR', 'phone_code' => '+237', 'flag' => 'cm.png'],
            ['name' => 'Canada', 'code' => 'CAN', 'phone_code' => '+1', 'flag' => 'ca.png'],
            ['name' => 'Chile', 'code' => 'CHL', 'phone_code' => '+56', 'flag' => 'cl.png'],
            ['name' => 'China', 'code' => 'CHN', 'phone_code' => '+86', 'flag' => 'cn.png'],
            ['name' => 'Colombia', 'code' => 'COL', 'phone_code' => '+57', 'flag' => 'co.png'],
            ['name' => 'Comoros', 'code' => 'COM', 'phone_code' => '+269', 'flag' => 'km.png'],
            ['name' => 'Congo (DRC)', 'code' => 'COD', 'phone_code' => '+243', 'flag' => 'cd.png'],
            ['name' => 'Congo (Republic)', 'code' => 'COG', 'phone_code' => '+242', 'flag' => 'cg.png'],
            ['name' => 'Costa Rica', 'code' => 'CRI', 'phone_code' => '+506', 'flag' => 'cr.png'],
            ['name' => 'Croatia', 'code' => 'HRV', 'phone_code' => '+385', 'flag' => 'hr.png'],
            ['name' => 'Cuba', 'code' => 'CUB', 'phone_code' => '+53', 'flag' => 'cu.png'],
            ['name' => 'Cyprus', 'code' => 'CYP', 'phone_code' => '+357', 'flag' => 'cy.png'],
            ['name' => 'Czech Republic', 'code' => 'CZE', 'phone_code' => '+420', 'flag' => 'cz.png'],
            ['name' => 'Denmark', 'code' => 'DNK', 'phone_code' => '+45', 'flag' => 'dk.png'],
            ['name' => 'Djibouti', 'code' => 'DJI', 'phone_code' => '+253', 'flag' => 'dj.png'],
            ['name' => 'Dominican Republic', 'code' => 'DOM', 'phone_code' => '+1-809', 'flag' => 'do.png'],
            ['name' => 'Ecuador', 'code' => 'ECU', 'phone_code' => '+593', 'flag' => 'ec.png'],
            ['name' => 'Egypt', 'code' => 'EGY', 'phone_code' => '+20', 'flag' => 'eg.png'],
            ['name' => 'Ethiopia', 'code' => 'ETH', 'phone_code' => '+251', 'flag' => 'et.png'],
            ['name' => 'Finland', 'code' => 'FIN', 'phone_code' => '+358', 'flag' => 'fi.png'],
            ['name' => 'France', 'code' => 'FRA', 'phone_code' => '+33', 'flag' => 'fr.png'],
            ['name' => 'Germany', 'code' => 'DEU', 'phone_code' => '+49', 'flag' => 'de.png'],
            ['name' => 'Ghana', 'code' => 'GHA', 'phone_code' => '+233', 'flag' => 'gh.png'],
            ['name' => 'Greece', 'code' => 'GRC', 'phone_code' => '+30', 'flag' => 'gr.png'],
            ['name' => 'India', 'code' => 'IND', 'phone_code' => '+91', 'flag' => 'in.png'],
            ['name' => 'Indonesia', 'code' => 'IDN', 'phone_code' => '+62', 'flag' => 'id.png'],
            ['name' => 'Ireland', 'code' => 'IRL', 'phone_code' => '+353', 'flag' => 'ie.png'],
            ['name' => 'Israel', 'code' => 'ISR', 'phone_code' => '+972', 'flag' => 'il.png'],
            ['name' => 'Italy', 'code' => 'ITA', 'phone_code' => '+39', 'flag' => 'it.png'],
            ['name' => 'Japan', 'code' => 'JPN', 'phone_code' => '+81', 'flag' => 'jp.png'],
            ['name' => 'Jordan', 'code' => 'JOR', 'phone_code' => '+962', 'flag' => 'jo.png'],
            ['name' => 'Kenya', 'code' => 'KEN', 'phone_code' => '+254', 'flag' => 'ke.png'],
            ['name' => 'Kuwait', 'code' => 'KWT', 'phone_code' => '+965', 'flag' => 'kw.png'],
            ['name' => 'Lesotho', 'code' => 'LSO', 'phone_code' => '+266', 'flag' => 'ls.png'],
            ['name' => 'Liberia', 'code' => 'LBR', 'phone_code' => '+231', 'flag' => 'lr.png'],
            ['name' => 'Madagascar', 'code' => 'MDG', 'phone_code' => '+261', 'flag' => 'mg.png'],
            ['name' => 'Malawi', 'code' => 'MWI', 'phone_code' => '+265', 'flag' => 'mw.png'],
            ['name' => 'Malaysia', 'code' => 'MYS', 'phone_code' => '+60', 'flag' => 'my.png'],
            ['name' => 'Mali', 'code' => 'MLI', 'phone_code' => '+223', 'flag' => 'ml.png'],
            ['name' => 'Mexico', 'code' => 'MEX', 'phone_code' => '+52', 'flag' => 'mx.png'],
            ['name' => 'Morocco', 'code' => 'MAR', 'phone_code' => '+212', 'flag' => 'ma.png'],
            ['name' => 'Mozambique', 'code' => 'MOZ', 'phone_code' => '+258', 'flag' => 'mz.png'],
            ['name' => 'Namibia', 'code' => 'NAM', 'phone_code' => '+264', 'flag' => 'na.png'],
            ['name' => 'Nepal', 'code' => 'NPL', 'phone_code' => '+977', 'flag' => 'np.png'],
            ['name' => 'Netherlands', 'code' => 'NLD', 'phone_code' => '+31', 'flag' => 'nl.png'],
            ['name' => 'New Zealand', 'code' => 'NZL', 'phone_code' => '+64', 'flag' => 'nz.png'],
            ['name' => 'Nigeria', 'code' => 'NGA', 'phone_code' => '+234', 'flag' => 'ng.png'],
            ['name' => 'Norway', 'code' => 'NOR', 'phone_code' => '+47', 'flag' => 'no.png'],
            ['name' => 'Pakistan', 'code' => 'PAK', 'phone_code' => '+92', 'flag' => 'pk.png'],
            ['name' => 'Philippines', 'code' => 'PHL', 'phone_code' => '+63', 'flag' => 'ph.png'],
            ['name' => 'Poland', 'code' => 'POL', 'phone_code' => '+48', 'flag' => 'pl.png'],
            ['name' => 'Portugal', 'code' => 'PRT', 'phone_code' => '+351', 'flag' => 'pt.png'],
            ['name' => 'Qatar', 'code' => 'QAT', 'phone_code' => '+974', 'flag' => 'qa.png'],
            ['name' => 'Rwanda', 'code' => 'RWA', 'phone_code' => '+250', 'flag' => 'rw.png'],
            ['name' => 'Saudi Arabia', 'code' => 'SAU', 'phone_code' => '+966', 'flag' => 'sa.png'],
            ['name' => 'Senegal', 'code' => 'SEN', 'phone_code' => '+221', 'flag' => 'sn.png'],
            ['name' => 'Seychelles', 'code' => 'SYC', 'phone_code' => '+248', 'flag' => 'sc.png'],
            ['name' => 'Sierra Leone', 'code' => 'SLE', 'phone_code' => '+232', 'flag' => 'sl.png'],
            ['name' => 'Singapore', 'code' => 'SGP', 'phone_code' => '+65', 'flag' => 'sg.png'],
            ['name' => 'Somalia', 'code' => 'SOM', 'phone_code' => '+252', 'flag' => 'so.png'],
            ['name' => 'South Africa', 'code' => 'ZAF', 'phone_code' => '+27', 'flag' => 'za.png'],
            ['name' => 'South Korea', 'code' => 'KOR', 'phone_code' => '+82', 'flag' => 'kr.png'],
            ['name' => 'Spain', 'code' => 'ESP', 'phone_code' => '+34', 'flag' => 'es.png'],
            ['name' => 'Sudan', 'code' => 'SDN', 'phone_code' => '+249', 'flag' => 'sd.png'],
            ['name' => 'Sweden', 'code' => 'SWE', 'phone_code' => '+46', 'flag' => 'se.png'],
            ['name' => 'Switzerland', 'code' => 'CHE', 'phone_code' => '+41', 'flag' => 'ch.png'],
            ['name' => 'Tanzania', 'code' => 'TZA', 'phone_code' => '+255', 'flag' => 'tz.png'],
            ['name' => 'Thailand', 'code' => 'THA', 'phone_code' => '+66', 'flag' => 'th.png'],
            ['name' => 'Togo', 'code' => 'TGO', 'phone_code' => '+228', 'flag' => 'tg.png'],
            ['name' => 'Tunisia', 'code' => 'TUN', 'phone_code' => '+216', 'flag' => 'tn.png'],
            ['name' => 'Turkey', 'code' => 'TUR', 'phone_code' => '+90', 'flag' => 'tr.png'],
            ['name' => 'Uganda', 'code' => 'UGA', 'phone_code' => '+256', 'flag' => 'ug.png'],
            ['name' => 'Ukraine', 'code' => 'UKR', 'phone_code' => '+380', 'flag' => 'ua.png'],
            ['name' => 'United Arab Emirates', 'code' => 'ARE', 'phone_code' => '+971', 'flag' => 'ae.png'],
            ['name' => 'United Kingdom', 'code' => 'GBR', 'phone_code' => '+44', 'flag' => 'gb.png'],
            ['name' => 'United States', 'code' => 'USA', 'phone_code' => '+1', 'flag' => 'us.png'],
            ['name' => 'Venezuela', 'code' => 'VEN', 'phone_code' => '+58', 'flag' => 've.png'],
            ['name' => 'Vietnam', 'code' => 'VNM', 'phone_code' => '+84', 'flag' => 'vn.png'],
            ['name' => 'Zambia', 'code' => 'ZMB', 'phone_code' => '+260', 'flag' => 'zm.png'],
            ['name' => 'Zimbabwe', 'code' => 'ZWE', 'phone_code' => '+263', 'flag' => 'zw.png'],
        ];


        foreach ($countries as $c) {
            Country::firstOrCreate(
                ['name' => $c['name']],
                [
                    'code' => $c['code'],
                    'phone_code' => $c['phone_code'] ?? null,
                    'flag' => $c['flag'] ?? null,
                ]
            );
        }
    }
}
