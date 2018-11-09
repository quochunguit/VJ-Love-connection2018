<?php

namespace Setting\Front\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Setting\Front\Service\SettingService;

class Setting extends AbstractHelper {

    protected $setingService;

    public function getSettingService() {
        return $this->settingService;
    }

    public function setSettingService(SettingService $settingService) {
        $this->settingService = $settingService;
    }

    public function __invoke($identity) {
        $blocks = $this->getSettingService()->getByIdentity($identity);
        return $blocks;
    }

}

