<table class="ui-table">
    <thead>
        <tr>
            <th>Asset Name</th>
            <th>Category</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($assets as $asset)
            <tr>
                <td class="cell-primary">{{ $asset->name }}</td>
                <td>{{ $asset->category }}</td>
                <td><span class="badge badge-gray">{{ $asset->status }}</span></td>
                <td class="text-right">
                    <div class="action-group" style="justify-content:flex-end;">
                        <a href="{{ route('assets.edit', $asset->id) }}" class="action-btn" title="Edit" aria-label="Edit"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-edit"/></svg></a>
                        <button type="button" onclick="deleteAsset({{ $asset->id }})" class="action-btn action-delete" title="Delete" aria-label="Delete"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-trash"/></svg></button>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
