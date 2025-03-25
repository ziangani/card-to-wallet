<div>
    <div class="mb-6">
        <h2 class="text-xl font-bold">Available Transactions</h2>
        <p class="text-gray-500">These transactions are available for cashout</p>

        <div class="mt-4 overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                            Reference
                        </th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Amount
                        </th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($this->availableTransactions as $transaction)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                {{ $transaction->merchant_reference }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">ZMW
                                {{ number_format($transaction->amount, 2) }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <th scope="row"
                            class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                            Available Balance
                        </th>
                        <td class="whitespace-nowrap px-3 py-3.5 text-sm font-semibold text-gray-900">ZMW
                            {{ number_format($this->availableBalance, 2) }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th scope="row"
                            class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                            Actual Balance (after charges)
                        </th>
                        <td class="whitespace-nowrap px-3 py-3.5 text-sm font-semibold text-gray-900">ZMW
                            {{ number_format($this->actualBalance, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <form wire:submit.prevent="initiateCashOut">
        {{ $this->form }}

        <div class="flex items-center justify-end gap-4">
            <button type="submit" class="btn-primary">
                Initiate Cash Out
            </button>
        </div>
    </form>
</div>
