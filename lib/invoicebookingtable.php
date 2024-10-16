<?

namespace Mywebstor\Invoicebooking;

use Bitrix\Crm\CompanyTable;
use Bitrix\Crm\Controller\Item\ProductRow;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\Model\Dynamic\TypeTable;
use Bitrix\Crm\ProductRowTable;
use Bitrix\Main\DB\SqlExpression;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\Entity\Query\Join;
use Bitrix\Main\UserTable;
use Sale\Handlers\Delivery\Additional\DeliveryRequests\RusPost\Reference;

Loc::loadMessages(__FILE__);

class InvoicebookingTable extends DataManager
{
  public static function getTableName()
  {
    return 'mws_invoicebooking';
  }

  public static function getMap()
  {
    return array(
      /**
       * Define mws_invoicebooking table 
       */
      'ID' => (new IntegerField('ID'))
        ->configurePrimary()
        ->configureAutocomplete()
        ->configureDefaultValue(0)
        ->configureTitle(Loc::getMessage('ID_FIELD')),
      'SP_ENTITY_TYPE_ID' => (new IntegerField('SP_ENTITY_TYPE_ID'))
        ->configureRequired()
        ->configureTitle(Loc::getMessage('SP_ENTITY_TYPE_ID_FIELD')),
      'SP_ELEMENT_ID' => (new IntegerField('SP_ELEMENT_ID'))
        ->configureRequired()
        ->configureTitle(Loc::getMessage('SP_ELEMENT_ID_FIELD')),
      'PRODUCT_ROW_ID' => (new IntegerField('PRODUCT_ROW_ID'))
        ->configureRequired()
        ->configureTitle(Loc::getMessage('PRODUCT_ROW_ID_FIELD')),
      'DEAL_ID' => (new IntegerField('DEAL_ID'))
        ->configureRequired()
        ->configureTitle(Loc::getMessage('DEAL_ID_FIELD')),
      'BOOKED_BY' => (new IntegerField('BOOKED_BY'))
        ->configureDefaultValue(null)
        ->configureTitle(Loc::getMessage('BOOKED_BY_FIELD')),
      'AMOUNT' => (new IntegerField('AMOUNT'))
        ->configureTitle(Loc::getMessage('AMOUNT_FIELD')),

      /**
       * ReferenceField
       */
      'SMART_PROCESS' => (new ReferenceField(
        'SMART_PROCESS_ENTITY_TYPE_ID',
        TypeTable::getEntity(),
        Join::on('this.SP_ENTITY_TYPE_ID', 'ref.ENTITY_TYPE_ID')
      )),
      
      'PRODUCT_ROW' => (new ReferenceField(
        'PRODUCT_ROW',
        ProductRowTable::getEntity(),
        Join::on('this.PRODUCT_ROW_ID', 'ref.ID')
      )),

      'DEAL' => (new ReferenceField(
        'DEAL',
        DealTable::getEntity(),
        Join::on('this.DEAL_ID', 'ref.ID')
      )),

      'COMPANY' => (new ReferenceField(
        'COMPANY',
        CompanyTable::getEntity(),
        Join::on('this.DEAL.COMPANY_ID', 'ref.ID')
      )),

      'BOOKED_BY_USER' => (new ReferenceField(
        'BOOKED_BY_USER',
        UserTable::getEntity(),
        Join::on('this.BOOKED_BY', 'ref.ID')
      ))

      // 'DEAL_UF' => (new ReferenceField(
      //   'DEAL_UF',
      //   DealTable::getEntity(),
      //   Join::on('this.DEAL_ID', 'ref.ID')
      // ))

      // 'SMART_PROCESS_SYMBOL_CODE_SHORT' => (new ExpressionField(
      //   'SMART_PROCESS_SYMBOL_CODE_SHORT',
      //   (
      //     new SqlExpression('%s')
      //   ),
      //   array('SMART_PROCESS.ENTITY_TYPE_ID')
      // )),
    );
  }
}
