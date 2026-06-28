<?php

namespace App\Services;

use App\Enums\DocumentStateName;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentState;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DashboardService
{
    public function getDashboardData(): array
    {
        return [
            'total_documents' => $this->getTotalDocuments(),
            'documents_by_state' => $this->getDocumentsByState(),
            'documents_by_category' => $this->getDocumentsByCategory(),
            'recent_activities' => $this->getRecentActivities(),
            'recent_documents' => $this->getRecentDocuments(),
            'awaiting_review' => $this->getDocumentsAwaitingReview(),
            'recently_published' => $this->getRecentlyPublishedDocuments(),
        ];
    }

    private function getTotalDocuments(): int
    {
        return Document::count();
    }

    private function getDocumentsByState(): array
    {
        $states = DocumentState::all();
        $counts = Document::select('document_state_id', DB::raw('count(*) as total'))
            ->groupBy('document_state_id')
            ->pluck('total', 'document_state_id')
            ->toArray();

        $result = [];
        foreach ($states as $state) {
            $result[$state->name] = [
                'id' => $state->id,
                'count' => $counts[$state->id] ?? 0,
            ];
        }

        return $result;
    }

    private function getDocumentsByCategory(): array
    {
        $categories = Category::all();
        $counts = Document::select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->pluck('total', 'category_id')
            ->toArray();

        $result = [];
        foreach ($categories as $category) {
            $result[$category->name] = [
                'id' => $category->id,
                'count' => $counts[$category->id] ?? 0,
            ];
        }

        return $result;
    }

    private function getRecentActivities(int $limit = 10)
    {
        return Activity::with('causer', 'subject')
            ->latest()
            ->take($limit)
            ->get();
    }

    private function getRecentDocuments(int $limit = 5)
    {
        return Document::with(['category', 'documentState', 'responsibleUser'])
            ->latest()
            ->take($limit)
            ->get();
    }

    private function getDocumentsAwaitingReview(int $limit = 5)
    {
        $state = DocumentState::where('name', DocumentStateName::InReview->value)->first();
        if (! $state) {
            return collect();
        }

        return Document::with(['category', 'documentState', 'responsibleUser'])
            ->where('document_state_id', $state->id)
            ->latest()
            ->take($limit)
            ->get();
    }

    private function getRecentlyPublishedDocuments(int $limit = 5)
    {
        $state = DocumentState::where('name', DocumentStateName::Published->value)->first();
        if (! $state) {
            return collect();
        }

        return Document::with(['category', 'documentState', 'responsibleUser'])
            ->where('document_state_id', $state->id)
            ->latest()
            ->take($limit)
            ->get();
    }
}
