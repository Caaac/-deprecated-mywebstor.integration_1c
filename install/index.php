<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Loader;
use Bitrix\Main\UrlRewriter;


Loader::includeModule("crm");
Loc::loadMessages(__FILE__);
IncludeModuleLangFile(__FILE__);

class mywebstor_integration_1c extends CModule
{
    public $MODULE_ID = "mywebstor.integration_1c";
    public $errors = '';
    static $events = array(
        array(
            "FROM_MODULE" => "rest",
            "FROM_EVENT" => "onRestServiceBuildDescription",
            "TO_CLASS" => "CInvoicebookingRestService",
            "TO_FUNCTION" => "onRestServiceBuildDescription",
            "VERSION" => "1"
        ),
        // array(
        // "FROM_MODULE" => "main",
        // "FROM_EVENT" => "OnEpilog",
        // "TO_CLASS" => "CMedMenteEvents",
        // "TO_FUNCTION" => "showSalaryBtn",
        // "VERSION" => "1"
        // ),
        // array(
        //     "FROM_MODULE" => "main",
        //     "FROM_EVENT" => "OnEpilog",
        //     "TO_CLASS" => "CMedMenteEvents",
        //     "TO_FUNCTION" => "iframeSize",
        //     "VERSION" => "1"
        // ),
    );

    public function __construct()
    {
        if (file_exists(__DIR__ . "/version.php")) {

            $arModuleVersion = array();

            include_once(__DIR__ . "/version.php");
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            $this->MODULE_NAME = Loc::getMessage("MYWEBSTOR_MODULE_NAME");
            $this->MODULE_DESCRIPTION = Loc::getMessage("MYWEBSTOR_MODULE_DESCRIPTION");
            $this->PARTNER_NAME = Loc::getMessage("MYWEBSTOR_MODULE_PARTNER_NAME");
            $this->PARTNER_URI = Loc::getMessage("MYWEBSTOR_MODULE_PARTNER_URI");
        }
        return true;
    }



    public function DoInstall()
    {
        if (!check_bitrix_sessid())
            return false;

        ModuleManager::registerModule($this->MODULE_ID);
        // $this->InstallDB();
        $this->InstallEvents();

        return true;
    }

    public function DoUninstall()
    {
        // $this->UnInstallDB();
        $this->UnInstallEvents();
        ModuleManager::unRegisterModule($this->MODULE_ID);


        // if (!check_bitrix_sessid() || !IsModuleInstalled("iblock"))
        //     return false;

        // global $APPLICATION, $USER, $DB, $step;
        // $step = intval($step);
        // if ($step < 2) {
        //     $APPLICATION->IncludeAdminFile(Loc::getMessage("MYWEBSTOR_UNINSTALL_TITLE", array("#MODULE_NAME#" => $this->MODULE_NAME)), __DIR__ . "/unstep1.php");
        // } elseif ($step === 2) {

        //     if (!array_key_exists('savedata', $_REQUEST) || $_REQUEST['savedata'] != 'Y') {
        //         $this->UnInstallDB();
        //     }

        //     $this->UnInstallEvents();

        //     ModuleManager::unRegisterModule($this->MODULE_ID);

        //     return true;
        // }

        // return true;
    }

    function InstallDB()
    {
        global $DB, $APPLICATION;
        $this->errors = $DB->RunSQLBatch(__DIR__ . '/db/install.sql');
        if (is_array($this->errors)) {
            $APPLICATION->ThrowException(implode('<br />', $this->errors));
            return false;
        }
        return true;
    }

    function UnInstallDB()
    {
        global $DB, $APPLICATION;
        $this->errors = $DB->RunSQLBatch(__DIR__ . '/db/uninstall.sql');
        if (is_array($this->errors)) {
            $APPLICATION->ThrowException(implode('<br />', $this->errors));
            return false;
        }

        return true;
    }

    public function InstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        foreach (static::$events as $event) 
            $eventManager->registerEventHandlerCompatible($event["FROM_MODULE"], $event["FROM_EVENT"], $this->MODULE_ID, $event["TO_CLASS"], $event["TO_FUNCTION"]);
        return true;
    }

    public function UnInstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        foreach (static::$events as $event)
            $eventManager->unRegisterEventHandler($event["FROM_MODULE"], $event["FROM_EVENT"], $this->MODULE_ID, $event["TO_CLASS"], $event["TO_FUNCTION"]);
        return true;
    }
}
