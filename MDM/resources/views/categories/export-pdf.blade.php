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
            margin-top: 10px;
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
    <h3>Categories Export</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Code</th>
                <th>Name</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cats as $c)
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->user_id }}</td>
                <td>{{ $c->code }}</td>
                <td>{{ $c->name }}</td>
                <td>{{ $c->status }}</td>
                <td>{{ $c->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>