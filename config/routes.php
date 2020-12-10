<?php
return [
    'index/load' =>         ['ControllerLoader',        'actionLoadSubcategoryList'],
    'index/search' =>       ['ControllerSearch',        'actionSearchImport'],
    'password/page' =>      ['ControllerPassword',      'passwordPage'],
    'password/request' =>   ['ControllerPassword',      'passwordRequest'],
    'password/confirm' =>   ['ControllerPassword',      'passwordConfirm'],
    'password/change' =>    ['ControllerPassword',      'passwordChange'],
    'user/auth' =>          ['ControllerAuthorization', 'actionUserAuthorization'],
    'user/exit' =>          ['ControllerAuthorization', 'actionUserSessionDestroy'],
    'user/search' =>        ['ControllerSearch',        'actionSearch'],
    'user/load' =>          ['ControllerLoader',        'actionUserLoad'],
    'user/chenge' =>        ['ControllerUser',          'chengeSwitcher'],
    'user/create' =>        ['ControllerUser',          'creationSwitcher'],
    'user/delet' =>         ['ControllerUser',          'deletionSwitcher'],
    'detailed/description' =>   ['ControllerUser',      'detailedPostInfo'],
    'admin/creat' =>        ['ControllerAdmin',         'creationSwitcher'],
    'admin/chenge' =>       ['ControllerAdmin',         'chengeSwitcher'],
    'admin/delet' =>        ['ControllerAdmin',         'deletionSwitcher'],
    '21232f297a57a5a743894a0e4a801fc3/ec4d1eb36b22d19728e9d1d23ca84d1c' => ['ControllerLoader', 'actionAdminLoad'],
    'feedback/message' =>   ['ControllerFeedback',      'sendMail']
];

