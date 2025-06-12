<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ChartOfAccountsImport;
use Maatwebsite\Excel\Facades\Excel;

class AccountImportController extends Controller
{
    public function showForm()
    {
        return view('import-form');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        Excel::import(new ChartOfAccountsImport, $request->file('file'));

        return back()->with('success', 'Chart of accounts imported successfully.');
    }
}