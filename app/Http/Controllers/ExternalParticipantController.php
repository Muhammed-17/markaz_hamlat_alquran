<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExternalParticipant\StoreExternalParticipantRequest;
use App\Http\Requests\ExternalParticipant\UpdateExternalParticipantRequest;
use App\Models\ExternalParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class ExternalParticipantController
 *
 * Handles CRUD operations for external competition participants.
 */
class ExternalParticipantController extends Controller
{
    // public function __construct()
    // {
    //     $this->authorizeResource(ExternalParticipant::class, 'external_participant');
    // }

    public function index(Request $request): View
    {
        $sort = $request->get('sort', 'name');
        $dir  = $request->get('dir', 'asc') === 'desc' ? 'desc' : 'asc';

        $allowedSorts = ['name', 'created_at'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }

        $externalParticipants = ExternalParticipant::query()
            ->withCount('competitionParticipants')
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->q;
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('national_id', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $dir)
            ->paginate(15)
            ->withQueryString();

        return view('external-participants.index', compact('externalParticipants'));
    }

    public function create(): View
    {
        return view('external-participants.create');
    }

    public function store(StoreExternalParticipantRequest $request): RedirectResponse
    {
        ExternalParticipant::create($request->validated());

        return redirect()
            ->route('external-participants.index')
            ->with('success', 'تم إضافة المشارك الخارجي بنجاح.');
    }

    public function show(ExternalParticipant $externalParticipant): View
    {
        $externalParticipant->loadCount('competitionParticipants');

        return view('external-participants.show', compact('externalParticipant'));
    }

    public function edit(ExternalParticipant $externalParticipant): View
    {
        return view('external-participants.edit', compact('externalParticipant'));
    }

    public function update(UpdateExternalParticipantRequest $request, ExternalParticipant $externalParticipant): RedirectResponse
    {
        $externalParticipant->update($request->validated());

        return redirect()
            ->route('external-participants.index')
            ->with('success', 'تم تحديث بيانات المشارك الخارجي بنجاح.');
    }

    public function destroy(ExternalParticipant $externalParticipant): RedirectResponse
    {
        if ($externalParticipant->competitionParticipants()->exists()) {
            return redirect()
                ->route('external-participants.index')
                ->with('error', 'لا يمكن حذف هذا المشارك لأنه مسجَّل بمسابقات حالية.');
        }

        $externalParticipant->delete();

        return redirect()
            ->route('external-participants.index')
            ->with('success', 'تم حذف المشارك الخارجي بنجاح.');
    }
}
