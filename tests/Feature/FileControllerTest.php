<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use App\Services;

class FileControllerTest extends TestCase
{
    /**
     * Test file upload.
     *
     * @return void
     */
    public function testFileUpload()
    {
        Storage::fake('public'); // Use a fake disk for testing

        $file = FileService::fake()->create('testfile.csv');

        $response = $this->post('/upload', ['file' => $file]);

        $response->assertStatus(200); // Assuming your upload route returns a 200 status upon success

        // Optionally, assert that the file was stored in the expected location
        Storage::disk('public')->assertExists($file->hashName());
    }
}
