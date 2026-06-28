<?php

namespace App\Http\Controllers;

use App\Enums\DocumentPriority;
use App\Http\Requests\IndexDocumentRequest;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentState;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService
    ) {}

    public function index(IndexDocumentRequest $request): View
    {
        Gate::authorize('viewAny', Document::class);

        $documents = Document::query()
            ->with(['category', 'responsibleUser', 'currentVersion.documentState'])
            ->filter($request->validated())
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::all();
        $states = DocumentState::all();
        $priorities = DocumentPriority::cases();

        return view('documents.index', compact('documents', 'categories', 'states', 'priorities'));
    }

    public function create(): View
    {
        Gate::authorize('create', Document::class);

        $categories = Category::all();
        $priorities = DocumentPriority::cases();

        return view('documents.create', compact('categories', 'priorities'));
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['responsible_user_id'] = auth()->id();

        $this->documentService->create($data);

        return redirect()->route('documents.index')->with('success', 'Document created successfully.');
    }

    public function show(Document $document): View
    {
        Gate::authorize('view', $document);

        $document->load(['category', 'responsibleUser', 'versions.documentState']);

        $versionId = request('version_id');
        $version = $versionId
            ? $document->versions()->findOrFail($versionId)
            : $document->currentVersion;

        $activities = [];
        if (auth()->user()->can('is-admin')) {
            $activities = Activity::forSubject($version)
                ->with('causer')
                ->latest()
                ->get();
        }

        return view('documents.show', compact('document', 'version', 'activities'));
    }

    public function edit(Document $document): View
    {
        Gate::authorize('update', $document);

        $categories = Category::all();
        $priorities = DocumentPriority::cases();

        $document->load('currentVersion');

        return view('documents.edit', compact('document', 'categories', 'priorities'));
    }

    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        $this->documentService->update($document, $request->validated());

        return redirect()->route('documents.index')->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        Gate::authorize('delete', $document);

        $this->documentService->delete($document);

        return redirect()->route('documents.index')->with('success', 'Document deleted successfully.');
    }
}
