<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InternshipController extends Controller
{
    public function show(Request $request)
    {
        $internship = $request->user()->currentInternship()?->load('lecturer.lecturerProfile');

        return view('mahasiswa.magang', compact('internship'));
    }
}
