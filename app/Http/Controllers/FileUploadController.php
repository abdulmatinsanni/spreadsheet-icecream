<?php

namespace App\Http\Controllers;

use App\Enums\ImportStatus;
use App\Http\Requests\FileUploadStoreRequest;
use App\Imports\ContractsImport;
use App\Models\Import;
use Maatwebsite\Excel\Facades\Excel;

class FileUploadController extends Controller
{
    private Import $import;

    /**
     * Constructs class instance.
     *
     * @param Import $import
     * @return \Illuminate\Http\Response
     */
    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $imports = $this->import->latest()->get();

        return response()->json([
            'message' => 'Imports record fetched successfully.',
            'data' => $imports
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FileUploadStoreRequest $request)
    {
        $import = new $this->import();
        $import->filename = $request->file('file')->getClientOriginalName();
        $import->path = $request->file('file')->store('imports');
        $import->status = ImportStatus::PENDING;
        $import->save();

        Excel::import(new ContractsImport($import), $import->path);

        return response()->json(['message' => 'File uploaded successfully.']);
    }
}
