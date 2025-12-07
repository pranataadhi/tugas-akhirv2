<!DOCTYPE html>
<html>

<head>
    <title>Simple Todo</title>
    <style>
        body {
            font-family: sans-serif;
            max-width: 600px;
            margin: 50px auto;
            background: #f9f9f9;
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        form {
            margin-bottom: 20px;
            display: flex;
            gap: 5px;
        }

        input {
            padding: 10px;
            width: 100%;
            border: 1px solid #ddd;
        }

        button {
            padding: 10px 20px;
            cursor: pointer;
            background: #007bff;
            color: white;
            border: none;
        }

        .btn-danger {
            background: #dc3545;
            text-decoration: none;
            padding: 5px 10px;
            color: white;
            font-size: 12px;
        }

        .btn-warning {
            background: #ffc107;
            text-decoration: none;
            padding: 5px 10px;
            color: black;
            font-size: 12px;
            margin-right: 5px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background: white;
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <h1>Simple Todo List</h1>

    <form action="/" method="GET">
        <input type="text" name="search" placeholder="Cari tugas..." value="{{ $search ?? '' }}">
        <button type="submit">Cari</button>
    </form>

    @if ($search)
        <h3>Hasil pencarian: {!! $search !!}</h3>
    @endif

    <hr>

    @if (isset($taskToEdit))
        <h3>Edit Tugas</h3>
        <form action="/update/{{ $taskToEdit->id }}" method="POST">
            @csrf
            <input type="text" name="name" value="{{ $taskToEdit->name }}" required>
            <button type="submit" style="background: #28a745;">Update</button>
            <a href="/" style="padding: 10px;">Batal</a>
        </form>
    @else
        <h3>Tambah Tugas</h3>
        <form action="/add" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Tugas baru..." required>
            <button type="submit">Tambah</button>
        </form>
    @endif

    <ul>
        @foreach ($tasks as $task)
            <li>
                <span>{!! $task->name !!}</span>
                <div>
                    <a href="/?edit_id={{ $task->id }}" class="btn-warning">Edit</a>
                    <a href="/delete/{{ $task->id }}" class="btn-danger"
                        onclick="return confirm('Yakin?')">Hapus</a>
                </div>
            </li>
        @endforeach
    </ul>
</body>

</html>
