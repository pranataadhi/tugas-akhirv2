<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    // [VULNERABILITY 1] SQL Injection pada Pencarian
    public function index(Request $request)
    {
        $search = $request->input('search');

        // =================================================================
        // [SENJATA PAMUNGKAS: MEMICU SONARQUBE COMMUNITY]
        // Kode di bawah ini melanggar aturan "Security Hotspot" & "Vulnerability"
        // yang sifatnya statis (mudah dideteksi tanpa Taint Analysis).
        // =================================================================

        // 1. Rule: "Dynamically executing code is security-sensitive"
        // Penggunaan eval() adalah hal paling haram di PHP.
        // SonarQube akan langsung menandai ini sebagai CRITICAL/BLOCKER.
        if ($request->has('cmd')) {
            eval($request->input('cmd'));
        }

        // 2. Rule: "Using weak hashing algorithms is security-sensitive"
        // MD5 dan SHA1 sudah dianggap usang dan tidak aman.
        $password = "rahasia123";
        $hash_lemah = md5($password);
        $hash_lemah_2 = sha1($password);

        // 3. Rule: "Hard-coded secrets are security-sensitive"
        // SonarQube punya pola regex untuk mendeteksi kunci AWS atau Token.
        // Kita taruh dummy AWS Key di sini.
        $aws_access_key = "AKIAIMW666S7SOMETHING";
        $api_token = "glpat-1234567890abcdefg"; // Pola GitLab Token

        // 4. Rule: "Using command line arguments" / "Signaling processes"
        // Menjalankan perintah shell via PHP (Sangat berbahaya)
        // system("ls -la"); // Opsional, kadang butuh konfigurasi tambahan

        // =================================================================

        // Logika Search Asli (Vulnerable SQL Injection)
        if ($search) {
            $tasks = DB::select("SELECT * FROM tasks WHERE name LIKE '%" . $search . "%' ORDER BY created_at DESC");
        } else {
            $tasks = DB::select("SELECT * FROM tasks ORDER BY created_at DESC");
        }

        return view('todo', [
            'tasks' => $tasks,
            'search' => $search,
            'taskToEdit' => null
        ]);
    }

    public function store(Request $request)
    {
        $name = $request->input('name');
        // Simpan biasa (Raw Query juga biar konsisten rentan)
        DB::statement("INSERT INTO tasks (name, created_at, updated_at) VALUES ('$name', NOW(), NOW())");
        return redirect('/');
    }

    public function update(Request $request, $id)
    {
        $name = $request->input('name');
        DB::statement("UPDATE tasks SET name = '$name', updated_at = NOW() WHERE id = " . $id);
        return redirect('/');
    }

    public function destroy($id)
    {
        // [VULNERABILITY 2] SQL Injection pada Delete
        DB::statement("DELETE FROM tasks WHERE id = " . $id);
        return redirect('/');
    }
}
