<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Wajib untuk Raw SQL

class TaskController extends Controller
{
    // 1. READ & SEARCH (RENTAN SQL INJECTION)
    public function index(Request $request)
    {
        $search = $request->input('search');
        $editId = $request->input('edit_id'); // Untuk mode edit

        // Logika Pencarian (Vulnerable)
        if ($search) {
            // [BAHAYA] SQL Injection: Variabel $search digabung langsung
            $tasks = DB::select("SELECT * FROM tasks WHERE name LIKE '%" . $search . "%'");
        } else {
            $tasks = DB::select("SELECT * FROM tasks ORDER BY created_at DESC");
        }

        // Logika Edit: Jika tombol edit diklik, ambil data tugas tersebut
        $taskToEdit = null;
        if ($editId) {
            // [BAHAYA] SQL Injection pada ID
            $result = DB::select("SELECT * FROM tasks WHERE id = " . $editId);
            $taskToEdit = $result[0] ?? null;
        }

        return view('todo', [
            'tasks' => $tasks,
            'search' => $search,
            'taskToEdit' => $taskToEdit
        ]);
    }

    // 2. CREATE (TAMBAH)
    public function store(Request $request)
    {
        $name = $request->input('name');

        // [BAHAYA] SQL Injection pada Insert
        DB::statement("INSERT INTO tasks (name, created_at, updated_at) VALUES ('$name', NOW(), NOW())");

        return redirect('/');
    }

    // 3. UPDATE (EDIT)
    public function update(Request $request, $id)
    {
        $name = $request->input('name');

        // [BAHAYA] SQL Injection pada Update
        DB::statement("UPDATE tasks SET name = '$name', updated_at = NOW() WHERE id = " . $id);

        return redirect('/');
    }

    // 4. DELETE (HAPUS)
    public function destroy($id)
    {
        // [BAHAYA] SQL Injection pada Delete
        DB::statement("DELETE FROM tasks WHERE id = " . $id);
        return redirect('/');
    }
}
