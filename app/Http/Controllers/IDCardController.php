<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;

class IDCardController extends Controller
{
    public function showUploadForm()
    {
        return view('backends.id_card.index');
    }

    public function uploadAndDetect(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'id_card_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Store the uploaded image
        $imagePath = $request->file('id_card_image')->store('id_card_images', 'public');

        // Initialize the Google Cloud Vision client
        $imageAnnotator = new ImageAnnotatorClient([
            'credentials' => storage_path('app/google-cloud-credentials.json'), // Path to your Google Cloud credentials file
        ]);

        // Perform text detection on the image
        $image = file_get_contents(storage_path('app/public/' . $imagePath));
        $response = $imageAnnotator->textDetection($image);
        $texts = $response->getTextAnnotations();

        // Extract relevant information from the detected text
        $idCardInfo = $this->parseIDCardText($texts);

        // Close the client
        $imageAnnotator->close();

        // Return the extracted information
        return view('backends.id_card.result', ['idCardInfo' => $idCardInfo]);
    }

    private function parseIDCardText($texts)
    {
        $idCardInfo = [];

        foreach ($texts as $text) {
            $description = $text->getDescription();

            // Example: Extract ID number
            if (preg_match('/ID No\.\s*(\d+)/', $description, $matches)) {
                $idCardInfo['id_number'] = $matches[1];
            }

            // Example: Extract name
            if (preg_match('/Name:\s*([A-Za-z\s]+)/', $description, $matches)) {
                $idCardInfo['name'] = $matches[1];
            }

            // Add more fields as needed
        }

        return $idCardInfo;
    }
}
