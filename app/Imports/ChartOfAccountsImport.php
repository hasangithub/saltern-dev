<?php

namespace App\Imports;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\SubAccount;
use App\Models\Ledger;
use App\Models\SubAccountGroup;
use App\Models\SubLedger;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ChartOfAccountsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $row = array_values($row->toArray()); // force numeric index
            $accountName = $row[0] ?? null;
            $subAccountName = $row[1] ?? null;
            $ledgerName = $row[2] ?? null;
            $subLedgerName = $row[3] ?? null;
            if(empty($ledgerName)) continue;
            $account = AccountGroup::firstOrCreate(['name' => $accountName]);

            $subAccount = null;
            if (!empty($subAccountName)) {
                $subAccount = SubAccountGroup::firstOrCreate([
                    'name' => $subAccountName,
                    'account_group_id' => $account->id,
                ]);
            }

            $ledger = Ledger::firstOrCreate([
                'name' => $ledgerName,
                'sub_account_group_id' => $subAccount ? $subAccount->id : null,
            ]);

            if (!empty($subLedgerName)) {
                SubLedger::firstOrCreate([
                    'name' => $subLedgerName,
                    'ledger_id' => $ledger->id,
                ]);
            }
        }
    }
}
