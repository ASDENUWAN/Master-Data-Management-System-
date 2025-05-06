<?php

namespace App\Http\Controllers;

use App\Models\MasterCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;



class MasterCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterCategory::query();

        if (!Auth::user()->is_admin) {
            $query->where('user_id', Auth::id());
        }

        // Search by code or name
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                    ->orWhere('name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Only admins can filter by user_id
        if (Auth::user()->is_admin && $request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $categories = $query->orderBy('created_at', 'desc')->paginate(5);

        return view('categories.index', compact('categories'))
            ->with('search', $request->search)
            ->with('status', $request->status)
            ->with('user_id', $request->user_id);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:master_categories,code|max:20',
            'name' => 'required|max:100',
        ]);

        MasterCategory::create([
            'user_id' => Auth::id(),
            'code' => $request->code,
            'name' => $request->name,
            'status' => 'Active',
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $category = MasterCategory::findOrFail($id);

        if (!Auth::user()->is_admin && $category->user_id !== Auth::id()) {
            abort(403);
        }

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = MasterCategory::findOrFail($id);

        if (!Auth::user()->is_admin && $category->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'code' => 'required|max:20|unique:master_categories,code,' . $category->id,
            'name' => 'required|max:100',
            'status' => 'required|in:Active,Inactive',
        ]);

        $category->update([
            'code' => $request->code,
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = MasterCategory::findOrFail($id);

        if (!Auth::user()->is_admin && $category->user_id !== Auth::id()) {
            abort(403);
        }

        // Prevent deletion if items exist
        if ($category->items()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Cannot delete category: It is assigned to one or more items.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Build the filtered query for categories.
     */
    protected function filteredCategories(Request $request)
    {
        $q = MasterCategory::query();

        if (!Auth::user()->is_admin) {
            $q->where('user_id', Auth::id());
        }
        if ($s = $request->search) {
            $q->where(
                fn($q) => $q
                    ->where('code', 'like', "%$s%")
                    ->orWhere('name', 'like', "%$s%")
            );
        }
        if ($st = $request->status) {
            $q->where('status', $st);
        }
        if (Auth::user()->is_admin && ($u = $request->user_id)) {
            $q->where('user_id', $u);
        }

        return $q->orderBy('created_at', 'desc');
    }

    /** Export CSV */
    public function exportCsv(Request $request)
    {
        $cats = $this->filteredCategories($request)->get();
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="categories_' . now()->format('Ymd_His') . '.csv"',
        ];
        $callback = function () use ($cats) {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['ID', 'User ID', 'Code', 'Name', 'Status', 'Created At']);
            foreach ($cats as $c) {
                fputcsv($f, [
                    $c->id,
                    $c->user_id,
                    $c->code,
                    $c->name,
                    $c->status,
                    $c->created_at->toDateTimeString(),
                ]);
            }
            fclose($f);
        };
        return response()->stream($callback, 200, $headers);
    }

    /** Export XLSX */
    public function exportXlsx(Request $request)
    {
        $cats = $this->filteredCategories($request)->get();
        $sheet = (new Spreadsheet())->getActiveSheet();
        $sheet->fromArray(['ID', 'User ID', 'Code', 'Name', 'Status', 'Created At'], null, 'A1');
        $row = 2;
        foreach ($cats as $c) {
            $sheet->fromArray([
                $c->id,
                $c->user_id,
                $c->code,
                $c->name,
                $c->status,
                $c->created_at->toDateTimeString(),
            ], null, "A{$row}");
            $row++;
        }
        $writer = new Xlsx($sheet->getParent());
        $filename = 'categories_' . now()->format('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /** Export PDF */
    public function exportPdf(Request $request)
    {
        $cats = $this->filteredCategories($request)->get();
        $pdf = Pdf::loadView('categories.export-pdf', compact('cats'));
        return $pdf->download('categories_' . now()->format('Ymd_His') . '.pdf');
    }
}
