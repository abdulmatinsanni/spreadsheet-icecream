<?php

namespace App\Imports;

use App\Enums\ImportStatus;
use App\Models\Contract;
use App\Models\Import;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ContractsImport implements
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithBatchInserts,
    ShouldQueue,
    WithEvents,
    WithUpserts
{
    protected Import $import;

    /**
     * Constructs class instance
     *
     * @param Import $import
     */
    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $contract = new Contract();

        $publicationDate = Date::excelToDateTimeObject($row['datapublicacao'])->format('Y-m-d');
        $celebrationDate = Date::excelToDateTimeObject($row['datacelebracaocontrato'])->format('Y-m-d');

        $contract->contract_id = $row['idcontrato'];
        $contract->announcement = $row['nanuncio'];
        $contract->contract_type = $row['tipocontrato'];
        $contract->procedure_type = $row['tipoprocedimento'];
        $contract->contract_object = $row['objectocontrato'];
        $contract->adjudicators = $row['adjudicantes'];
        $contract->contractors = $row['adjudicatarios'];
        $contract->publication_date = $publicationDate;
        $contract->celebration_date = $celebrationDate;
        $contract->contract_price = $row['precocontratual'];
        $contract->cpv = $row['cpv'];
        $contract->execution_term = $row['prazoexecucao'];
        $contract->execution_location = $row['localexecucao'];
        $contract->reasoning = $row['fundamentacao'];

        return $contract;
    }

    /**
     * Chunk size
     *
     * @return integer
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Batch size
     *
     * @return integer
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * Unique column for upserting records
     *
     * @return void
     */
    public function uniqueBy()
    {
        return 'contract_id';
    }

    /**
     * Register events
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows()['Folha1'] - 1;

                $this->import->total_rows = $totalRows;
                $this->import->save();
            },

            AfterImport::class => function (AfterImport $event) {
                $this->import->status = ImportStatus::COMPLETED;
                $this->import->save();
            },

            ImportFailed::class => function (ImportFailed $event) {
                $this->import->status = ImportStatus::FAILED;
                $this->import->save();
            },
        ];
    }
}
