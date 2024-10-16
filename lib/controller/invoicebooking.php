<?

namespace Mywebstor\Invoicebooking\Controller;

use Bitrix\BIConnector\Rest;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\Integration\BizProc\Document\Invoice;
use Bitrix\Crm\ProductRowTable;
use Bitrix\Crm\Model\Dynamic\TypeTable;
use Bitrix\Rest\RestException;
use Mywebstor\Invoicebooking\InvoicebookingTable;

use function PHPUnit\Framework\isEmpty;

class Invoicebooking extends \IRestService
{
  const NAMESPACE = 'invoicebooking';

  public static $methods = array(
    self::NAMESPACE . '.get' => array(__CLASS__, 'getBooking'),
    self::NAMESPACE . '.set' => array(__CLASS__, 'setBooking'),
    self::NAMESPACE . '.delete' => array(__CLASS__, 'deleteBooking'),
    self::NAMESPACE . '.update' => array(__CLASS__, 'updateBooking'),
    self::NAMESPACE . '.getProductRow' => array(__CLASS__, 'getProductRow'),
    self::NAMESPACE . '.getDeal' => array(__CLASS__, 'getDeal'),
  );

  /**
   * smartProcessId is ENTITY_TYPE_ID in a b_crm_dynamic_type
   */
  public static function getBooking($query)
  {
    $itemId = $query['itemId'];
    $smartProcessId = $query['smartProcessId'];

    if (!isset($query['itemId']) || !isset($query['smartProcessId'])) return null;

    $entityTypeId = $smartProcessId;

    $symbolShortCode = \CCrmOwnerTypeAbbr::ResolveByTypeID($entityTypeId);

    $bookings = InvoicebookingTable::query()
      ->setSelect([
        '*',
        'DEAL_TITLE' => 'DEAL.TITLE',
        'DEAL_ASSIGNED_ID' => 'DEAL.ASSIGNED_BY_ID',
        // 'PRODUCT_NAME' => 'PRODUCT_ROW.PRODUCT_NAME',
      ])
      ->where('SP_ELEMENT_ID', $itemId)
      ->where('SP_ENTITY_TYPE_ID', $entityTypeId)
      ->fetchAll();

    $productRow = ProductRowTable::query()
      ->setSelect(['ID', 'PRODUCT_NAME', 'QUANTITY'])
      ->setOrder(['ID' => 'ASC'])
      ->where('OWNER_TYPE', $symbolShortCode)
      ->where('OWNER_ID', $query['itemId'])
      ->fetchAll();

    foreach ($productRow as &$row) {
      $row['BOOKING'] = [];
      foreach ($bookings as $booking) {
        if ($booking['PRODUCT_ROW_ID'] == $row['ID']) {
          $row['BOOKING'][] = $booking;
        }
      }
    }

    AddMessage2Log(array(
      "bookings" => $bookings,
      "productRow" => $productRow,
    ));

    return $productRow ?? [];
  }

  public static function setBooking($query)
  {
    if (empty($query['data']) || !is_array($query['data'])) return null;

    $query = $query['data'];

    // $newBooking = array(
    //   'SP_ENTITY_TYPE_ID' => $query['SP_ENTITY_TYPE_ID'],
    //   'SP_ELEMENT_ID' => $query['SP_ELEMENT_ID'],
    //   'PRODUCT_ROW_ID' => $query['PRODUCT_ROW_ID'],
    //   'DEAL_ID' => $query['DEAL_ID'],
    //   'AMOUNT' => $query['AMOUNT'],
    // );

    $result = InvoicebookingTable::add($query);

    if (!$result->isSuccess()) {
      throw new RestException('Бронирование не сохранено', 400);
      return 0;
    }

    return $result->getId();
  }

  public static function deleteBooking($query)
  {
    if (!isset($query['id'])) return null;
    
    $result = InvoicebookingTable::delete($query['id']);

    if (!$result->isSuccess()) {
      throw new RestException('Бронирование не удалено', 400);
      return false;
    }

    return true;
  }

  public static function updateBooking($books) {
    
    if (empty($books) || !is_array($books)) return null;

    foreach ($books as $book) {
      $result = InvoicebookingTable::update($book['ID'], $book);

      if (!$result->isSuccess()){
        throw new RestException('Бронирование не обновлено', 400);
        return false;
      }
    }
    
    return true;
  }

  public static function getProductRow($query)
  {
    if (!isset($query['ownerType']) || !isset($query['ownerId'])) return null;

    $ownerType = \CCrmOwnerTypeAbbr::ResolveByTypeID($query['ownerType']);

    $productRow = ProductRowTable::query()
      ->setSelect(['*'])
      ->setOrder(['ID' => 'ASC'])
      ->where('OWNER_TYPE', $ownerType)
      ->where('OWNER_ID', $query['ownerId'])
      ->fetchAll();

    if (!$productRow->isSuccess()) {
      throw new RestException('Не удалось получить данные', 400);
      return false;
    }

    return $productRow;
  }

  public static function getDeal($query)
  {

    $res = DealTable::query()->setSelect(['*']);

    // $res = $res->where('ID', 224971);

    if (isset($query['select']))
      $res = $res->setSelect($query['select']);

    if (isset($query['filter']))
      $res = $res->setFilter($query['filter']);

    if (isset($query['order']))
      $res = $res->setOrder($query['order']);

    if (isset($query['limit']))
      $res = $res->setLimit($query['limit']);

    $res = $res->fetchAll();

    return $res;
  }
}
