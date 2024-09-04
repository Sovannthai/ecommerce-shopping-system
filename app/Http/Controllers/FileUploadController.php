<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\ChunkFile;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function index()
    {
        return view('backends.chunk_file.uploades');
    }
    public function upload(Request $request)
    {
        try {
            $fileName = $request->get('name');
            $chunkIndex = $request->get('chunkIndex');
            $totalChunks = $request->get('totalChunks');
            $file = $request->file('file');

            $tempDir = storage_path('app/public/uploads/temp/');
            $finalDir = public_path('uploads/all_photo/');

            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $tempFileName = $fileName . '.part' . $chunkIndex;
            $tempFilePath = $tempDir . $tempFileName;
            $file->move($tempDir, $tempFileName);

            $finalFilePath = $finalDir . $fileName;

            if ($chunkIndex == $totalChunks - 1) {
                $finalFile = fopen($finalFilePath, 'ab');

                for ($i = 0; $i < $totalChunks; $i++) {
                    $chunkFilePath = $tempDir . $fileName . '.part' . $i;
                    $chunk = fopen($chunkFilePath, 'rb');
                    while ($content = fread($chunk, 1024)) {
                        fwrite($finalFile, $content);
                    }
                    fclose($chunk);
                    unlink($chunkFilePath);
                }
                fclose($finalFile);
                $chunkFile = new ChunkFile();
                $chunkFile->chunk_files = $fileName;
                $chunkFile->save();

                return response()->json(['success' => 'File uploaded and stored successfully!']);
            } else {
                return response()->json(['success' => 'Chunk uploaded successfully!']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}

