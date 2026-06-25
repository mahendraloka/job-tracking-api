<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\JobApplication;
use Illuminate\Http\Request;



class JobApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getStats()
    {
        // Mengambil ID user yang sedang login via token Sanctum
        $userId = Auth::id();
    
        // Menghitung jumlah data berdasarkan status masing-masing menggunakan agregasi MySQL
        $stats = JobApplication::where('user_id', $userId)
            ->selectRaw("
                COUNT(CASE WHEN status = 'Applied' THEN 1 END) as applied,
                COUNT(CASE WHEN status = 'Interview' THEN 1 END) as interview,
                COUNT(CASE WHEN status = 'Ghosting' THEN 1 END) as ghosting,
                COUNT(CASE WHEN status = 'Rejected' THEN 1 END) as rejected
            ")
            ->first();
    
        // Mengembalikan data siap konsumsi untuk React 19
        return response()->json([
            'applied'   => (int) ($stats->applied ?? 0),
            'interview' => (int) ($stats->interview ?? 0),
            'ghosting'  => (int) ($stats->ghosting ?? 0),
            'rejected'  => (int) ($stats->rejected ?? 0),
        ]);
    }
    public function index(Request $request)
    {
        // Ambil data user yang sedang login, lalu ambil data lowongannya
        // Diurutkan berdasarkan tanggal apply terbaru
        $jobs = $request->user()->jobApplications()->latest('applied_date')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar lowongan kerja berhasil diambil',
            'data' => $jobs
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input dari React
        $request->validate([
            'company_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'status' => 'required|string|in:Applied,Interview,Rejected,Ghosting',
            'applied_date' => 'required|date',
            'job_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'salary_expectation' => 'nullable|integer',
        ]);

        // Simpan data ke database lewat user yang sedang login
        $job = $request->user()->jobApplications()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Lowongan pekerjaan berhasil ditambahkan!',
            'data' => $job
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        // Cari lowongan berdasarkan ID, pastikan itu milik user yang sedang login
        $job = $request->user()->jobApplications()->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $job
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Cari data lowongan milik user
        $job = $request->user()->jobApplications()->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        // Validasi input data yang diubah
        $request->validate([
            'company_name' => 'sometimes|required|string|max:255',
            'job_title' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|in:Applied,Interview,Offered,Rejected,Ghosting',
            'applied_date' => 'sometimes|required|date',
            'job_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'salary_expectation' => 'nullable|integer',
        ]);

        // Update data di database
        $job->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data lowongan berhasil diperbarui!',
            'data' => $job
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        // Cari data lowongan milik user
        $job = $request->user()->jobApplications()->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        // Hapus dari database
        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lowongan pekerjaan berhasil dihapus.'
        ], 200);
    }

}
