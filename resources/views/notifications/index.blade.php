<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('notifications.title') }}
            </h2>
            @if($notifications->count() > 0)
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    <x-ds.button type="submit" variant="secondary" size="sm">
                        {{ __('notifications.mark_all_read') }}
                    </x-ds.button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4">
                            <p class="text-green-700">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if($notifications->isEmpty())
                        <div class="text-center py-12 text-slate-500">
                            {{ __('notifications.empty') }}
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($notifications as $notification)
                                <div class="flex justify-between items-center p-4 border rounded-lg {{ $notification->unread() ? 'bg-blue-50 border-blue-100' : 'bg-white border-slate-200' }}">
                                    <div>
                                        <p class="text-sm text-slate-800 font-medium">
                                            {{ $notification->data['message'] ?? 'Notification' }}
                                        </p>
                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                        @if(isset($notification->data['document_id']))
                                            <a href="{{ route('documents.show', $notification->data['document_id']) }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">
                                                {{ __('notifications.view_document') }}
                                            </a>
                                        @endif
                                    </div>
                                    @if($notification->unread())
                                        <form action="{{ route('notifications.markRead', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                {{ __('notifications.mark_as_read') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
