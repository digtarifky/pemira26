<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Group;
use App\Models\Organization;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization, Group $group)
    {
       return redirect()->route('admin.organizations.groups.show', [
            'organization' => $organization->id,
            'group' => $group->id
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("admin/organizations/groups/candidates/create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organization $organization, Group $group)
    {
     // 1. Validasi Data yang Masuk
        $validated = $request->validate([
            'name_1' => 'required|string|max:255',
            'name_2' => 'nullable|string|max:255',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'picture' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Maks 2MB
        ]);

        // 2. Proses Upload Gambar
        $picturePath = null;
        if ($request->hasFile('picture')) {
            // Simpan gambar ke folder 'storage/app/public/candidates'
            $picturePath = $request->file('picture')->store('candidates', 'public');
        }

        // 3. Simpan ke Database melalui relasi Group
        $group->candidates()->create([
            'organization_id' => $organization->id, // Wajib diisi sesuai skema database
            'name_1' => $validated['name_1'],
            'name_2' => $validated['name_2'],
            'vision' => $validated['vision'],
            'mission' => $validated['mission'],
            'picture' => $picturePath ? '/storage/' . $picturePath : null, // Format path untuk Vue
        ]);

        // 4. Kembali ke halaman daftar dengan pesan sukses
        return redirect()
            ->route('admin.organizations.groups.show', [$organization->id, $group->id])
           ->with('flash.message', 'Candidate successfully added!')
            ->with('flash.type', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(
        Organization $organization,
        Group $group,
        Candidate $candidate
    ) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(
        Organization $organization,
        Group $group,
        Candidate $candidate
    ) {
        return Inertia::render("admin/organizations/groups/candidates/edit", [
        'organization' => $organization,
        'group' => $group,    
        "candidate" => $candidate,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        Organization $organization,
        Group $group,
        Candidate $candidate
    ) {
       // 1. Validasi Data
        $validated = $request->validate([
            'name_1' => 'required|string|max:255',
            'name_2' => 'nullable|string|max:255',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        $picturePath = $candidate->picture; // Ambil path foto lama sebagai default

        // 2. Cek apakah ada file foto baru yang diunggah
        if ($request->hasFile('picture')) {
            // Hapus foto lama dari server untuk menghemat ruang
            if ($picturePath && Storage::disk('public')->exists(ltrim($picturePath, '/'))) {
               Storage::disk('public')->delete(ltrim($picturePath, '/'));
            }
            
            // Simpan foto baru
            $newPath = $request->file('picture')->store('candidates', 'public');
            $picturePath = '/storage/' . $newPath;
        }

        // 3. EKSEKUSI PENYIMPANAN KE DATABASE (Ini yang sebelumnya hilang)
        $candidate->update([
            'organization_id' => $organization->id,
            'group_id' => $group->id,
            'name_1' => $validated['name_1'],
            'name_2' => $validated['name_2'],
            'vision' => $validated['vision'],
            'mission' => $validated['mission'],
            'picture' => $picturePath,
        ]);

        // 4. Redirect kembali dengan pesan sukses
        return redirect()
            ->route('admin.organizations.groups.show', [$organization->id, $group->id])
            ->with('flash.message', 'Candidate successfully updated!')
            ->with('flash.type', 'success');
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Organization $organization,
        Group $group,
        Candidate $candidate
    ) {
        try {
            // 1. Coba hapus data dari database terlebih dahulu
            $candidate->delete();

            // 2. JIKA BERHASIL (tidak ditolak MySQL), baru hapus foto fisik dari Storage
            if ($candidate->picture && Storage::disk('public')->exists(ltrim($candidate->picture, '/'))) {
                Storage::disk('public')->delete(ltrim($candidate->picture, '/'));
            }

            // 3. Kembalikan dengan pesan sukses
            return redirect()
                ->route('admin.organizations.groups.show', [$organization->id, $group->id])
                ->with('flash.message', 'Candidate successfully deleted!')
                ->with('flash.type', 'success');

        } catch (QueryException $e) {
            // 4. TANGKAP ERROR MySQL: Jika kode error adalah 23000 (Integrity Constraint Violation)
            if ($e->getCode() == "23000") {
                return redirect()
                    ->route('admin.organizations.groups.show', [$organization->id, $group->id])
                    ->with('flash.message', 'Gagal menghapus! Kandidat ini sudah mendapatkan suara (Vote) di dalam sistem. Bersihkan data suara terlebih dahulu.')
                    ->with('flash.type', 'destructive');
            }

            // Jika error disebabkan oleh hal lain, lempar kembali agar terlihat di log
            throw $e;
        }
    }
}
