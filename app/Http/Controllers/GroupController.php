<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Organization;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organization $organization)
    {
        return Inertia::render("admin/organizations/groups/create", [
            "organization" => $organization,
            "new_ordering" => $organization->groups()->count() + 1,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request, Organization $organization)
    {
        // 1. Validasi Data dari Vue
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'min_candidates' => 'required|integer|min:1',
            'ordering' => 'required|integer',
        ]);

        // 2. Simpan Group baru sebagai anak dari Organization
        $organization->groups()->create($validated);

        // 3. Redirect kembali ke halaman Detail Organisasi
        return redirect()
            ->route('admin.organizations.show', $organization->id)
            ->with('flash.message', 'Group successfully added!')
            ->with('flash.type', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization, Group $group)
    {
        return Inertia::render("admin/organizations/groups/show", [
            "group" => $group->load("candidates"),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization, Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, Organization $organization, Group $group)
    {
        // 1. Validasi Data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'min_candidates' => 'required|integer|min:1',
            'ordering' => 'required|integer',
        ]);

        // 2. Update data ke MySQL
        $group->update($validated);

        // 3. Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()
            ->back()
            ->with('flash.message', 'Group successfully updated!')
            ->with('flash.type', 'success');
    }
    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Organization $organization, Group $group)
    {
        try {
            // Coba hapus grup
            $group->delete();

            return redirect()
                ->route('admin.organizations.show', $organization->id)
                ->with('flash.message', 'Group successfully deleted!')
                ->with('flash.type', 'success');

        } catch (QueryException $e) {
            // Tangkap error jika grup ini sudah memiliki kandidat atau data suara
            if ($e->getCode() == "23000") {
                return redirect()
                    ->back()
                    ->with('flash.message', 'Gagal menghapus! Hapus semua Kandidat di dalam grup ini terlebih dahulu.')
                    ->with('flash.type', 'destructive');
            }
            throw $e;
        }
    }
}
