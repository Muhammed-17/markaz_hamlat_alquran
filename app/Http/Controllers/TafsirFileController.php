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
        $this->authorize('manage competitions');

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
        $this->authorize('manage competitions');

        return view('tafsir_files.create');
    }

    /**
     * Store a newly created tafsir file.
     */
    public function store(StoreTafsirFileRequest $request)
    {
        $this->authorize('manage competitions');

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
        $this->authorize('manage competitions');

        return view('tafsir_files.edit', compact('tafsirFile'));
    }

    /**
     * Update the specified tafsir file.
     */
    public function update(
        UpdateTafsirFileRequest $request,
        TafsirFile $tafsirFile
    ) {
        $this->authorize('manage competitions');

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
        $this->authorize('manage competitions');

        $tafsirFile->delete();

        return redirect()
            ->route('tafsir-files.index')
            ->with('success', 'تم حذف ملف التفسير بنجاح.');
    }
}