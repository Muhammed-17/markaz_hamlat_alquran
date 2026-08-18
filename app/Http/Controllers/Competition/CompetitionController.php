<?php

namespace App\Http\Controllers\Competition;

use App\Http\Controllers\Controller;
use App\Http\Requests\Competition\StoreCompetitionRequest;
use App\Http\Requests\Competition\UpdateCompetitionRequest;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompetitionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Competition::class);

        $competitions = Competition::query()
            ->withCount(['levels', 'competitionParticipants'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('competitions.index', compact('competitions'));
    }

    public function create()
    {
        $this->authorize('create', Competition::class);

        return view('competitions.create');
    }

    public function store(StoreCompetitionRequest $request)
    {
        $this->authorize('create', Competition::class);

        $competition = Competition::create($request->validated());

        // التوجيه إلى صفحة تحديد المستويات الخاصة بالمسابقة المنشأة حديثاً
        return redirect()
            ->route('competitions.levels', $competition)
            ->with('success', 'تم إنشاء المسابقة بنجاح، يُرجى تحديد المستويات المطلوبة.');
    }
    public function edit(Competition $competition)
    {
        $this->authorize('update', $competition);

        return view('competitions.edit', compact('competition'));
    }

    public function update(UpdateCompetitionRequest $request, Competition $competition)
    {
        $this->authorize('update', $competition);

        $competition->update($request->validated());

        return redirect()
            ->route('competitions.index')
            ->with('success', 'تم تحديث المسابقة بنجاح.');
    }

    public function destroy(Competition $competition)
    {
        $this->authorize('delete', $competition);

        $competition->delete();

        return redirect()
            ->route('competitions.index')
            ->with('success', 'تم حذف المسابقة بنجاح.');
    }

    public function duplicate(Competition $competition)
    {
        $this->authorize('create', Competition::class);

        DB::transaction(function () use ($competition) {
            $new = $competition->replicate();
            $new->name = $competition->name . ' (نسخة)';
            $new->status = 'draft';
            $new->save();

            // نسخ المستويات المربوطة بالمسابقة
            $new->levels()->sync($competition->levels->pluck('id'));
        });

        return redirect()
            ->route('competitions.index')
            ->with('success', 'تم نسخ المسابقة بنجاح.');
    }
}
