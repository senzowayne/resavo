<?php

namespace App\Service\Paypal;

use Sample\PayPalClient;
use PayPalCheckoutSdk\Payments\AuthorizationsCaptureRequest;

class CaptureAuthorization
{

  // 2. Set up your server to receive a call from the client
  /**
   *Use the following function to capture Authorization.
   *Pass a valid authorization ID as an argument.
   */
  public static function captureAuth($authorizationId, $debug=false)
  {
    $request = new AuthorizationsCaptureRequest($authorizationId);
    $request->body = self::buildRequestBody();
    // 3. Call PayPal to capture an authorization.
    $client = PayPalClient::client();
    $response = $client->execute($request);
    // 4. Save the capture ID to your database for future reference.
    if ($debug)
    {
      print "Status Code: {$response->statusCode}\n";
      print "Status: {$response->result->status}\n";
      print "Capture ID: {$response->result->id}\n";
      print "Links:\n";
      foreach($response->result->links as $link)
      {
        print "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";
      }
      // To toggle printing the whole response body comment/uncomment
      // the follwowing line
      echo json_encode($response->result, JSON_PRETTY_PRINT), "\n";
    }
    return $response;
  }

   /**
   *You can use the following method to build the capture request body.
   *Refer to the Payments API reference for more information.
   */
  public static function buildRequestBody()
  {
    return "{}";
  }
}

  if (!count(debug_backtrace()))
  {
    CaptureAuthorization::captureAuth('REPLACE-WITH-VALID-APPROVED-AUTH-ID', true);
  }
