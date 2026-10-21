<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Models\JobApplication;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::withCount('applications')->latest()->paginate(15);
        return view('jobs.index', compact('jobs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_name' => 'required|string|max:180',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:180',
            'employment_type' => 'nullable|string|max:80',
        ]);

        Job::create(array_merge($validated, [
            'is_published' => 1,
            'created_by' => Auth::id(),
        ]));

        return back()->with('success', 'Job posting created successfully.');
    }

    public function show($id)
    {
        $job = Job::with('applications')->findOrFail($id);
        return view('jobs.show', compact('job'));
    }

    public function togglePublish($id)
    {
        $job = Job::findOrFail($id);
        $job->update(['is_published' => !$job->is_published]);

        return back()->with('success', 'Job publication status updated.');
    }

    public function delete($id)
    {
        $job = Job::findOrFail($id);
        $job->applications()->delete();
        $job->delete();

        return back()->with('success', 'Job deleted successfully.');
    }

    // Public / Frontend Jobs
    public function frontend()
    {
        $jobs = Job::where('is_published', 1)->latest()->paginate(12);
        return view('jobs.frontend', compact('jobs'));
    }

    public function apply(Request $request, $id)
    {
        $job = Job::where('is_published', 1)->findOrFail($id);

        $validated = $request->validate([
            'candidate_name' => 'required|string|max:180',
            'email' => 'required|email|max:190',
            'contact_number' => 'required|string|max:40',
            'resume_file' => 'required|file|mimes:pdf,doc,docx|max:3072',
        ]);

        $filename = '';
        if ($request->hasFile('resume_file')) {
            $file = $request->file('resume_file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $dest = public_path('uploads/job_resumes');
            if (!file_exists($dest)) mkdir($dest, 0777, true);
            $file->move($dest, $filename);
        }

        JobApplication::create([
            'job_id' => $job->id,
            'candidate_name' => $validated['candidate_name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'],
            'resume_file' => $filename,
            'applied_at' => now(),
        ]);

        return back()->with('success', 'Your application has been submitted successfully.');
    }
}

