<?php return [
    'domain' =>'https://domstroy.com.ua',
    /*
     * Наборы
     */
    'asset'=>'/themes/default/web',
    /*
     * Папки загрузок
     */
    'upload'=>'/public/uploads/category/',
    'src'=> '/public/uploads',
    'uploadsCompany'=>'company',
    /*
     * Страницы в соц сетях.
    */
    'social' => array(
        'fb' => 'https://www.facebook.com/domstroyua',
        'tw' => 'https://twitter.com/',
        'vk' => 'https://vk.com/publicdomstroy',
        'od' => 'https://www.ok.ru/group/54224603906175',
        'yt' => 'https://www.youtube.com/channel/UCkXeQb8IDjiVwcUBI2UKDzg',
        'g' => 'https://plus.google.com/+ДомСтройУкраина',
        'inst' => 'https://www.instagram.com/domstroy_ukraine/'
    ),
    'payments' => array(
        '1' => 'Безналичный расчет',
        '2' => 'Наложенный платеж',
        '3' => 'Наличными'
    ),
    'delivery' => array(
        '1' => 'Нова пошта',
        '2' => 'Самовывоз'
    ),
    'delivery_type' => array(
        '1' => 'В отделение',
        '2' => 'По адресу'
    ),
    'novaPoshtaApiKey' => '7eb61fb18c4683c7a12f4d3971bf9dc8',//bd934b9dc111d79e1300a0dfc07f6121
    'smtp-config' => array(
        'host' => 'smtp.yandex.com',
        'username' => 'info@domstroy.com.ua',
        'password' => 'domstroy+7!',
        'port' => '465',
        'secure' => 'ssl',
        //'debug' => '1',
    ),
    'order-status' => array(
        1 => 'Новый заказ',
        2 => 'В работе',
        3 => 'Выполнен',
        9 => 'Отменен',
    ),
    /*
     * Приложения входа через соц сети.
     * */
    'socialAuth' => array(
        'fb' => 'https://www.facebook.com/',
        'tw' => 'https://twitter.com/',
        'vk' => 'https://vk.com/',
        'od' => 'https://ok.ru/',
        'yt' => 'https://www.youtube.com/',
        'g' => 'https://plus.google.com/',
        'inst' => 'https://www.instagram.com/'
    ),
    'facebook' =>[
        'client_id' => '1796122033995575',
        'client_secret' => 'fc011b61904aaa8d18b024666c71761a',
    ],

    'main_email' =>'info@domstroy.com.ua',
    'show_settings' => 1,
    'show_team' => false,
    'closed' => 0,
    'phone-mask' => '+38(099)999-99-99',
    'phone-pattern' => '/^\+38\(\d{3}\)\d{3}-\d{2}-\d{2}$/',
    'email-mask' => '',
    'email-pattern' => '',
    'nominals' => [
        '1' => 1100,
        '3' => 3300,
        '4' => 2200
    ]
];
