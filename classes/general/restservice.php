<?

use Mywebstor\Invoicebooking\Controller\Invoicebooking;

class CInvoicebookingRestService extends \IRestService
{
  public static function onRestServiceBuildDescription()
  {
    AddMessage2Log('CInvoicebookingRestService');

    return array(
      // "mwssiteorders" => array(
      //   "mwssiteorders.getSettings" => array(__CLASS__, 'getSettings'),
      //   "mwssiteorders.setSettings" => array(__CLASS__, 'setSettings'),
      // ),


      // 'invoicebooking' => array(
      //   'callback' => array(
      //     Invoicebooking::class,
      //     'getBooking'
      //   ),
      // ),

      "invoicebooking" => Invoicebooking::$methods,
    );
  }
}
