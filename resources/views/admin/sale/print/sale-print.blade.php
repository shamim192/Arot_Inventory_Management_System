<style>
    @media print {
        .noprint {
            visibility: hidden;
        }
    }
</style>
<div style="width: 80mm; margin:0 auto; font-family: Arial;">
    <table border="0" cellpadding="0" cellspacing="0" style="width:100%; border-collapse: collapse;">
        <tr>
            <td>{!! printHeader('বিক্রয় রসিদ') !!}</td>
        </tr>
        <tr>
            <td style="border-top: 1px dashed #000;">&nbsp;</td>
        </tr>
        <tr>
            <td style="font-size:14px;">
                সূত্রঃ {{ enToBnNumber($data->id) }}<br>
                তাংঃ {{ enToBnNumber(dateFormat($data->date)) }}<br>
                নামঃ {{ $data->customer != null ? $data->customer->name : 'N/A'}}<br>
                মোবাইলঃ {{ $data->customer != null ? enToBnNumber($data->customer->mobile) : '-'}}
            </td>
        </tr>
        <tr>
            <td style="height:5px;"></td>
        </tr>
    </table>
    
    <table border="0" cellpadding="2" cellspacing="0" style="width:100%; border-collapse: collapse; font-size:12px;">
        <thead>
            <tr>
                <td style="font-weight: 600;">পণ্য</td>
                <td style="font-weight: 600; text-align: right;">পরিমাণ</td>
                <td style="font-weight: 600; text-align: right;">একক</td>
                <td style="font-weight: 600; text-align: right;">টাকার পরিমাণ</td>
            </tr>
            <tr>
                <td style="border-top: 1px dashed #000;" colspan="4"></td>
            </tr>
        </thead>
        <tbody>
            @foreach($data->items as $key => $item)
            <tr>
                <td>{{ $item->product != null ? $item->product->name : '-' }} {{ $item->unit != null ? '('.$item->unit->name.')' : '' }}</td>
                <td style="text-align: right;">{{ enToBnNumber($item->quantity) }}</td>
                <td style="text-align: right;">{{ enToBnNumber($item->unit_price) }}</td>
                <td style="text-align: right;">{{ enToBnNumber($item->amount) }}{{ env('CURRENCY') }}</td>
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td style="border-top: 1px dashed #000;" colspan="4"></td>
            </tr>
            <tr>
                <td style="font-weight: 600; font-size: 14px;" colspan="3">মোট দামঃ</td>
                <td style="font-weight: 600; text-align: right;">{{ enToBnNumber(numberFormat($data->items->sum('amount'))) }}{{ env('CURRENCY') }}</td>
            </tr>
            <tr>
                <td colspan="4" style="height:5px;"></td>
            </tr>

            @if($data->vat_amount > 0)
            <tr>
                <td style="font-weight: 600; font-size: 14px;" colspan="3"><strong>ভ্যাটঃ({{ enToBnNumber($data->vat_percent) }}%)</strong></td>
                <td style="font-weight: 600; text-align: right;">{{ enToBnNumber($data->vat_amount) }}{{ env('CURRENCY') }}</td>
            </tr>
            @endif

            @if($data->discount_amount > 0)
            <tr>
                <td style="font-weight: 600; font-size: 14px;" colspan="3"><strong>ছাড়ঃ</strong></td>
                <td style="font-weight: 600; text-align: right;">{{ enToBnNumber($data->discount_amount) }}{{ env('CURRENCY') }}</td>
            </tr>
            @endif
            
            <tr>
                <td colspan="4" style="height:5px;"></td>
            </tr>
            @if($data->vat_amount > 0 || $data->discount_amount > 0)
            <tr>
                <td style="font-weight: 600; font-size: 14px;" colspan="3"><strong>মোট</strong></td>
                <td style="font-weight: 600; font-size: 14px; text-align: right;">{{ enToBnNumber($data->total_amount) }}{{ env('CURRENCY') }}</td>
            </tr>
            @endif

            <tr>
                <td style="font-weight: 600; font-size: 14px;" colspan="3"><strong>জমা</strong></td>
                <td style="font-weight: 600; font-size: 14px; text-align: right;">{{ enToBnNumber($paidAmount) }}{{ env('CURRENCY') }}</td>
            </tr>
            <tr>
                <td style="border-top: 1px dashed #000;" colspan="4"></td>
            </tr>
            <tr>
                <td style="font-weight: 600; font-size: 14px;" colspan="3"><strong>বাকি</strong></td>
                <td style="font-weight: 600; font-size: 14px; text-align: right;">{{ enToBnNumber($data->total_amount-$paidAmount) }}{{ env('CURRENCY') }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; font-size: 14px;" colspan="3"><strong>জের</strong></td>
                <td style="font-weight: 600; font-size: 14px; text-align: right;">{{ enToBnNumber($reports->dueAmount-($data->total_amount-$paidAmount)) }}{{ env('CURRENCY') }}</td>
            </tr>
            <tr>
                <td style="border-top: 1px dashed #000;" colspan="4"></td>
            </tr>
            <tr>
                <td style="font-weight: 600; font-size: 14px;" colspan="3"><strong>মোট বাকি</strong></td>
                <td style="font-weight: 600; font-size: 14px; text-align: right;">{{ enToBnNumber($reports->dueAmount) }}{{ env('CURRENCY') }}</td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4">{!! printFooter() !!}</td>
            </tr>

            <tr>
                <td colspan="4" class="noprint" style="text-align: center;"><br><br><a href="{{ route('sale.index') }}">BACK TO LIST</a></td>
            </tr>
        </tfoot>
    </table>
</div>
<script> window.print(); </script>