<td>
    <a href="{{ route('weighbridge_entries.invoice', $entry->id) }}" target="_blank" class="btn btn-primary btn-sm">
        Print
    </a>
    <a href="{{ route('weighbridge_entries.show', $entry->id) }}" class="btn btn-default btn-xs">
        <i class="fas fa-eye"></i> View
    </a>
    <a href="{{ route('weighbridge_entries.edit', $entry->id) }}" class="btn btn-sm btn-warning">Edit</a>
    @role('admin')
    <form action="{{ route('weighbridge-entries.delete', $entry->id) }}" method="POST" style="display:inline;"
        onsubmit="return confirm('Are you sure you want to delete this entry {{ $entry->id }}?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">
            <i class="fas fa-trash-alt"></i> Delete
        </button>
    </form>
    @endrole
</td>