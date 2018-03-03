
<div id="client_invoices" name="client_invoices" style="background-color: #fff;width:auto;">
    <div class="row">
        <div class="col-md-12" >
            <!-- IF THIS IS THE DEFAULT FRESHBOOKS ACCOUNT -->
            @if(!$provider->is_default)
                <a style="font-weight:bold;float:right;" class="no-print" href="{{ $client->fb_invoice['invoice']['links']['edit'] }}" target="_blank">
                    Edit Invoice In Freshbooks</a>
                <h3>QuikFiles Invoice</h3>
        @endif


            <script type='text/javascript' src='{{ asset('js/invoice.js?v=2') }}'></script>


            <div id="invoice-page-wrap" >
                <!--   MAKE INVOICE  -->
                <link rel='stylesheet' type='text/css' href='{{ asset('css/invoice-style.css') }}' />
                <link rel='stylesheet' type='text/css' href='{{ asset('css/invoice-print.css') }}' media="print" />

                <div style="clear:both"></div>

                <div id="customer" >

                    <table id="meta" >
                        <tr>
                            <td class="meta-head totals">Invoice #</td>
                            <td class="meta-head totals" style="background-color:#fff; text-align:right;">{{$client->fb_invoice['invoice']['number']}}</td>
                        </tr>
                        <tr>
                            <td class="meta-head totals">PO #</td>
                            <td class="meta-head totals" style="background-color:#fff; text-align:right;">
                                <textarea name="custom_invoice_po">{{is_array($client->fb_invoice['invoice']['po_number']) ? implode(',',$client->fb_invoice['invoice']['po_number']) : $client->fb_invoice['invoice']['po_number']}}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="meta-head totals">Amount Due</td>
                            <td class="meta-head totals" style="background-color:#fff;text-align:right;"><div class="due">${{$client->fb_invoice['invoice']['amount_outstanding']}}</div></td>
                        </tr>

                    </table>

                </div>

                <table id="items">

                    <tr>
                        <th style="width:150px;">Name</th>
                        <th>Description</th>
                        <th style="width:80px;">Unit Cost</th>
                        <th style="width:80px;">Quantity</th>
                        <th style="width:80px;" nowrap>Tax %</th>
                        <th style="width:80px;" class="text-right">Line Total</th>
                    </tr>

                    <?php
                    $invoice_options_r = Array('Plan',
                            'Weight',
                            'Has Pacemaker',
                            'Plan For Cremation Remains',
                            'Certificate Shipping',
                            'Certificates',
                            'Filing Fee',
                            'Plaque Certificate',
                            'Urn'
                    );
                    $found = false;
                    ?>

                    {{--{{ '<pre>'.dd($client->fb_invoice['invoice']['lines']) }}--}}
                    @foreach( $client->fb_invoice['invoice']['lines']['line'] as $key=> $item )
                        <?php
                        if(is_array($item['name']))$item['name'] = implode(' ',$item['name']);
                        if(is_array($item['description']))$item['description'] = implode(' ',$item['description']);
                        if(is_array($item['unit_cost']))$item['unit_cost'] = implode(' ',$item['unit_cost']);
                        if(is_array($item['quantity']))$item['quantity'] = implode(' ',$item['quantity']);
                        if(is_array($item['amount']))$item['amount'] = implode(' ',$item['amount']);
                        if(is_array($item['tax1_percent']))$item['tax1_percent'] = implode(' ',$item['tax1_percent']);
                        ?>
                        <tr class="item-row">
                            <td class="item-name"><div class="delete-wpr">
                                    <textarea name="custom_invoice[{{$key}}][name]">{{$item['name']}}</textarea>

                                    <a class="delete" href="javascript:;" title="Remove row">X</a></div>
                            </td>
                            <td class="description"><textarea name="custom_invoice[{{$key}}][description]">{{$item['description']}}</textarea></td>
                            <td><textarea class="cost" name="custom_invoice[{{$key}}][unit_cost]">${{$item['unit_cost']}}</textarea></td>
                            <td><textarea class="qty" name="custom_invoice[{{$key}}][quantity]">{{$item['quantity']}}</textarea></td>
                            <td><textarea class="tax1_percent" name="custom_invoice[{{$key}}][tax1_percent]">{{$item['tax1_percent']}}</textarea></td>
                            <td class="balance"><span class="price">${{$item['amount']}}</span></td>
                        </tr>

                    @endforeach


                    <tr id="hiderow" style="border-bottom: none;">
                        <td colspan="6" style="border-bottom: none;"><a id="addrow" href="javascript:;" title="Add a row">Add a row</a></td>
                    </tr>
                    <tr>

                        <td colspan="3" class="blank totals" style="border-top:none;"> </td>
                        <td colspan="2" class="total-line totals">Total</td>
                        <td class="total-value totals"><div id="total">${{$client->fb_invoice['invoice']['amount']}}</div></td>
                    </tr>
                    <tr style="display:none;">
                        <td colspan="3" class="blank totals"> </td>
                        <td colspan="2" class="total-line totals">Amount Paid</td>

                        <td class="total-value totals"><textarea id="paid">${{$client->fb_invoice['invoice']['paid']}}</textarea></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="blank totals"> </td>
                        <td colspan="2" class="total-line balance totals">Balance Due</td>
                        <td class="total-value balance totals"><div class="due">${{$client->fb_invoice['invoice']['amount_outstanding']}}</div></td>
                    </tr>

                </table>
                <!--*Charges are only for those items that you selected or that are required. If we are required by law or by a cemetery or crematory to use any items, we will explain the reasons in writing below.
                *We charge you for our services in obtaining: Cash Advance Items such as; Coroners or Medical Examiner Release Fees.

                This facility is licensed and regulated by the Oregon Mortuary And Cemetery Board (971) 673-1500

                Print:__________________________

                Signature:____________________________

                Date:_____________________-->
                <hr style="page-break-after:always;border: none;" />
                <div class="row">
                    <div id="terms" class="col-xs-6 pull-left" style=";">
                        <h5>Terms</h5>
                        <textarea id="terms_text" name="custom_invoice_email[fb_invoice_terms]" style="height:250px;overflow-y:auto;border:1px solid #aaa;text-align:left;padding:2px;">{{($client->fb_invoice_terms!=''?$client->fb_invoice_terms:$provider->freshbooks_terms)}}</textarea>
                    </div>
                    <div id="terms" class="col-xs-6 pull-right" style="">
                        <h5>Notes</h5>
                        <textarea name="custom_invoice_email[fb_invoice_notes]" style="height:250px;overflow-y:auto;border:1px solid #aaa;text-align:left;padding:2px;">{{$client->fb_invoice_notes}}</textarea>
                    </div>
                </div>
                <div class="row">
                    <br />
                    <button class="pull-right no-print" type="submit" name="save_invoice" value="submit">Save Invoice</button>

                    <button class="pull-right no-print" type="button" name="print_invoice" value="Print" onclick="PrintElem('#client_invoices')" style="margin-right:15px;">Print Invoice</button>
                </div>
            </div>



            <script>
                function PrintElem(elem)
                {
                    printDiv($('<div/>').append($(elem).clone()).html());
                }

                function printDiv(divName) {
                    var mywindow = window.open('', 'new div', 'height=400,width=800');
                    var html = '';
                    //html += "<script src='https://code.jquery.com/jquery-2.2.4.min.js' > <\/script>";
                    html += "<link rel=\"stylesheet\" href=\"{{ asset('css/invoice-style.css') }}\" type=\"text/css\" />";
                    html += "<link rel=\"stylesheet\" href=\"{{ asset('css/invoice-print.css') }}\" type=\"text/css\" />";
                    //html += "<a href='#' onclick='window.print();' class='no-print'>Print<\/a>";
                    html += divName;
                    html += '';


                    mywindow.document.write(html);

                    setTimeout(function(){
                        //do what you need here
                        mywindow.print();
                        mywindow.close();
                    }, 1000);


                    return true;
                }

            </script>

            <br class="no-print" /><br class="no-print" /><br  class="no-print" />
            <hr class="no-print">
            <div class="no-print">
                <b>Email Message</b><br /><br />
                <div style="margin-left:25px;">
                    <label><b>To:</b></label>
                    <input type="text" name="custom_invoice_email[fb_invoice_email]" style="border:1px solid #bbb;padding:2px;" placeholder="Email Subject"
                           value="{{($client->fb_invoice_email!=''?$client->fb_invoice_email:$client->User->email)}}" />
                    <Br />
                    <label><b>Summary:</b></label>
                <!--<input type="text" name="email_to" value="{{$client->User->email}}" placeholder="Email To" />-->
                    <input type="text" name="invoice_subject" style="border:1px solid #bbb;padding:2px;"  placeholder="Email Subject" value="New invoice {{trim($client->fb_invoice['invoice']['invoice_id'],'0')}} from {{$provider->business_name}}, sent using QuikFiles" /><br />

                    <label><b>Message:</b></label>

                    @if(!$provider->is_default)
                        <textarea name="invoice_message" type="text" style="border:1px solid #bbb;padding:2px;"  placeholder="Email Message">To view your invoice from {{$provider->business_name}} for ${{$client->fb_invoice['invoice']['amount_outstanding']}}, or to download a PDF copy for your records, click the link below:

                            {{$client->fb_invoice['invoice']['links']['client_view']}}

                            or copy and paste the following URL into your browser: {{$client->fb_invoice['invoice']['links']['client_view']}}
                                            </textarea>
                    @else
                        <textarea name="invoice_message" type="text" style="border:1px solid #bbb;padding:2px;"  placeholder="Email Message">Attached is your invoice from {{$provider->business_name}} for ${{$client->fb_invoice['invoice']['amount_outstanding']}}
                                            </textarea>
                    @endif


                    <?php
                    $domain = str_replace('https://', '', $provider->freshbooks_api_url);
                    $domain = substr($domain, 0, strpos($domain, '.freshbooks.com'));

                    ?>
                    <br />


                    Best regards,<br />
                    {{$provider->business_name}} ({{$provider->email}})<br />
                </div>
                <hr><br />
                @if($provider->is_default)
                    <input class="pull-right btn" type="submit" name="send_invoice_pdf" value="Send Invoice PDF" />
                @else
                    <input class="pull-right btn" type="submit" name="send_invoice" value="Send Invoice" />
                @endif

            </div>
        </div>
    </div>

</div>