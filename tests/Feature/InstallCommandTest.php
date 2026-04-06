<?php

namespace LaraOrVite\Framework\Tests\Feature;

use Illuminate\Support\Facades\File;
use LaraOrVite\Framework\Tests\TestCase;

class InstallCommandTest extends TestCase
{
    protected array $frameworks = [
        'lit', 'lit-ts',
        'preact', 'preact-ts',
        'react', 'react-ts',
        'svelte', 'svelte-ts',
        'vanilla', 'vanilla-ts',
        'vue', 'vue-ts'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        if (!File::exists(resource_path())) {
            File::makeDirectory(resource_path(), 0755, true);
        }

        if (!File::exists(base_path('routes'))) {
            File::makeDirectory(base_path('routes'), 0755, true);
        }

        if (File::exists(resource_path('frontend'))) {
            File::deleteDirectory(resource_path('frontend'));
        }
        if (File::exists(resource_path('my-app'))) {
            File::deleteDirectory(resource_path('my-app'));
        }
    }

    /** @test */
    public function test_it_installs_the_api_routes_file()
    {
        File::put(base_path('routes/api.php'), '<?php');

        $this->artisan('frontend:setup')
            ->expectsChoice('Which frontend framework do you want to use?', 'react', $this->frameworks)

            ->expectsChoice('Select additional packages to install (comma-separated numbers)', 'None', [
                'None', 'Tailwind CSS', 'Axios', 'Lucide Icons', 'TanStack Query',
                'Redux Toolkit (with React-Redux)', 'Zustand', 'React Router'
            ])
            ->assertExitCode(0);

        $this->assertTrue(File::exists(base_path('routes/api.php')));
    }

    /** @test */
    public function test_it_creates_the_frontend_directory_with_custom_name()
    {
        $folderName = 'my-app';
        $path = resource_path($folderName);

        $this->artisan("frontend:setup {$folderName}")
            ->expectsChoice('Which frontend framework do you want to use?', 'react', $this->frameworks)

            ->expectsChoice('Select additional packages to install (comma-separated numbers)', 'None', [
                'None', 'Tailwind CSS', 'Axios', 'Lucide Icons', 'TanStack Query',
                'Redux Toolkit (with React-Redux)', 'Zustand', 'React Router'
            ])
            ->assertExitCode(0);

        $this->assertTrue(File::exists($path));
    }
}
