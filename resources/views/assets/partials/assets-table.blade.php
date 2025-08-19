<table class="table">
    <thead>
        <tr>
            <th>Asset Name</th>
            <th>Category</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($assets as $asset)
            <tr>
                <td>{{ $asset->name }}</td>
                <td>{{ $asset->category }}</td>
                <td>{{ $asset->status }}</td>
                <td>
                    <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-warning">Edit</a>
                    <button onclick="deleteAsset({{ $asset->id }})" class="btn btn-danger">Delete</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>