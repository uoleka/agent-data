<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\FileService;
use App\Http\Requests\UploadFileRequest;

class FileController extends Controller
{
    public function uploadFile(UploadFileRequest $request, FileService $fileService)
    {   
        $validated = $request->validated('file');
        $filePath = $validated->getPathname();
      
        try {
            
            // Create the order using the OrderService
            $filePersons = $fileService->storeValues($filePath); 
            // Return a success response with the created order
            return response()->json([
                'message' => 'Persons record created successfully'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => "The records couldn't be created"
            ], 500);
        }
    }
}
