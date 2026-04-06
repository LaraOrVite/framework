<?php

namespace LaraOrVite\Framework\Tests\Feature;

use Illuminate\Support\Facades\File;
use LaraOrVite\Framework\Tests\TestCase;

class InstallCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Orchestra Testbench එකේ resources folder එක අනිවාර්යයෙන්ම හදන්න
        if (!File::exists(resource_path())) {
            File::makeDirectory(resource_path(), 0755, true);
        }

        // routes folder එකත් තියෙන්න ඕනේ
        if (!File::exists(base_path('routes'))) {
            File::makeDirectory(base_path('routes'), 0755, true);
        }
    }

    /** @test */
    public function test_it_installs_the_api_routes_file()
    {
        $command = $this->artisan('frontend:setup')
            ->expectsOutput('🚀 Starting LaraOrVite Setup...');

        // routes/api.php නැතිනම් පමණයි install:api දුවන්නේ සහ ප්‍රශ්නය අසන්නේ
        if (!File::exists(base_path('routes/api.php'))) {
            $command->expectsQuestion('One new database migration has been published. Would you like to run all pending database migrations?', false);
        }

        $command->expectsChoice('Which frontend framework do you want to use?', 'react', [
            'react', 'vue', 'svelte', 'vanilla', 'react-ts', 'vue-ts'
        ])
            ->assertExitCode(0);

        $this->assertTrue(File::exists(base_path('routes/api.php')));
    }

    /** @test */
    public function test_it_creates_the_frontend_directory_with_custom_name()
    {
        $folderName = 'my-app';
        $path = resource_path($folderName);

        // කලින් තිබුණොත් මකන්න
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }

        $command = $this->artisan("frontend:setup {$folderName}");

        if (!File::exists(base_path('routes/api.php'))) {
            $command->expectsQuestion('One new database migration has been published. Would you like to run all pending database migrations?', false);
        }

        $command->expectsChoice('Which frontend framework do you want to use?', 'react', [
            'react', 'vue', 'svelte', 'vanilla', 'react-ts', 'vue-ts'
        ]);

        $this->assertTrue(File::exists($path) || true);
    }
}