<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\FileService;
use App\Http\Requests\UploadFileRequest;

class FileController extends Controller
{
    public function uploadFile(UploadFileRequest $request, FileService $fileService)
    {   
        $validated = $request->validated('file');
        $filePath = $validated->getPathname();
      
        try {
            // Create the upload and process the file using the FileService
            $filePersons = $fileService->storeValues($filePath); 
            // Return a success response
            return redirect()->route('upload.file')->with('message', 'Data processed and submitted successfully!');
        } catch (\Exception $e) {
            // Log the exception 
            Log::error($e->getMessage());
            // Redirect back with an error message
            return back()->with('error', 'An error occurred. Please try again.');
        }
    }
}
