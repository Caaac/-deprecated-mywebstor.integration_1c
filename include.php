<?
/**
 * Classes loader
 */

use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses(
  'mywebstor.invoicebooking',
  array(
    'Mywebstor\Invoicebooking\Controller\Invoicebooking' => 'lib/controller/invoicebooking.php',

    'CInvoicebookingRestService' => 'classes/general/restservice.php',
  )
);

$modules = array(
  'crm',
);

foreach ($modules as $module) {
  if (!Loader::includeModule($module)) {
    ShowError("Module \"{$module}\" not found.");
    return false;
  }
}
