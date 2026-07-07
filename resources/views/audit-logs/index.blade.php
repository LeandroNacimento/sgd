<x-app-layout>
    <x-slot name="title">System Audit Logs</x-slot>

    <x-ds.card noPadding="true" class="mb-6">
        <x-ds.table :headers="[
            'Timestamp',
            'Event',
            'Actor',
            'Subject',
            'Description'
        ]">
            @forelse($activities as $activity)
                <tr class="hover:bg-slate-50 ds-transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $activity->event }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $activity->causer->name ?? 'System' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        @if($activity->subject_type)
                            {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $activity->description }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12">
                        <x-ds.empty-state title="No audit logs found" />
                    </td>
                </tr>
            @endforelse
        </x-ds.table>

        @if($activities->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $activities->links() }}
            </div>
        @endif
    </x-ds.card>
</x-app-layout>
