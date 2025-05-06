<?php

namespace App\Http\Controllers;

use App\Models\MasterItem;
use App\Models\MasterBrand;
use App\Models\MasterCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;


class MasterItemController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterItem::with(['brand', 'category']);

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

        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user_id if admin
        if (Auth::user()->is_admin && $request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(5);
        $brands = MasterBrand::orderBy('name')->get();
        $categories = MasterCategory::orderBy('name')->get();

        return view('items.index', compact('items', 'brands', 'categories'))
            ->with('search', $request->search)
            ->with('status', $request->status)
            ->with('brand_id', $request->brand_id)
            ->with('category_id', $request->category_id)
            ->with('user_id', $request->user_id);
    }


    public function create()
    {
        $brands = MasterBrand::where('status', 'Active')->get();
        $categories = MasterCategory::where('status', 'Active')->get();
        return view('items.create', compact('brands', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:master_items,code|max:20',
            'name' => 'required|max:100',
            'brand_id' => 'required|exists:master_brands,id',
            'category_id' => 'required|exists:master_categories,id',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

        ]);

        $filePath = null;
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('attachments', 'public');
        }

        MasterItem::create([
            'user_id' => Auth::id(),
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'code' => $request->code,
            'name' => $request->name,
            'attachment' => $filePath,
            'status' => 'Active',
        ]);

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    public function edit($id)
    {
        $item = MasterItem::findOrFail($id);

        if (!Auth::user()->is_admin && $item->user_id !== Auth::id()) {
            abort(403);
        }

        $brands = MasterBrand::where('status', 'Active')->get();
        $categories = MasterCategory::where('status', 'Active')->get();

        return view('items.edit', compact('item', 'brands', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $item = MasterItem::findOrFail($id);

        if (!Auth::user()->is_admin && $item->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'code' => 'required|max:20|unique:master_items,code,' . $item->id,
            'name' => 'required|max:100',
            'brand_id' => 'required|exists:master_brands,id',
            'category_id' => 'required|exists:master_categories,id',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required|in:Active,Inactive',
        ]);

        // If new file is uploaded
        if ($request->hasFile('attachment')) {
            if ($item->attachment) {
                Storage::disk('public')->delete($item->attachment);
            }
            $item->attachment = $request->file('attachment')->store('attachments', 'public');
        }

        $item->update([
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'code' => $request->code,
            'name' => $request->name,
            'attachment' => $item->attachment,
            'status' => $request->status,
        ]);

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy($id)
    {
        $item = MasterItem::findOrFail($id);

        if (!Auth::user()->is_admin && $item->user_id !== Auth::id()) {
            abort(403);
        }

        if ($item->attachment) {
            Storage::disk('public')->delete($item->attachment);
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }

    /** 
     * Shared: build query with all current filters
     */
    protected function filteredItems(Request $request)
    {
        $q = MasterItem::with(['brand', 'category']);
        if (!Auth::user()->is_admin) {
            $q->where('user_id', Auth::id());
        }
        if ($s = $request->search)      $q->where(fn($q) => $q->where('code', 'like', "%$s%")->orWhere('name', 'like', "%$s%"));
        if ($b = $request->brand_id)    $q->where('brand_id', $b);
        if ($c = $request->category_id) $q->where('category_id', $c);
        if ($st = $request->status)      $q->where('status', $st);
        if (Auth::user()->is_admin && $u = $request->user_id) $q->where('user_id', $u);
        return $q->orderBy('created_at', 'desc');
    }

    /**
     * CSV Export
     */
    public function exportCsv(Request $request)
    {
        $items = $this->filteredItems($request)->get();
        $columns = ['ID', 'User ID', 'Code', 'Name', 'Brand', 'Category', 'Status', 'Created At'];

        $filename = 'items_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($items, $columns) {
            $f = fopen('php://output', 'w');
            fputcsv($f, $columns);
            foreach ($items as $i) {
                fputcsv($f, [
                    $i->id,
                    $i->user_id,
                    $i->code,
                    $i->name,
                    $i->brand->name,
                    $i->category->name,
                    $i->status,
                    $i->created_at->toDateTimeString(),
                ]);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * XLSX Export
     */
    public function exportXlsx(Request $request)
    {
        $items = $this->filteredItems($request)->get();
        $columns = ['ID', 'User ID', 'Code', 'Name', 'Brand', 'Category', 'Status', 'Created At'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($columns, null, 'A1');

        $row = 2;
        foreach ($items as $i) {
            $sheet->fromArray([
                $i->id,
                $i->user_id,
                $i->code,
                $i->name,
                $i->brand->name,
                $i->category->name,
                $i->status,
                $i->created_at->toDateTimeString(),
            ], null, 'A' . $row);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'items_' . now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * PDF Export
     */
    public function exportPdf(Request $request)
    {
        $items = $this->filteredItems($request)->get();
        $pdf = Pdf::loadView('items.export-pdf', compact('items'));
        return $pdf->download('items_' . now()->format('Ymd_His') . '.pdf');
    }
}
