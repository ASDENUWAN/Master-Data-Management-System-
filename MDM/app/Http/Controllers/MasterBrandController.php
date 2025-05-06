<?php

namespace App\Http\Controllers;

use App\Models\MasterBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;



class MasterBrandController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterBrand::query();

        // For non-admins, restrict to own data
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

        $brands = $query->orderBy('created_at', 'desc')->paginate(5);

        return view('brands.index', compact('brands'))
            ->with('search', $request->search)
            ->with('status', $request->status)
            ->with('user_id', $request->user_id);
    }


    public function create()
    {
        return view('brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:master_brands,code|max:20',
            'name' => 'required|max:100',
        ]);

        MasterBrand::create([
            'user_id' => Auth::id(),
            'code' => $request->code,
            'name' => $request->name,
            'status' => 'Active',
        ]);

        return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
    }

    public function edit($id)
    {
        $brand = MasterBrand::findOrFail($id);

        if (!Auth::user()->is_admin && $brand->user_id !== Auth::id()) {
            abort(403);
        }

        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $brand = MasterBrand::findOrFail($id);

        if (!Auth::user()->is_admin && $brand->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'code' => 'required|max:20|unique:master_brands,code,' . $brand->id,
            'name' => 'required|max:100',
            'status' => 'required|in:Active,Inactive',
        ]);

        $brand->update([
            'code' => $request->code,
            'name' => $request->name,
            'status' => $request->status,
        ]);


        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy($id)
    {
        $brand = MasterBrand::findOrFail($id);

        if (!Auth::user()->is_admin && $brand->user_id !== Auth::id()) {
            abort(403);
        }

        if ($brand->items()->count() > 0) {
            return redirect()->route('brands.index')->with('error', 'Cannot delete brand: it has linked items.');
        }

        $brand->delete();

        return redirect()->route('brands.index')->with('success', 'Brand deleted successfully.');
    }

    /**
     * Build brand query with the same filters you already have.
     */
    protected function filteredBrands(Request $request)
    {
        $q = MasterBrand::query();

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

    /**
     * Export to CSV
     */
    public function exportCsv(Request $request)
    {
        $brands = $this->filteredBrands($request)->get();
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="brands_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($brands) {
            $f = fopen('php://output', 'w');
            // header row
            fputcsv($f, ['ID', 'User ID', 'Code', 'Name', 'Status', 'Created At']);
            foreach ($brands as $b) {
                fputcsv($f, [
                    $b->id,
                    $b->user_id,
                    $b->code,
                    $b->name,
                    $b->status,
                    $b->created_at->toDateTimeString(),
                ]);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to Excel (XLSX)
     */
    public function exportXlsx(Request $request)
    {
        $brands = $this->filteredBrands($request)->get();
        $sheet = (new Spreadsheet())->getActiveSheet();

        // headings
        $sheet->fromArray(
            ['ID', 'User ID', 'Code', 'Name', 'Status', 'Created At'],
            null,
            'A1'
        );

        // data rows
        $row = 2;
        foreach ($brands as $b) {
            $sheet->fromArray([
                $b->id,
                $b->user_id,
                $b->code,
                $b->name,
                $b->status,
                $b->created_at->toDateTimeString(),
            ], null, 'A' . $row);
            $row++;
        }

        $writer = new Xlsx($sheet->getParent());
        $filename = 'brands_' . now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        $brands = $this->filteredBrands($request)->get();
        $pdf = Pdf::loadView('brands.export-pdf', compact('brands'));
        return $pdf->download('brands_' . now()->format('Ymd_His') . '.pdf');
    }
}
