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
            <td>{!! printHeader('কাস্টমার পেমেন্ট রসিদ') !!}</td>
        </tr>
        <tr>
            <td style="border-top: 1px dashed #000;">&nbsp;</td>
        </tr>
        <tr>
            <td style="height:5px;"></td>
        </tr>
    </table>
    
    <table border="0" cellpadding="2" cellspacing="0" style="width:100%; border-collapse: collapse; font-size:14px;">
        <tbody>
            <tr>
                <td style="font-weight: normal;">সূত্রঃ</td>
                <td style="font-weight: normal; text-align: right;">{{ enToBnNumber($data->id) }}</td>
            </tr>
            <tr>
                <td style="font-weight: normal;">ধরনঃ</td>
                <td style="font-weight: normal; text-align: right;">{{ $data->type }}</td>
            </tr>
            <tr>
                <td style="font-weight: normal;">তাংঃ</td>
                <td style="font-weight: normal; text-align: right;">{{ enToBnNumber(dateFormat($data->date)) }}</td>
            </tr>
            <tr>
                <td style="font-weight: normal;">ব্যাংকঃ</td>
                <td style="font-weight: normal; text-align: right;">{{ $data->bank != null ? $data->bank->name : 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight: normal;">নামঃ</td>
                <td style="font-weight: normal; text-align: right;">{{ $data->customer != null ? $data->customer->name : 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight: normal;">মোবাইলঃ</td>
                <td style="font-weight: normal; text-align: right;">{{ $data->customer != null ? enToBnNumber($data->customer->mobile) : '-' }}</td>
            </tr>
        </tbody>

        <tfoot>
            <tr>
                <td style="border-top: 1px dashed #000;" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td style="font-weight: 600; font-size: 14px;">জমা</td>
                <td style="font-weight: 600; font-size: 14px; text-align: right;">{{ enToBnNumber($data->amount) }}{{ env('CURRENCY') }}</td>
            </tr>
            <tr>
                <td style="border-top: 1px dashed #000;" colspan="2"></td>
            </tr>
            <tr>
                <td style="font-weight: 600; font-size: 14px;"><strong>মোট বাকি</strong></td>
                <td style="font-weight: 600; font-size: 14px; text-align: right;">{{ enToBnNumber($reports->dueAmount) }}{{ env('CURRENCY') }}</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">{!! printFooter() !!}</td>
            </tr>

            <tr>
                <td colspan="2" class="noprint" style="text-align: center;"><br><br><a href="{{ route('customer-payment.index') }}">BACK TO LIST</a></td>
            </tr>
        </tfoot>
    </table>
</div>
<script> window.print(); </script>