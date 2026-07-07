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

    public $tries = 30;

    public $maxExceptions = 3;

    public $timeout = 300; // 5 minutes for OCR

    public ?string $operationLocation = null;

    public int $pollAttempts = 0;

    public function __construct(
        public DocumentVersion $documentVersion,
        public Media $media
    ) {
        $this->onQueue('ocr');
    }

    public function handle(): void
    {
        $endpoint = rtrim(config('services.azure.ocr_endpoint'), '/');
        $key = config('services.azure.ocr_key');

        if (empty($endpoint) || empty($key)) {
            Log::warning('Azure OCR config missing. Skipping OCR.');

            return;
        }

        // Phase 1: Submit to Azure
        if (! $this->operationLocation) {
            $url = "{$endpoint}/formrecognizer/documentModels/prebuilt-read:analyze?api-version=2023-07-31";

            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $key,
                'Content-Type' => 'application/octet-stream',
            ])->send('POST', $url, [
                'body' => $this->media->stream(),
            ]);

            if (! $response->successful()) {
                throw new \Exception('Azure OCR Submission Failed: '.$response->body());
            }

            $this->operationLocation = $response->header('Operation-Location');
            if (! $this->operationLocation) {
                throw new \Exception('No Operation-Location header received from Azure.');
            }

            // Release the job back to the queue with a 5-second delay
            $this->release(5);

            return;
        }

        // Phase 2: Poll for results
        $this->pollAttempts++;

        if ($this->pollAttempts > 30) {
            Log::warning('OCR timed out after maximum polling attempts', ['media_id' => $this->media->id]);

            return;
        }

        $resultResponse = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $key,
        ])->get($this->operationLocation);

        if ($resultResponse->successful()) {
            $status = $resultResponse->json('status');

            if ($status === 'succeeded') {
                $extractedText = $resultResponse->json('analyzeResult.content');
                $this->saveExtractedText($extractedText);

                return;
            }

            if ($status === 'failed') {
                throw new \Exception('Azure OCR Processing Failed: '.$resultResponse->body());
            }

            // Still processing, release again
            $this->release(5);

            return;
        }

        throw new \Exception('Azure OCR Polling Failed: '.$resultResponse->body());
    }

    private function saveExtractedText(string $extractedText): void
    {
        if (empty($extractedText)) {
            return;
        }

        $version = DocumentVersion::find($this->documentVersion->id);
        if ($version) {
            $currentText = $version->extracted_text ? $version->extracted_text."\n\n" : '';
            $version->extracted_text = $currentText.$extractedText;
            $version->save();
        }
    }
}
