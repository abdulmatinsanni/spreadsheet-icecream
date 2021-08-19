<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    private Contract $contract;

    /**
     * Constructs class instance.
     *
     * @param Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $contracts = $this->contract
            ->when($request->has('celebration_date'), function ($query) use ($request) {
                $query->where('celebration_date', $request->celebration_date);
            })
            ->when(
                $request->has(['contract_price_from', 'contract_price_from']),
                function ($query) use ($request) {
                    $query->whereBetween('contract_price', [
                        $request->contract_price_from,
                        $request->contract_price_to
                    ]);
                }
            )
            ->when($request->has('winning_company'), function ($query) use ($request) {
                $query->where('adjudicators', 'LIKE',  "%{$request->winning_company}%");
            })
            ->paginate();

        return response()->json([
            'status' => true,
            'message' => 'Contracts fetched successfully.',
            'data' => $contracts
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $contract = $this->contract->find($id);

        if (!$contract) {
            return response()->json([
                'status' => false,
                'message' => 'Contract matching the provided id was not found',
            ], 400);
        }

        $contract->last_read_at = date('Y-m-d H:i:s');
        $contract->save();

        return response()->json([
            'status' => true,
            'message' => 'Contract fetched successfully.',
            'data' => $contract
        ]);
    }

    /**
     * Display the read status of the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showReadStatus(int $id)
    {
        $contract = $this->contract->find($id);

        if (!$contract) {
            return response()->json([
                'status' => false,
                'message' => 'Contract matching the provided id was not found',
            ], 400);
        }

        $data = [
            'id' => $contract->id,
            'contract_id' => $contract->contract_id,
            'last_read_at' => $contract->last_read_at ?? "N/A"
        ];

        return response()->json([
            'status' => true,
            'message' => 'Contract read status fetched successfully.',
            'data' => $data
        ]);
    }
}
