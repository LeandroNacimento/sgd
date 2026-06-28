<?php

namespace App\Http\Controllers;

use App\Enums\DocumentPriority;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Category;
use App\Models\Document;
use App\Models\DocumentState;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService
    ) {}

    public function index(): View
    {
        Gate::authorize('viewAny', Document::class);

        $documents = Document::query()
            ->with(['category', 'documentState', 'responsibleUser'])
            ->latest()
            ->paginate(10);

        return view('documents.index', compact('documents'));
    }

    public function create(): View
    {
        Gate::authorize('create', Document::class);

        $categories = Category::all();
        $states = DocumentState::all();
        $priorities = DocumentPriority::cases();

        return view('documents.create', compact('categories', 'states', 'priorities'));
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        // The authenticated user is responsible for the document they create
        $data['responsible_user_id'] = auth()->id();

        $this->documentService->create($data);

        return redirect()->route('documents.index')->with('success', 'Document created successfully.');
    }

    public function show(Document $document): View
    {
        Gate::authorize('view', $document);

        $document->load(['category', 'documentState', 'responsibleUser']);

        return view('documents.show', compact('document'));
    }

    public function edit(Document $document): View
    {
        Gate::authorize('update', $document);

        $categories = Category::all();
        $states = DocumentState::all();
        $priorities = DocumentPriority::cases();

        return view('documents.edit', compact('document', 'categories', 'states', 'priorities'));
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
