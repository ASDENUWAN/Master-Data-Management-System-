<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h3>Items Export</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Code</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i)
            <tr>
                <td>{{ $i->id }}</td>
                <td>{{ $i->user_id }}</td>
                <td>{{ $i->code }}</td>
                <td>{{ $i->name }}</td>
                <td>{{ $i->brand->name }}</td>
                <td>{{ $i->category->name }}</td>
                <td>{{ $i->status }}</td>
                <td>{{ $i->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>