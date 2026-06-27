<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\JobApplication;
use Illuminate\Http\Request;



class JobApplicationController extends Controller
{
    public function getStats()
    {
        $userId = Auth::id();
    
        $stats = JobApplication::where('user_id', $userId)
            ->selectRaw("
                COUNT(CASE WHEN status = 'Applied' THEN 1 END) as applied,
                COUNT(CASE WHEN status = 'Interview' THEN 1 END) as interview,
                COUNT(CASE WHEN status = 'Ghosting' THEN 1 END) as ghosting,
                COUNT(CASE WHEN status = 'Rejected' THEN 1 END) as rejected
            ")
            ->first();
    
        return response()->json([
            'applied'   => (int) ($stats->applied ?? 0),
            'interview' => (int) ($stats->interview ?? 0),
            'ghosting'  => (int) ($stats->ghosting ?? 0),
            'rejected'  => (int) ($stats->rejected ?? 0),
        ]);
    }
    public function index(Request $request)
    {
        $jobs = $request->user()->jobApplications()->latest('applied_date')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar lowongan kerja berhasil diambil',
            'data' => $jobs
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'status' => 'required|string|in:Applied,Interview,Rejected,Ghosting',
            'applied_date' => 'required|date',
            'job_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'salary_expectation' => 'nullable|integer',
        ]);

        $job = $request->user()->jobApplications()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Lowongan pekerjaan berhasil ditambahkan!',
            'data' => $job
        ], 201);
    }

    public function show(Request $request, string $id)
    {
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

    public function update(Request $request, string $id)
    {
        $job = $request->user()->jobApplications()->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        $request->validate([
            'company_name' => 'sometimes|required|string|max:255',
            'job_title' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|in:Applied,Interview,Offered,Rejected,Ghosting',
            'applied_date' => 'sometimes|required|date',
            'job_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'salary_expectation' => 'nullable|integer',
        ]);

        $job->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data lowongan berhasil diperbarui!',
            'data' => $job
        ], 200);
    }

    public function destroy(Request $request, string $id)
    {
        $job = $request->user()->jobApplications()->find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lowongan pekerjaan berhasil dihapus.'
        ], 200);
    }

}
