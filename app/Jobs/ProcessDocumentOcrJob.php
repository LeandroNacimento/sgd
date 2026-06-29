<?php

namespace App\Jobs;

use App\Models\DocumentVersion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProcessDocumentOcrJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(
        public DocumentVersion $documentVersion,
        public Media $media
    ) {}

    public function handle(): void
    {
        $endpoint = rtrim(config('services.azure.ocr_endpoint'), '/');
        $key = config('services.azure.ocr_key');

        if (empty($endpoint) || empty($key)) {
            Log::warning('Azure OCR config missing. Skipping OCR.');
            return;
        }

        $url = "{$endpoint}/formrecognizer/documentModels/prebuilt-read:analyze?api-version=2023-07-31";

        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $key,
            'Content-Type' => 'application/octet-stream',
        ])->send('POST', $url, [
            'body' => $this->media->stream()
        ]);

        if (!$response->successful()) {
            $this->fail(new \Exception('Azure OCR Submission Failed: ' . $response->body()));
            return;
        }

        $operationLocation = $response->header('Operation-Location');
        if (!$operationLocation) {
            $this->fail(new \Exception('No Operation-Location header received from Azure.'));
            return;
        }

        $attempts = 0;
        $extractedText = '';

        while ($attempts < 30) {
            sleep(2);
            $attempts++;

            $resultResponse = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $key,
            ])->get($operationLocation);

            if ($resultResponse->successful()) {
                $status = $resultResponse->json('status');

                if ($status === 'succeeded') {
                    $extractedText = $resultResponse->json('analyzeResult.content');
                    break;
                }

                if ($status === 'failed') {
                    $this->fail(new \Exception('Azure OCR Processing Failed: ' . $resultResponse->body()));
                    return;
                }
            } else {
                $this->fail(new \Exception('Azure OCR Polling Failed: ' . $resultResponse->body()));
                return;
            }
        }

        if (empty($extractedText)) {
            Log::warning('OCR returned empty text or timed out', ['media_id' => $this->media->id]);
            return;
        }

        $version = DocumentVersion::find($this->documentVersion->id);
        if ($version) {
            $currentText = $version->extracted_text ? $version->extracted_text . "\n\n" : '';
            $version->extracted_text = $currentText . $extractedText;
            $version->save();
        }
    }
}
