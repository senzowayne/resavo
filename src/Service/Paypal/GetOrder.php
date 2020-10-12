<?php

namespace App\Service\Paypal;

use Sample\PayPalClient;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

class GetOrder
{

    // 2. Set up your server to receive a call from the client
    /**
     *You can use this function to retrieve an order by passing order ID as an argument.
     */
    public static function getOrder($orderId)
    {


        // 3. Call PayPal to get the transaction details
        $client = PayPalClient::client();
        $response = $client->execute(new OrdersGetRequest($orderId));
        /**
         *Enable the following line to print complete response as JSON.
         */
        //print json_encode($response->result);
        print "Status Code: {$response->statusCode}\n";
        print "Status: {$response->result->status}\n";
        print "Order ID: {$response->result->id}\n";
        print "Intent: {$response->result->intent}\n";
        print "Links:\n";
        foreach ($response->result->links as $link) {
            print "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";
        }
        // 4. Save the transaction in your database. Implement logic to save transaction to your database for future reference.
        print "Gross Amount: {$response->result->purchase_units[0]->amount->currency_code} {$response->result->purchase_units[0]->amount->value}\n";

        // To print the whole response body, uncomment the following line
        // print json_encode($response->result, JSON_PRETTY_PRINT);
        return ['status' => $response->result->status, 'statusCode' => $response->statusCode, 'orderID' => $response->result->id, 'value' => $response->result->purchase_units[0]->amount->value, 'currency' => $response->result->purchase_units[0]->amount->currency_code, 'mail' => $response->result->payer->email_address ];
    }
}

/**
 *This is the driver function that invokes the getOrder function to retrieve
 *sample order details.
 *
 *To get the correct order ID, this sample uses createOrder to create a new order
 *and then uses the newly-created order ID with GetOrder.
 */
if (!count(debug_backtrace())) {
    GetOrder::getOrder('REPLACE-WITH-ORDER-ID', true);
}
