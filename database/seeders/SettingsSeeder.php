<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Entities\Settings;
class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $user=Settings::create([
            'portal_name' => env('APP_NAME', 'Storffy'),
            'portal_email' => env('APP_EMAIL', 'support@dummy.com'),
            'portal_logo' => 'logo.png',
            'portal_favicon' => 'favicon.png',
            'layout' => 1,
            'sidebar_color' => 1,
            'color_theme' => 'white',
            'mini_sidebar' => 'unchecked',
            'sticky_header' => 'checked',
        ]);
    }
}
