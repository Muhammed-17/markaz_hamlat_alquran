<?php

namespace App\Http\Controllers;

use App\Http\Requests\TafsirFile\StoreTafsirFileRequest;
use App\Http\Requests\TafsirFile\UpdateTafsirFileRequest;
use App\Models\TafsirFile;

class TafsirFileController extends Controller
{
    /**
     * Display a listing of tafsir files.
     */
    public function index()
    {
        $this->authorize('viewAny', TafsirFile::class);

        $tafsirFiles = TafsirFile::query()
            ->orderBy('name')
            ->paginate(15);

        return view('tafsir_files.index', compact('tafsirFiles'));
    }

    /**
     * Show the form for creating a new tafsir file.
     */
    public function create()
    {
        $this->authorize('create', TafsirFile::class);

        return view('tafsir_files.create');
    }

    /**
     * Store a newly created tafsir file.
     */
    public function store(StoreTafsirFileRequest $request)
    {
        $this->authorize('create', TafsirFile::class);

        TafsirFile::create($request->validated());

        return redirect()
            ->route('tafsir-files.index')
            ->with('success', 'تم إضافة ملف التفسير بنجاح.');
    }

    /**
     * Show the form for editing the specified tafsir file.
     */
    public function edit(TafsirFile $tafsirFile)
    {
        $this->authorize('update', $tafsirFile);

        return view('tafsir_files.edit', compact('tafsirFile'));
    }

    /**
     * Update the specified tafsir file.
     */
    public function update(
        UpdateTafsirFileRequest $request,
        TafsirFile $tafsirFile
    ) {
        $this->authorize('update', $tafsirFile);

        $tafsirFile->update($request->validated());

        return redirect()
            ->route('tafsir-files.index')
            ->with('success', 'تم تحديث ملف التفسير بنجاح.');
    }

    /**
     * Remove the specified tafsir file.
     */
    public function destroy(TafsirFile $tafsirFile)
    {
        $this->authorize('delete', $tafsirFile);

        $tafsirFile->delete();

        return redirect()
            ->route('tafsir-files.index')
            ->with('success', 'تم حذف ملف التفسير بنجاح.');
    }
}