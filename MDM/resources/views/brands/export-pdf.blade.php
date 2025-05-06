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

        th {
            background: #f0f0f0;
        }
    </style>
</head>

<body>
    <h3>Brands Export</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Code</th>
                <th>Name</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($brands as $b)
            <tr>
                <td>{{ $b->id }}</td>
                <td>{{ $b->user_id }}</td>
                <td>{{ $b->code }}</td>
                <td>{{ $b->name }}</td>
                <td>{{ $b->status }}</td>
                <td>{{ $b->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>